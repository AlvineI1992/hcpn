<?php

namespace App\Controllers;
use nusoap_client;

use App\Controllers\BaseController;
use App\Models\ReferralModel;

class ReferralWSController extends BaseController
{
	function __construct() {
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
			'urn:homis',
			'urn:homis#Refer',
			'rpc',
			'encoded',
			'Webservice');
			
	$server->register('referralreceive',             
	array('data' => "xsd:string"), 
	array("return"=>"xsd:string"),
			'urn:homis',
			'urn:homis#referralreceive',
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
			
	$server->register('status',             
	array('data' => "xsd:string"), 
	array("return"=>"xsd:string"),
			'urn:homis',
			'urn:homis#status',
			'rpc',
			'encoded',
			'Webservice');
			
	$server->register('referralrefer',             
	array('data' => "xsd:string"), 
	array("return"=>"xsd:string"),
			'urn:homis',
			'urn:homis#referralrefer',
			'rpc',
			'encoded',
			'Webservice');
			
	$server->register('discharge',             
	array('data' => "xsd:string"), 
	array("return"=>"xsd:string"),
			'urn:homis',
			'urn:homis#referraldisch',
			'rpc',
			'encoded',
			'Webservice');
	
	$server->register('getReferralData',             
	array('data' => 'xsd:string'), 
	array("return"=>"xsd:string"),
			'urn:homis',
			'urn:homis#getReferralData',
			'rpc',
			'encoded',
			'Webservice');	

	$server->register('confirmReferral',             
	array('data' => 'xsd:string'), 
	array("return"=>"xsd:string"),
			'urn:homis',
			'urn:homis#confirmReferral',
			'rpc',
			'encoded',
			'Webservice');
			
	$server->register('getReferralFhud',             
	array('data' => 'xsd:string'), 
	array("return"=>"xsd:string"),
			'urn:homis',
			'urn:homis#getReferralFhud',
			'rpc',
			'encoded',
			'Webservice');
   $server->register('returnslip',             
	array('data' => 'xsd:string'), 
	array("return"=>"xsd:string"),
			'urn:homis',
			'urn:homis#returnslip',
			'rpc',
			'encoded',
			'Webservice');
	$server->register('sendtoSeven',             
	array('data' => 'xsd:string'), 
	array("return"=>"xsd:string"),
			'urn:homis',
			'urn:homis#sendtoSeven',
			'rpc',
			'encoded',
			'Webservice');

		
		
	
	}


	function soapMethods()
	{
		$this->Refer();
		$this->getReferralData();
		$this->confirmReferral();
		$this->getReferralFhud();
		$this->referralreceive();
		$this->referralrefer();
		$this->referraldisch();
	
		$this->status();
		$this->sendtoSeven();
		$this->returnslip();
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


	public function client()
	{
	// Config
	 $client = new \nusoap_client('https://hcpn.test/Referral_ws/wsdl', true);
	 
		$client->soap_defencoding = 'UTF-8';
		 $client->decode_utf8 = FALSE;

		// Calls

		 $result = $client->call('wsCheck','');
		if ($client->fault) {
			echo 'Error: ';
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			// check result
			$err_msg = $client->getError();

			if ($err_msg) {
				// Print error msg
				echo '<h2>Constructor error</h2><pre>' . $err_msg . '</pre>';
					echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			} else {
				// Print result
				echo 'Result: ';
				print_r($result);
			}
		}

		$err = $client->getError();
	}




	//WEBSERVICE	

	
	public function status() 
	{
		function status($data) 
		{
			global $wsdl, $client;
			$CI =& get_instance();
			$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($data));
			 $data= json_decode($input,true);
			 $code= $data['hfhudcode'];
			 $status= $data['status'];
			try 
			{
				$row=$CI->Referral_model->updateOnline($code,$status);				 
				return json_encode($row);
			}catch (SoapFault $exception) 
			{
				return json_encode($exception);
			} 
		}
	}
	
	
	function xmlsafechar($as_data){
		if(strpos($as_data,">") !== false){
			$as_data = str_replace("&gt;",">",$as_data);
		}
		
		if(strpos($as_data,"&lt;") !== false){
			$as_data = str_replace("&lt;","<",$as_data);
		}
		
		if(strpos($as_data,"&quot;") !== false){
			$as_data = str_replace("&quot;",'"',$as_data);
		}
		
		if(strpos($as_data,"&apos;") !== false){
			$as_data = str_replace("&apos;","'",$as_data);
		}
		
		if(strpos($as_data,"&amp;") !== false){
			$as_data = str_replace("&amp;","&",$as_data);
		}

		return $as_data;
	}
	
	
	public  function referralreceive()
	{
		function referralreceive($data)
		{
		$CI =& get_instance();
		$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($data));
		 $data= json_decode($input,true);
		 $LogID= $data['LogID'];
		$check=$CI->Referral_model->existLog($LogID);
		 if($check == 0){
			$response=array(
			'LogID'=>$LogID,
			'code'=>'104',
			'response'=>'ID does not exist!',
			'date'=>date('m/d/Y H:i:s'));
			return  json_encode($response);
		 }else{
			 $data=array(
			 'receivedDate'=>$data['receivedDate'],
			 'status'=>'R',
			 'receivedPerson'=>$data['receivedPerson']);
			 $CI->Referral_model->referralReceive($LogID,$data);
			
				$response=array(
				'LogID'=>$LogID,
				'code'=>'200',
				'response'=>'Patient successfully received!',
				'date'=>date('m/d/Y H:i:s'));
				return  json_encode($response);
		 }
		}
	}
	
	public  function returnslip()
	{
		function returnslip($param)
		{
		$CI =& get_instance();
		$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($param));
		$data= json_decode($input,true);
		$LogID= $data['LogID'];
		$check=$CI->Referral_model->existLog($LogID);
		 if($check == 0){
			 $response=array(
			'LogID'=>$LogID,
			'code'=>'104',
			'response'=>'ID does not exist!',
			'date'=>date('m/d/Y H:i:s'));
		 }else{
			$returnSlip=array(
			'LogID'=>$data['LogID'],
			'action'=>$data['action'],
			'actionDate'=>$data['actionDate'],
			'actionTime'=>$data['actionTime'],
			'disposition'=>$data['disposition'],
			'condition'=>$data['condition'],
			'instruction'=>$data['instruction']);
			$row=$CI->Referral_model->existsReturn($LogID);
				if($row== 0)
				{
					$CI->Sql_model->insert('referral_return',$returnSlip);
					 $response=array(
					'LogID'=>$LogID,
					'code'=>'200',
					'response'=>'Success',
					'date'=>date('m/d/Y H:i:s'));
					return json_encode($response);
				}else{
					$return=$CI->Sql_model->update('referral_return','LogID',$returnSlip,$LogID);
					 $response=array(
					'LogID'=>$LogID,
					'code'=>'200',
					'response'=>'Success',
					'date'=>date('m/d/Y H:i:s'));
					return json_encode($response);
				}
		 }
		}
	}
	
	
	public  function referralrefer()
	{
		function referralrefer($param)
		{
		$CI =& get_instance();
		$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($param));
		$data= json_decode($input,true);
		$curLogID= $data['LogID'];
		$check=$CI->Referral_model->existLog($curLogID);
		 if($check == 0){
			$response=array(
			'LogID'=>$curLogID,
			'code'=>'104',
			'response'=>'ID does not exist!',
			'date'=>date('m/d/Y H:i:s'));
			return  json_encode($response);
		 }else{		
			$LogID=$CI->_generateReferCode($data['caseNumber']);
			 $newID=array(
			 'RefID'=>$curLogID,
			 'LogID'=>$LogID);
			 $CI->Referral_model->referUpdate($LogID,$newID);
				
		 		$referInfo=array('LogID'=>$LogID,
				'fhudFrom'=>$data['fhudFrom'],
				'fhudTo'=>$data['fhudto'],
				'typeOfReferral'=>$data['typeOfReferral'],
				'referralReason'=>$data['referralReason'],
				'otherReasons'=>$data['otherReasons'],
				'remarks'=>$data['remarks'],
				'referralContactPerson'=>$data['referralContactPerson'],
				'referralContactPersonDesignation'=>$data['referralPersonDesignation'],
				//'referringProvider'=>$data['referringProvider'],
				'rprhreferral'=>$data['rprhreferral'],
				'rprhreferralmethod'=>$data['rprhreferralmethod'],
				'status'=>$data['status'],
				'refferalDate'=>date("Y-m-d H:i:s",strtotime($data['refferalDate'])),
				'refferalTime'=>date("Y-m-d H:i:s",strtotime($data['refferalDate'])),
				'referralCategory'=>$data['referralCategory'],
				'referringProviderContactNumber'=>$data['referringContactNumber'],
				'logDate'=>date('m/d/Y H:i:s'));
			
				$patientInfo=array('LogID'=>$LogID,
				'FamilyID'=>$data['familyNumber'],
				'phicNum'=>$data['phicNumber'],
				'caseNum'=>$data['caseNumber'],
				'patientLastName'=>$data['patientLastName'],
				'patientFirstName'=>$data['patientFirstName'],
				'patientSuffix'=>$data['patientSuffix'],
				'patientMiddlename'=>$data['patientMiddlename'],
				'patientBirthDate'=>$data['patientBirthDate'],
				'patientSex'=>$data['patientSex'],
				'patientContactNumber'=>$data['patientContactNumber'],
				'patientCivilStatus'=>$data['patientCivilStatus'],
				'patientReligion'=>$data['patientReligion'],
				'patientBloodType'=>$data['patientBloodType'],
				'patientBloodTypeRH'=>$data['patientBloodTypeRH']);
				
				$patientDemo=array('LogID'=>$LogID,
				'patientStreetAddress'=>$data['patientStreetAddress'],
				'patientBrgyCode'=>$data['patientBrgyAddress'],
				'patientMundCode'=>$data['patientMunAddress'],
				'patientProvCode'=>$data['patientProvAddress'],
				'patientRegCode'=>$data['patientRegAddress'],
				'patientZipCode'=>$data['patientZipAddress']);
		
				$patientClinic=array('LogID'=>$LogID,
				'clinicalDiagnosis'=>$data['clinicalDiagnosis'],
				'clinicalHistory'=>$data['clinicalHistory'] ,
				'physicalExamination'=>$data['physicalExamination'],
				'chiefComplaint'=>$data['chiefComplaint'],
				'findings'=>$data['findings'],
				'vitals'=>json_encode($data['vitalSign']));
				
				$track=array('LogID'=>$LogID);
				
				if(!$referInfo){	
					$response=array(
					'LogID'=>$LogID,
					'code'=>'103',
					'response'=>'Invalid parameter: Please check referral data'.$referInfo,
					'date'=>date('m/d/Y H:i:s'));
					$CI->Sql_model->insert('referral_logs',$response);
					return  json_encode($response);
				}else if(!$patientInfo){
					$response=array(
					'LogID'=>$LogID,
					'code'=>'103',	
					'response'=>'Invalid parameter: Please check patient data'.$patientInfo,
					'date'=>date('m/d/Y H:i:s'));
					$CI->Sql_model->insert('referral_logs',$response);
					return  json_encode($response);
				}else if(!$patientDemo){
					$response=array(
					'LogID'=>$LogID,
					'code'=>'103',
					'response'=>'Invalid parameter: Please check  demographic data'.$patientDemo,
					'date'=>date('m/d/Y H:i:s'));
					$CI->Sql_model->insert('referral_logs',$response);
					return  json_encode($response);
				}else if(!$patientClinic){
					$response=array(
					'LogID'=>$LogID,
					'code'=>'103',
					'response'=>'Invalid parameter: Please check clinical data!'.$patientClinic,
					'date'=>date('m/d/Y H:i:s'));
					$CI->Sql_model->insert('referral_logs',$response);
					return json_encode($response);
					
				} else if(!$data['patientProvider']){
					$response=array(
					'LogID'=>$LogID,
					'code'=>'103',
					'response'=>'Invalid parameter: Please check provider data!'.json_encode($data),
					'date'=>date('m/d/Y H:i:s'));
					$CI->Sql_model->insert('referral_logs',$response);
					return  json_encode($response);
				
				} else{
					
					$insRef = $CI->Sql_model->insert('referral_information',$referInfo);
					$insPat = $CI->Sql_model->insert('referral_patientinfo',$patientInfo);
					$insDemo = $CI->Sql_model->insert('referral_patientdemo',$patientDemo);
					$insClinic = $CI->Sql_model->insert('referral_clinical',$patientClinic);
					$insTrack =$CI->Sql_model->insert('referral_track',$track);
					
					for($i=0;$i<count($data['patientProvider']);$i++)
					{
						$patientProvider=array(
						'LogID'=>$LogID,
						'provider_last'=>$data['patientProvider'][$i]['ProviderLast'],
						'provider_first'=>$data['patientProvider'][$i]['ProviderFirst'],
						'provider_middle'=>$data['patientProvider'][$i]['ProviderMiddle'],
						'provider_suffix'=>$data['patientProvider'][$i]['ProviderSuffix'],
						'provider_contact'=>$data['patientProvider'][$i]['ProviderContactNo'],
						'provider_dateadd'=> date('m/d/Y H:i:s'),
						'provider_type'=>$data['patientProvider'][$i]['ProviderType']);
						$insProv = $CI->Sql_model->insert('referral_provider',$patientProvider);
					}
						$response=array(
						'LogID'=>$LogID,
						'code'=>'200',
						'response'=>'Patient referral code:'."=".$LogID."|"."Patient name:".$data['patientLastName'].",".$data['patientFirstName'].",".$data['patientMiddlename'],
						'date'=>date('m/d/Y H:i:s'));
					return  json_encode($response);
				} 
		 }
		}
	}
	
	
	public  function referraldisch()
	{
		function referraldisch($data)
		{
		$CI =& get_instance();
		 $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($data));
		 $data= json_decode($input,true);
		 $LogID= $data['LogID'];
		$check=$CI->Referral_model->existLog($LogID);
		 if($check == 0)
		 {
			$response=array(
			'LogID'=>$LogID,
			'code'=>'104',
			'response'=>'ID does not exist!',
			'date'=>date('m/d/Y H:i:s'));
			return  json_encode($response);
		 }else{
			 $data=array(
			 'Cond'=>$data['Condition'],
			 'Disp'=>$data['Disposition'],
			 'dischDate'=>$data['dischDate'],
			 'trackRemarks'=>$data['trackRemarks']);
			$resp = $this->Sql_model->refertrack($LogID,$data);
			 if ($resp==1)
			 {
				$response=array(
				'LogID'=>$LogID,
				'code'=>'200',
				'response'=>'Patient successfully received!',
				'date'=>date('m/d/Y H:i:s'));
				return  json_encode($response);
			 }
		 }
		}
	}

	  function _response($LogID,$msg)
	 {
			$response=array(
			'LogID'=>$LogID,
			'code'=>'103',
			'response'=>$msg,
			'date'=>date('m/d/Y H:i:s'));

		return json_encode($response);
	 }


	
	public function Refer()
	{
		function Refer($transmitData) 
		{
		$CI =& get_instance();
		$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
		$data= json_decode($input,true);

		$LogID=$CI->_generateReferCode($data['caseNumber']);
		$check=$CI->Referral_model->existLog($LogID);
		if($check == 0){
		 		$referInfo=array('LogID'=>$LogID,
				'fhudFrom'=>$data['fhudFrom'], 
				'fhudTo'=>$data['fhudto'],
				'typeOfReferral'=>$data['typeOfReferral'],
				'referralReason'=>$data['referralReason'],
				'otherReasons'=>$data['otherReasons'],
				'remarks'=>$data['remarks'],
				'referralContactPerson'=>$data['referralContactPerson'],
				'referralContactPersonDesignation'=>$data['referralPersonDesignation'],
				'rprhreferral'=>$data['rprhreferral'],
				'rprhreferralmethod'=>$data['rprhreferralmethod'],
				'status'=>$data['status'],
				'refferalDate'=> $data['referralTime'],
				'refferalTime'=>$data['referralTime'],
				'referralCategory'=>$data['referralCategory'],
				'referringProviderContactNumber'=>$data['referringContactNumber'],
				'logDate'=>date('m/d/Y H:i:s'));
			
				$patientInfo=array('LogID'=>$LogID,
				'FamilyID'=>$data['familyNumber'],
				'phicNum'=>$data['phicNumber'],
				'caseNum'=>$data['caseNumber'],
				'patientLastName'=>$data['patientLastName'],
				'patientFirstName'=>$data['patientFirstName'],
				'patientSuffix'=>$data['patientSuffix'],
				'patientMiddlename'=>$data['patientMiddlename'],
				'patientBirthDate'=>$data['patientBirthDate'],
				'patientSex'=>$data['patientSex'],
				'patientContactNumber'=>$data['patientContactNumber'],
				'patientCivilStatus'=>$data['patientCivilStatus'],
				'patientReligion'=>$data['patientReligion'],
				'patientBloodType'=>$data['patientBloodType'],
				'patientBloodTypeRH'=>$data['patientBloodTypeRH']);

				$patientDemo=array('LogID'=>$LogID,
				'patientStreetAddress'=>$data['patientStreetAddress'],
				'patientBrgyCode'=>$data['patientBrgyAddress'],
				'patientMundCode'=>$data['patientMunAddress'],
				'patientProvCode'=>$data['patientProvAddress'],
				'patientRegCode'=>$data['patientRegAddress'],
				'patientZipCode'=>$data['patientZipAddress']);
		
				$patientClinic=array('LogID'=>$LogID,
				'clinicalDiagnosis'=>$data['clinicalDiagnosis'],
				'clinicalHistory'=>$data['clinicalHistory'] ,
				'physicalExamination'=>$data['physicalExamination'],
				'chiefComplaint'=>$data['chiefComplaint'],
				'findings'=>$data['findings'],
				'vitals'=>json_encode($data['vitalSign']));
				
				$track=array('LogID'=>$LogID);
				
				if(!$referInfo){	
					$response=array(
					'LogID'=>$LogID,
					'code'=>'103',
					'response'=>'Invalid parameter: Please check referral data'.$referInfo,
					'date'=>date('m/d/Y H:i:s'));
					$CI->Sql_model->insert('referral_logs',$response);
					return  json_encode($response);
				}else if(!$patientInfo){
					$response=array(
					'LogID'=>$LogID,
					'code'=>'103',	
					'response'=>'Invalid parameter: Please check patient data'.$patientInfo,
					'date'=>date('m/d/Y H:i:s'));
					$CI->Sql_model->insert('referral_logs',$response);
					return  json_encode($response);
				}else if(!$patientDemo){
					$response=array(
					'LogID'=>$LogID,
					'code'=>'103',
					'response'=>'Invalid parameter: Please check  demographic data'.$patientDemo,
					'date'=>date('m/d/Y H:i:s'));
					$CI->Sql_model->insert('referral_logs',$response);
					return  json_encode($response);
				}else if(!$patientClinic){
					$response=array(
					'LogID'=>$LogID,
					'code'=>'103',
					'response'=>'Invalid parameter: Please check clinical data!'.$patientClinic,
					'date'=>date('m/d/Y H:i:s'));
					$CI->Sql_model->insert('referral_logs',$response);
					return json_encode($response);
					
				}else if(!$data['patientProvider']){
					$response=array(
					'LogID'=>$LogID,
					'code'=>'103',
					'response'=>'Invalid parameter: Please check provider data!'.json_encode($data),
					'date'=>date('m/d/Y H:i:s'));
					$CI->Sql_model->insert('referral_logs',$response);	
				}else{
					$insRef = $CI->Sql_model->insert('referral_information',$referInfo);	
					$insPat = $CI->Sql_model->insert('referral_patientinfo',$patientInfo);
					$insDemo = $CI->Sql_model->insert('referral_patientdemo',$patientDemo);
					$insClinic = $CI->Sql_model->insert('referral_clinical',$patientClinic);
					$insTrack =$CI->Sql_model->insert('referral_track',$track);
					if($insRef  != 1)
					{
						$response=array(
						'LogID'=>$LogID,
						'code'=>'200',
						'response'=>$insRef,
						'date'=>date('m/d/Y H:i:s'));
						return  json_encode($response);
					}
					
					if($insPat  != 1)
					{
						$response=array(
						'LogID'=>$LogID,
						'code'=>'200',
						'response'=>$insPat,
						'date'=>date('m/d/Y H:i:s'));
						return  json_encode($response);
					}
					
					if($insDemo  != 1)
					{
						$response=array(
						'LogID'=>$LogID,
						'code'=>'200',
						'response'=>$insDemo,
						'date'=>date('m/d/Y H:i:s'));
						return  json_encode($response);
					}
					
					if($insTrack  != 1)
					{
						$response=array(
						'LogID'=>$LogID,
						'code'=>'200',
						'response'=>$insTrack,
						'date'=>date('m/d/Y H:i:s'));
						return  json_encode($response);
					}
					
					
					if($insClinic  != 1)
					{
						$response=array(
						'LogID'=>$LogID,
						'code'=>'200',
						'response'=>$insClinic,
						'date'=>date('m/d/Y H:i:s'));
						return  json_encode($response);
					}
					
					
					
					for($i=0;$i<count($data['patientProvider']);$i++)
					{
						$patientProvider=array(
						'LogID'=>$LogID,
						'provider_last'=>$data['patientProvider'][$i]['ProviderLast'],
						'provider_first'=>$data['patientProvider'][$i]['ProviderFirst'],
						'provider_middle'=>$data['patientProvider'][$i]['ProviderMiddle'],
						'provider_suffix'=>$data['patientProvider'][$i]['ProviderSuffix'],
						'provider_contact'=>$data['patientProvider'][$i]['ProviderContactNo'],
						'provider_dateadd'=> date('m/d/Y H:i:s'),
						'provider_type'=>$data['patientProvider'][$i]['ProviderType']);
						$insProv = $CI->Sql_model->insert('referral_provider',$patientProvider);
					}
						$response=array(
						'LogID'=>$LogID,
						'code'=>'200',
						'response'=>'Referral code:'."=".$LogID."|"."Patient name:".$data['patientLastName']."  ".$data['patientFirstName']."   ".$data['patientMiddlename'],
						'date'=>date('m/d/Y H:i:s'));
						return  json_encode($response);
				}
		}else{
			$response=array(
			'LogID'=>$LogID,
			'code'=>'104',
			'response'=>'Referral Exist!',
			'date'=>date('m/d/Y H:i:s'));
			return  json_encode($response);
		}		
		
		}
		}
	
	public function sendtoSeven()
	{
		function sendtoSeven($transmitData) 
		{
		$CI =& get_instance();
		$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
		$param= json_decode($input,true);
		$curl = curl_init();
		$data=array(
		'province' =>$param['patientProvAddress'].'0000',
		'muncity' => $param['patientMunAddress'].'000',
		'barangay' =>$param['patientBrgyAddress'],
		'referring_facility' =>$param['fhudFrom'],
		'referred_facility' =>$param['fhudto'],
		'phic_id' =>$param['phicNum'],
		'fname' => $param['patientLastName'],
		'mname' => $param['patientMiddlename'],
		'lname' => $param['patientFirstName'],
		'nhtsID' => 'n/a',
		'civil_status'=>$param['patientFirstName'],
		'referring_md_fname' => $param['patientProvider'][0]['ProviderFirst'],
		'referring_md_mname' =>$param['patientProvider'][0]['ProviderMiddle'],
		'referring_md_lname' => $param['patientProvider'][0]['ProviderLast'],
		'referring_md_contact' =>$param['patientProvider'][0]['ProviderContactNo'],
		'department_id' => '3',
		'referred_md_fname' =>$param['patientProvider'][1]['ProviderFirst'],
		'referred_md_mname' => $param['patientProvider'][1]['ProviderMiddle'],
		'referred_md_lname' => $param['patientProvider'][1]['ProviderLast'],
		'referred_md_contact' =>$param['patientProvider'][1]['ProviderContactNo'],
		'covid_number'=> 'null',
		'clinical_status'=> 'n/a',
		'sur_category'=>$param['findings'],
		'case_summary'=>'null',
		'reco_summary'=>'null',
		'reason'=>$param['referralReason'],
		'other_reason_referral'=>$param['otherReasons'],
		'diagnosis' => $param['findings'],
		'other_diagnoses' => $param['clinicalDiagnosis'],
		'dob' => date("Y-m-d",strtotime($param['patientBirthDate'])),
		'sex' => $param['patientSex'],
		'contact' => $param['patientContactNumber'],
		'icd_ids' => '',
		'file_upload' => '',
		'source'=>'ihomisplus');	
		  curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://cvehrs.doh.gov.ph/dummy/referral/api/refer/patient',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>$data));
		$return =   curl_exec($curl);
			$fp = fopen('response.txt', 'w');
			fwrite($fp,$return);
			fclose($fp);
		return $return;
	
    }	
    }	


	function getReferralData()
	{ 
		function getReferralData($transmitData)
		{
			$CI =& get_instance();
			$response=[];
			$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
			$data= json_decode($input,true);

			if(empty($data['LogID'])){
				$response=array(
				'raw'=>$transmitData,
				'input'=>$input,
				'data'=>$data,
				'status'=>"Error",
				'message'=>"Missing LogID!");
				return json_encode($response);

           }else if(empty($data['fhudcode'])){

				$response=array(
				'input'=>$transmitData,
				'data'=>$input,
				'status'=>"Error",
				'message'=>"Missing Facility Code!");
				return  json_encode($response);
		   }else{
			
			$patientInfo=$CI->Referral_model->getPatientInfo($data['LogID'],$data['fhudcode']);
			$clinicalInfo=$CI->Referral_model->getClinicalInfo($data['LogID']);
			$providerInfo=$CI->Referral_model->getReferProvider($data['LogID']);
	
			$response =array(
			'fhudFrom'=>$patientInfo->fhudFrom,
			'fhudto'=>$patientInfo->fhudto,
			'typeOfReferral'=>$patientInfo->typeOfReferral,
			'referralReason'=>$patientInfo->referralReason,
			'otherReasons'=>$patientInfo->otherReasons,
			'remarks'=>$patientInfo->remarks,
			'referralContactPerson'=>$patientInfo->referralContactPerson,
			'referralPersonDesignation'=>$patientInfo->referralContactPersonDesignation,
			'rprhreferral'=>$patientInfo->rprhreferral,
			'rprhreferralmethod'=>$patientInfo->rprhreferralmethod,
			'status'=>$patientInfo->status,
			'referringContactNumber'=>$patientInfo->referringProviderContactNumber,
			'referralDate'=>$patientInfo->refferalDate,
			'referralTime'=>$patientInfo->refferalTime,
			'referralCategory'=>$patientInfo->referralCategory,
			'familyNumber'=>$patientInfo->FamilyID,
			'phicNumber'=>$patientInfo->phicNum,
			'caseNumber'=>$patientInfo->caseNum,
			'patientLastName'=>$patientInfo->patientLastName,
			'patientFirstName'=>$patientInfo->patientFirstName,
			'patientSuffix'=>$patientInfo->patientSuffix,
			'patientMiddlename'=>$patientInfo->patientMiddlename,
			'patientBirthDate'=>$patientInfo->patientBirthDate,
			'patientSex'=>$patientInfo->patientSex,
			'patientCivilStatus'=>$patientInfo->patientCivilStatus,
			'patientReligion'=>$patientInfo->patientReligion,
			'patientBloodType'=>$patientInfo->patientBloodType,
			'patientBlooTypeRH'=>$patientInfo->patientBloodTypeRH,
			'patientContactNumber'=>$patientInfo->patientContactNumber,
			'patientStreetAddress'=>$patientInfo->patientStreetAddress,
			'patientBrgyAddress'=>$patientInfo->patientBrgyAddress,
			'patientMunAddress'=>$patientInfo->patientMunAddress,
			'patientProvAddress'=>$patientInfo->patientProvAddress,
			'patientRegAddress'=>$patientInfo->patientRegAddress,
			'patientZipAddress'=>$patientInfo->patientZipAddress,
			'clinicalDiagnosis'=>$clinicalInfo->clinicalDiagnosis,
			'physicalExamination'=>$clinicalInfo->physicalExamination,
			'chiefComplaint'=>$clinicalInfo->chiefComplaint,
			'clinicalHistory'=>$clinicalInfo->clinicalHistory,
			'findings'=>$clinicalInfo->findings,
			'vitalSign'=>stripslashes($clinicalInfo->vitalSign),
			'patientProvider'=>$providerInfo);
			
			return json_encode($response);
		}
	}
}
	
	function getReferralFhud()
	{
		function getReferralFhud($hfhudcode)
		{
			$CI =& get_instance();
			$info=$CI->Referral_model->getReferralfhud($hfhudcode);
			$response= [];
			foreach($info as $patientInfo)
			{
				$providerID=$CI->Referral_model->getReferProvider($patientInfo['LogID']);
				$clinicalInfo=$CI->Referral_model->getClinicalInfo($patientInfo['LogID']);
				$response['data'][] =array(
				'LogID'=>$patientInfo['LogID'],
				'fhudFrom'=>$patientInfo['fhudFrom'],
				'fhudto'=>$patientInfo['fhudto'],
				'typeOfReferral'=>$patientInfo['typeOfReferral'],
				'referralReason'=>$patientInfo['referralReason'],
				'otherReasons'=>$patientInfo['otherReasons'],
				'remarks'=>$patientInfo['remarks'],
				'referralContactPerson'=>$patientInfo['referralContactPerson'],
				'referralPersonDesignation'=>$patientInfo['referralContactPersonDesignation'],
				'rprhreferral'=>$patientInfo['referral'],
				'rprhreferralmethod'=>$patientInfo['rprhreferralmethod'],
				'status'=>$patientInfo['status'],
				'referringContactNumber'=>$patientInfo['referringProviderContactNumber'],
				'referralDate'=>$patientInfo['refferalDate'],
				'referralTime'=>$patientInfo['refferalTime'],
				'referralCategory'=>$patientInfo['referralCategory'],
				'familyNumber'=>$patientInfo['FamilyID'],
				'phicNumber'=>$patientInfo['phicNum'],
				'caseNumber'=>$patientInfo['caseNum'],
				'patientLastName'=>$patientInfo['patientLastName'],
				'patientFirstName'=>$patientInfo['patientFirstName'],
				'patientSuffix'=>$patientInfo['patientSuffix'],
				'patientMiddlename'=>$patientInfo['patientMiddlename'],
				'patientBirthDate'=>$patientInfo['patientBirthDate'],
				'patientSex'=>$patientInfo['patientSex'],
				'patientCivilStatus'=>$patientInfo['patientCivilStatus'],
				'patientReligion'=>$patientInfo['patientReligion'],
				'patientBloodType'=>$patientInfo['patientBloodType'],
				'patientBlooTypeRH'=>$patientInfo['patientBloodTypeRH'],
				'patientStreetAddress'=>$patientInfo['patientStreetAddress'],
				'patientBrgyAddress'=>$patientInfo['patientBrgyAddress'],
				'patientMunAddress'=>$patientInfo['patientMunAddress'],
				'patientProvAddress'=>$patientInfo['patientProvAddress'],
				'patientRegAddress'=>$patientInfo['patientRegAddress'],
				'patientZipAddress'=>$patientInfo['patientZipAddress'],
				'patientContactNumber'=>$patientInfo['patientContactNumber'],
				'findings'=>$clinicalInfo->findings,
				'chiefComplaint'=>$clinicalInfo->chiefComplaint,
				'clinicalDiagnosis'=>$clinicalInfo->clinicalDiagnosis,
			 	'physicalExamination'=>$clinicalInfo->physicalExamination,
			 	'clinicalHistory'=>$clinicalInfo->clinicalHistory,
				'vitalSign'=>stripslashes($clinicalInfo->vitalSign), 
				'patientProvider'=>$providerID);
			} 
		return json_encode($response);
		}
	}

	function confirmReferral()
	{ 
		function confirmReferral($LogID)
		{
			$CI =& get_instance();
			$data=array('status'=>'C');
			$return=$CI->Sql_model->update('referral_information','LogID',$data,$LogID);
			if($return==1)
			{
				$return=array(
				'LogID'=>$LogID,
				'code'=>'200',
				'response'=>'Successfully Confirmed',
				'date'=>date('m/d/Y H:i:s'));
				$CI->Sql_model->insert('referral_logs',$return);
			}else{
				$return=array(
				'LogID'=>$LogID,
				'code'=>'200',
				'response'=>'Referral Code not exist',
				'date'=>date('m/d/Y H:i:s'));
				$CI->Sql_model->insert('referral_logs',$return);
			}
			return json_encode($return);
		}
	}
	
	function _checkFacility($param)
	{
		$data=json_decode($param,true);
		$return=[];
		$fromFactility=$this->Sql_model->exist('trn_engage_info','hfhudcode',$data['from']);
		$toFactility=$this->Sql_model->exist('trn_engage_info','hfhudcode',$data['to']);
		if($fromFactility!=1)
		{
			$return=array(
			'LogID'=>$data['logid'],
			'code'=>'500',
			'response'=>'Referring facility not engaged!',
			'date'=>date('m/d/Y H:i:s'));
			$this->Sql_model->insert('referral_logs',$return);
		}
		else if($toFactility!=1)
		{
			$return=array(
			'LogID'=>$data['logid'],
			'code'=>'500',
			'response'=>'Receiving facility not engaged!',
			'date'=>date('m/d/Y H:i:s'));
			$this->Sql_model->insert('referral_logs',$return);
		}
		else
		{
			$account=json_decode($this->_checkAccount($data['from']),true);
			 if($account!=null || !empty($account))
			 {
			if(($account['username']==$data['username'])&&($account['password']==$data['password'])&& ($account['status']=='A'))
			{
				$return=array(
				'LogID'=>$data['logid'],
				'code'=>'200',
				'response'=>'Success!',
				'date'=>date('m/d/Y H:i:s'));
				$this->Sql_model->insert('referral_logs',$return);
			}elseif(($account['username']==$data['username'])&&($account['password']==$data['password'])&& ($account['status']=='I'))
			{
				$return=array(
				'LogID'=>$data['logid'],
				'code'=>'500',
				'response'=>'Account is Deactivated!',
				'date'=>date('m/d/Y H:i:s'));
				$this->Sql_model->insert('referral_logs',$return);
			}
			else
			{
				$return=array(
				'LogID'=>$data['logid'],
				'code'=>'500',
				'response'=>'Check your credentials!',
				'date'=>date('m/d/Y H:i:s'));
				$this->Sql_model->insert('referral_logs',$return);
			}
			 }else{
				 $return=array(
				'LogID'=>$data['logid'],
				'code'=>'500',
				'response'=>'Error on Credentials!',
				'date'=>date('m/d/Y H:i:s'));
				$this->Sql_model->insert('referral_logs',$return);
			 }
			
		}
		return json_encode($return);
	}
	
	function _checkAccount($fhudcode)
	{ 
	if(!empty($fhudcode)){
		$engage=$this->Sql_model->get_by_id('trn_engage_info','hfhudcode',$fhudcode);
		 if(empty($engage->engage_id)){
			return 'null';
		 }else{
			$account=$this->engageModel->checkRefAccount($engage->engage_id);
			return json_encode($account);
		 }
	}else{
		return 'code is empty';
	}
		
	}
	
	/* function _generateReferCode($fhudcode,$casenum)
	{
		$code = preg_replace('/000+/','', $fhudcode);
		$code .= $casenum;
		$code .= $this->Referral_model->checkRef()+1;
		return $code =str_pad($code,6,0, STR_PAD_LEFT);
	} */
	
	function _generateReferCode($casenum)
	{
		$code  ='REF';
		$code .= $casenum;
		$code .= $this->Referral_model->checkRef()+1;
		return $code =str_pad($code,6,0, STR_PAD_LEFT);
	}
	
	function generateReferedCode($casenum='00000001')
	{
		$code  ='REF';
		$code .=date('Ymdhis');
		$code .= $casenum;
		$code .= $this->Referral_model->checkRefer()+1;
		return  $code =str_pad($code,6,0, STR_PAD_LEFT);
	}
}
