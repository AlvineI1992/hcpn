<?php


if ( !function_exists('wsCheck') ) {
    function wsCheck() 
    { 
        try 
        {
            $current_date = date('d-m-Y H:i:s');
            $data=array(
            "Response"=>'Webservice Is Online',
            "DateTime"=>$current_date);
                return json_encode($data);
        } 
        catch (SoapFault $exception) 
        {
                return json_encode($exception);
        } 

    }
}

if ( !function_exists('online') ) {
		function online($region) 
		{
          
	    	try 
			{
                $model = new \App\Models\ReferralModel;
                $row=  $model->onlineFacilities($region); 
                 return json_encode($row);
			}catch (SoapFault $exception) 
			{
				return json_encode($exception);
			} 
		}

    
}


if ( !function_exists('referralrefer') ) {
    
    
    function referralrefer($param){


   // $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($param));
$data= json_decode($param,true);
$curLogID= $data['LogID'];

$model = new \App\Models\ReferralModel;
$check=$model->existLog($curLogID);
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
}



  

?>