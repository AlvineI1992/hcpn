<?php

namespace App\Controllers;
use nusoap_client;

use App\Controllers\BaseController;
use App\Models\ReferralModel;

class ReferralWSController extends BaseController
{
	function __construct() {


		$this->Model = new ReferralModel();
		$ep= base_url()."/Referral_ws/wsdl";
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
		

		$server->register('medicine',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$ep,
		$ep.'#medicine',
		'rpc',
		'encoded',
		'Webservice');

		$server->register('followup',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$ep,
		$ep.'#followup',
		'rpc',
		'encoded',
		'Webservice');
						
	
	
	}
    function index()  
	{
		if($this->request->uri->getSegment(2)== "wsdl") {
			 $_SERVER['QUERY_STRING'] =  "wsdl";
		} else {
			 $_SERVER['QUERY_STRING'] =  "";
		}
		$this->response->setHeader('Content-Type', 'text/xml');
		$this->server->service(file_get_contents("php://input"));
    }

	function test()
	{
		$param  =array(
			'LogID'=>'',
			'date'=>'',
			'disposition'=>'',
			'condition'=>'',
			'remarks'=>'',
			'hasFollowup'=>'',
			'hasMedicine'=>'');
        echo json_encode($param);
	}

	

}
