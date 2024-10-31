<?php

namespace App\Controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use econea\nusoap_client;
use App\Controllers\BaseController;
use App\Models\ReferralModel;

class ReferralWSController extends BaseController
{
	private $jwtSecret = 'sicituradastra_1412'; // JWT secret key
    private $tokenValidity = 3600;          // Token validity in seconds (1 hour)
    private $refreshTokenValidity = 86400;  // Refresh token validity (1 day)
    private $rateLimitRequests = 100;       // Max requests per hour
    private $pdo;

	function __construct() {

		$this->Model = new ReferralModel();
		$ep= base_url()."/referral/wsdl";
		$server =$this->server = new \nusoap_server();
		$this->server->configureWSDL('homis',$ep,$ep);
		$this->server->wsdl->schemaTargetNamespace = $ep;  
		 // Register all services
		 $this->registerServices($ep);

		 $response = service('response');
	}

	private function generateJWT($userId)
    {
        $issuedAt = time();
        $expiry = $issuedAt + $this->tokenValidity;

        $payload = [
            'iss' => base_url(),
            'iat' => $issuedAt,
            'exp' => $expiry,
            'userId' => $userId
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    private function generateRefreshToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime("+{$this->refreshTokenValidity} seconds"));

        $stmt = $this->pdo->prepare("INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $token, $expiry]);

        return $token;
    }

    private function validateJWT($jwt)
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
            return $decoded->userId;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function validateRateLimit($userId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM request_logs WHERE user_id = ? AND request_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        $stmt->execute([$userId]);
        $requestCount = $stmt->fetchColumn();

        if ($requestCount >= $this->rateLimitRequests) {
            return false;
        }

        $stmt = $this->pdo->prepare("INSERT INTO request_logs (user_id, request_time) VALUES (?, NOW())");
        $stmt->execute([$userId]);

        return true;
    }


	private function registerServices($ep)
    {
		$this->server->register('wsCheck',             
		array(), 
		array("return"=>"xsd:string"),
				$ep,
				$ep.'#wsCheck',
				'rpc',
				'encoded',
				'Webservice'
		);	
		$this->server->register('Refer',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
			$ep,
			$ep.'#Refer',
			'rpc',
			'encoded',
			'Webservice');
		$this->server->register('getReferralData',             
			array('data' => 'xsd:string'), 
			array("return"=>"xsd:string"),
					$ep,
					$ep.'#getReferralData',
					'rpc',
					'encoded',
					'Webservice');	
		$this->server->register('getReferralFhud',             
			array('data' => 'xsd:string'), 
			array("return"=>"xsd:string"),
					$ep,
					$ep.'#getReferralFhud',
					'rpc',
					'encoded',
					'Webservice');
		$this->server->register('online',             
			array('data' => "xsd:string"), 
			array("return"=>"xsd:string"),
					$ep,
					$ep.'#online',
					'rpc',
					'encoded',
					'Webservice');

		$this->server->register('receive',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$ep,
		$ep.'#receive',
		'rpc',
		'encoded',
		'Webservice');

		$this->server->register('admit',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$ep,
		$ep.'#admit',
		'rpc',
		'encoded',
		'Webservice');
					
		$this->server->register('discharge',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$ep,
		$ep.'#discharge',
		'rpc',
		'encoded',
		'Webservice');
		
		$this->server->register('getDischargeData',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$ep,
		$ep.'#getDischargeData',
		'rpc',
		'encoded',
		'Webservice');
		$response = service('response');
    }

	
    public function index()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        $jwt = str_replace('Bearer ', '', $authHeader);

        if (!$jwt || !$userId = $this->validateJWT($jwt)) {
            return $this->response->setStatusCode(401)->setBody('Unauthorized');
        }

        if (!$this->validateRateLimit($userId)) {
            return $this->response->setStatusCode(429)->setBody('Too Many Requests');
        }

        $input = file_get_contents("php://input");
        if (empty($input)) {
            $input = $this->request->getBody();
        }

        if ($this->request->uri->getSegment(2) == 'wsdl') {
            $_SERVER['QUERY_STRING'] = 'wsdl';
        } else {
            $_SERVER['QUERY_STRING'] = '';
        }

        $this->response->setHeader('Content-Type', 'text/xml');
        $this->server->service($input);
    }

    public function wsCheck() 
    { 
        try {
            $current_date = date('d-m-Y H:i:s');
            $data = array(
                "Response" => 'Webservice Is Online',
                "DateTime" => $current_date
            );
            echo json_encode($data);
        } 
        catch (SoapFault $exception) 
        {
            return json_encode($exception);
        } 
    }

    // Endpoint to authenticate and generate access and refresh tokens
    public function authenticate($userId)
    {
        $jwt = $this->generateJWT($userId);
        $refreshToken = $this->generateRefreshToken($userId);

        return json_encode([
            'access_token' => $jwt,
            'refresh_token' => $refreshToken,
            'expires_in' => $this->tokenValidity
        ]);
    }

    // Endpoint to refresh the access token
    public function refreshToken($refreshToken)
    {
        // Validate refresh token
        $stmt = $this->pdo->prepare("SELECT user_id FROM refresh_tokens WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$refreshToken]);
        $userId = $stmt->fetchColumn();

        if (!$userId) {
            return $this->response->setStatusCode(401)->setBody('Invalid or Expired Refresh Token');
        }

        // Generate new access token
        $jwt = $this->generateJWT($userId);
        return json_encode(['access_token' => $jwt, 'expires_in' => $this->tokenValidity]);
    }

	
}


