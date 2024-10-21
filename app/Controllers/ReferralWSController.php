<?php
namespace App\Controllers;
use econea\nusoap_client;
use App\Controllers\BaseController;
use App\Models\ReferralModel;

class ReferralWSController extends BaseController
{
	function __construct() {

		$this->Model = new ReferralModel();
		$ep= base_url()."/referral/wsdl";
		$server =$this->server = new \nusoap_server();
		$this->server->configureWSDL('homis',$ep,$ep);
		$this->server->wsdl->schemaTargetNamespace = $ep;  
		$this->server->register('wsCheck',             
		array(), 
		array("return"=>"xsd:string"),
				$ep,
				$ep.'#wsCheck',
				'rpc',
				'encoded',
				'Webservice'
		);	
		$server->register('Refer',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
			$ep,
			$ep.'#Refer',
			'rpc',
			'encoded',
			'Webservice');
	    $server->register('getReferralData',             
			array('data' => 'xsd:string'), 
			array("return"=>"xsd:string"),
					$ep,
					$ep.'#getReferralData',
					'rpc',
					'encoded',
					'Webservice');	
		$server->register('getReferralFhud',             
			array('data' => 'xsd:string'), 
			array("return"=>"xsd:string"),
					$ep,
					$ep.'#getReferralFhud',
					'rpc',
					'encoded',
					'Webservice');
		$server->register('online',             
			array('data' => "xsd:string"), 
			array("return"=>"xsd:string"),
					$ep,
					$ep.'#online',
					'rpc',
					'encoded',
					'Webservice');

		$server->register('receive',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$ep,
		$ep.'#receive',
		'rpc',
		'encoded',
		'Webservice');

		$server->register('admit',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$ep,
		$ep.'#admit',
		'rpc',
		'encoded',
		'Webservice');
					
		$server->register('discharge',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$ep,
		$ep.'#discharge',
		'rpc',
		'encoded',
		'Webservice');
		
		$server->register('getDischargeData',             
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

		log_message('debug', 'Entering index method for SOAP handling.');

		// Read the input from php://input or getBody()
		$input = file_get_contents("php://input");
		if (empty($input)) {
			$input = $this->request->getBody();
		}
	
		log_message('debug', 'SOAP Request Input: ' . $input);
	
		if ($this->request->uri->getSegment(2) == 'wsdl') {
			$_SERVER['QUERY_STRING'] = 'wsdl';
		} else {
			$_SERVER['QUERY_STRING'] = '';
		}
	
		// Set the response type and handle the request
		$this->response->setHeader('Content-Type', 'text/xml');
		$this->server->service($input);
    }
	
	public function wsCheck() 
    { 
        try {
            $current_date = date('d-m-Y H:i:s');
            $data=array(
            "Response"=>'Webservice Is Online',
            "DateTime"=>$current_date);
                echo json_encode($data);
        } 
        catch (SoapFault $exception) 
        {
              return json_encode($exception);
        } 
    }
	
}


