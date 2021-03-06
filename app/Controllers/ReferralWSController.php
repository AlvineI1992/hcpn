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
		
		$server->register('getDischargeData',             
		array('data' => "xsd:string"), 
		array("return"=>"xsd:string"),
		$ep,
		$ep.'#getDischargeData',
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
		$data = $this->Model->getdischargeInformation('HOSP-2071422083643');
		echo json_encode($data);
	}

}
