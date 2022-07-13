<?php

namespace App\Controllers;
use nusoap_client;

use App\Controllers\BaseController;
use App\Models\ReferralModel;

class ReferralWSController extends BaseController
{
	function __construct() {


		$this->Model = new ReferralModel();
		$ep= base_url()."/public/Referral_ws/wsdl";
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

	public  function test()
	{
		//$data = $this->Model->getPatientInfo('HOSP2016-0030082071222105309','DOH000000000005937');
		//$data = $this->Model->getClinicalInfo('HOSP2016-0030082071222105309');
		//$data = $this->Model->getReferProvider('HOSP2016-0030082071222105309');
		//$data = $this->Model->getReferralfhud('DOH000000000005937');
		//echo json_encode($data);

		//$this->Model = new \App\Models\ReferralModel;
            $type =  $this->Model->type('DOH000000000005937')->facility_type;
            if($type == '4' || $type == '1'){
                $code  ='HOSP-';
                $code .=  $this->Model->maxID()+1;
				$code .= date('mdyhis');
                $code .= generateRandomString();
                echo  $code =str_pad($code,6,0, STR_PAD_LEFT);
            }else if($type == '17'){
                $code  ='RHU-';
                $code .=$this->Model->maxID()+1;
				$code .= date('mdyhis');
				$code .= generateRandomString();
                echo  $code =str_pad($code,6,0, STR_PAD_LEFT);
            }else{
                return  0;
            }

	}
	//WEBSERVICE

	

}
