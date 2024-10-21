<?php
namespace App\Controllers;
use econea\nusoap_client;
use App\Controllers\BaseController;
use App\Models\ReferralModel;

class ReferralWSController extends BaseController
{ 
	function __construct() {

		$this->Model = new ReferralModel();
		$end_point= base_url()."referral/wsdl";
		$server =$this->server = new \nusoap_server();
		$this->server->configureWSDL('hcpn',$end_point,$end_point);
		$this->server->wsdl->schemaTargetNamespace = $end_point;  

		$server->register('wsCheck',             
		array(), 
		array("return"=>"xsd:string"),
				$end_point,
				$end_point.'#wsCheck',
				'rpc',
				'encoded',
				'Webservice'
		);	
		$server->register('Refer',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
			$end_point,
			$end_point.'#Refer',
			'rpc',
			'encoded',
			'Webservice');
	    $server->register('getReferralData',             
			array('data' => 'xsd:string'), 
			array("return"=>"xsd:string"),
					$end_point,
					$end_point.'#getReferralData',
					'rpc',
					'encoded',
					'Webservice');	
		$server->register('getReferralFhud',             
			array('data' => 'xsd:string'), 
			array("return"=>"xsd:string"),
					$end_point,
					$end_point.'#getReferralFhud',
					'rpc',
					'encoded',
					'Webservice');
		$server->register('online',             
			array('data' => "xsd:string"), 
			array("return"=>"xsd:string"),
					$end_point,
					$end_point.'#online',
					'rpc',
					'encoded',
					'Webservice');

		$server->register('receive',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$end_point,
		$end_point.'#receive',
		'rpc',
		'encoded',
		'Webservice');

		$server->register('admit',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$end_point,
		$end_point.'#admit',
		'rpc',
		'encoded',
		'Webservice');
					
		$server->register('discharge',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$end_point,
		$end_point.'#discharge',
		'rpc',
		'encoded',
		'Webservice');
		
		$server->register('getDischargeData',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$end_point,
		$end_point.'#getDischargeData',
		'rpc',
		'encoded',
		'Webservice');
		$response = service('response');

	}
	
    public function index()
    {
		if($this->request->uri->getSegment(2)=='wsdl') {
			$_SERVER['QUERY_STRING'] ='wsdl';
		} else {
			$_SERVER['QUERY_STRING'] ='';
		}
		$this->response->setHeader('Content-Type', 'text/xml');
		$this->server->service(file_get_contents("php://input"));
		
    }
}


