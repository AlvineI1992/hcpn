<?php

if ( !function_exists('receive') ) {
    function receive($transmitData) 
    { 
        try 
        {
            $model = new \App\Models\ReferralModel;
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data= json_decode($input,true);
            if(empty($data['LogID'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Reference ID is Invalid or null',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }
            if(empty($data['receivedDate'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Received date is Invalid or null',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }
            if(empty($data['receivedPerson'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Received person is Invalid or null',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }
            $param  =array(
                    'LogID'=>$data['LogID'],
                    'receivedDate'=>date("Y-m-d H:i:s",strtotime($data['receivedDate'])),
                    'receivedPerson'=>$data['receivedPerson']);
            $model->insertTrack($param);
            $response=array(
                    'LogID'=>$data['LogID'],
                    'code'=>'200',
                    'response'=>'Patient received',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
        } 
        catch (SoapFault $exception) 
        {
                return json_encode($exception);
        } 

    }
}


if ( !function_exists('admit') ) {
    function admit($transmitData) 
    { 
        try 
        {
            $model = new \App\Models\ReferralModel;
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data= json_decode($input,true);
            if(empty($data['LogID'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Reference ID is Invalid or null',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }
            if(empty($data['date'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Received date is Invalid or null',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }
            if(empty($data['disp'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Received person is Invalid or null',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }
            $param  =array(
                'LogID'=>$data['LogID'],
                'admDate'=>date("Y-m-d H:i:s",strtotime($data['date'])),
                'admDisp'=>$data['disp']);
            $model->admiPatient($data['LogID'],$param);
            $response=array(
                'LogID'=>$data['LogID'],
                'code'=>'200',
                'response'=>'Patient is now admitted',
                'date'=>date('m/d/Y H:i:s'));
                return json_encode($response);
        } 
        catch (SoapFault $exception) 
        {
                return json_encode($exception);
        } 

    }
}

if ( !function_exists('discharge') ) {
    function discharge($transmitData) 
    { 
        try 
        {
            $model = new \App\Models\ReferralModel;
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data= json_decode($input,true);
            if(empty($data['LogID'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Reference ID is Invalid or null',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }
            if(empty($data['admDate'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Admission date is required!',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }

            if(empty($data['dischDate'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Discharge date is required',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }
            if(empty($data['disposition'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Disposition is Invalid or null',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }
            if(empty($data['condition'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Condition is Invalid or null',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }

            if(empty($data['hasFollowUp'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Follow Up status is required!',
                    'data'=>'Y if Yes & N for No',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }

            if(empty($data['hasMedicine'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Medicine status is required!',
                    'data'=>'Y if Yes & N for No',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }

            if(empty($data['diagnosis'])){
                $response=array(
                    'code'=>'403',
                    'response'=>'Diagnosis is required!',
                    'data'=>'text format only',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
            }

            $discharge  =array(
                'LogID'=>$data['LogID'],
                'admDate'=>date("Y-m-d H:i:s",strtotime($data['admDate'])),
                'dischDate'=>date("Y-m-d H:i:s",strtotime($data['dischDate'])),
                'dischDisp'=>$data['disposition'],
                'dischCond'=>$data['condition'],
                'diagnosis'=>$data['diagnosis'],
                'trackRemarks'=>$data['remarks'],
                'hasFollowUp'=>$data['hasFollowUp'],
                'hasMedicine'=>$data['hasMedicine']);
            $folUp= array(
            'LogID'=>$data['schedule']['LogID'],
            'scheduleDateTime'=>date("Y-m-d H:i:s",strtotime($data['schedule']['date'])),
            );

            $param = array(
                'LogID'=>$data['LogID'],
                'discharge'=>$discharge,
                'medicine'=> $data['drugs'],
                'followup'=> $folUp);
             $trans=$model->dischargeTransaction($param);
            if($trans['code'] == '200'){ 
                $response=array(
                   'LogID'=>$data['LogID'],
                   'code'=>$trans['code'],
                   'message'=>$trans['message'],
                   'date'=>date('m/d/Y H:i:s'));
                   return  json_encode($response);
                }else{
                    $response=array(
                        'code'=>$trans['code'],
                        'message'=>$trans['message'],
                        'date'=>date('m/d/Y H:i:s'));
                        return  json_encode($response);
                } 
        } 
        catch (SoapFault $exception) 
        {
                return json_encode($exception);
        } 

    }
}

if ( !function_exists('followup') ) {
    function followup($transmitData) 
    { 
        $param  =array(
            'LogID'=>$data['LogID'],
            'scheduleDateTime'=>$data['date']);
        $model->insertFollowUp($param);
    }
}

if ( !function_exists('medicine') ) {
    function medicine($transmitData) 
    { 
        $param  =array(
            'LogID'=>$data['LogID'],
            'drugcode'=>$data['intake'],
            'generic'=>$data['uom'],
            'instruction'=>$data['form'],
            'EDPMSID'=>$data['time']);
        $model->insertMedicine($param);
 }
}

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


    if ( !function_exists('Refer') ) {
        function Refer($transmitData) 
		{
            $model = new \App\Models\ReferralModel;
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data= json_decode($input,true);
            if($model->checkFacility($data['fhudFrom']) != '1'){
                     $response=array(
                    'code'=>'403',
                    'response'=>'Contact system administrator referring facility not exist/s',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
              }
              if($model->checkFacility($data['fhudto']) != '1'){
                    $response=array(
                    'code'=>'403',
                    'response'=>'Contact system administrator referral facility not exist/s',
                    'date'=>date('m/d/Y H:i:s'));
                    return json_encode($response);
             }
            $LogID=generateReferCode($data['fhudFrom']);
          
            $check=$model->existLog($LogID)->count;
    
            if($check == 0){
                $referInfo=array('LogID'=>$LogID,
               'fhudFrom'=>$data['fhudFrom'], 
               'fhudTo'=>$data['fhudto'],
               'typeOfReferral'=>$data['typeOfReferral'],
               'referralReason'=>$data['referralReason'],
               'otherReasons'=>$data['otherReasons'],
               'remarks'=>$data['remarks'],
               'referralContactPerson'=>$data['referralContactPerson'],
               'referringProviderContactNumber'=>$data['referringContactNumber'],
               'referralContactPersonDesignation'=>$data['referralPersonDesignation'],
               'rprhreferral'=>$data['rprhreferral'],
               'rprhreferralmethod'=>$data['rprhreferralmethod'],
               'status'=>$data['status'],
               'refferalDate'=> $data['referralDate'],
               'refferalTime'=>$data['referralTime'],
               'referralCategory'=>$data['referralCategory'],
               'logDate'=>date('Y/m/d H:i:s'));
            
               $patientInfo=array('LogID'=>$LogID,
               'FamilyID'=>$data['familyNumber'],
               'phicNum'=>$data['phicNumber'],
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
            
               $provConsu=array(
                'LogID'=>$LogID,
                'provider_last'=>$data['patientProvider'][0]['ProviderLast'],
                'provider_first'=>$data['patientProvider'][0]['ProviderFirst'],
                'provider_middle'=>$data['patientProvider'][0]['ProviderMiddle'],
                'provider_suffix'=>$data['patientProvider'][0]['ProviderSuffix'],
                'provider_contact'=>$data['patientProvider'][0]['ProviderContactNo'],
                'provider_type'=>$data['patientProvider'][0]['ProviderType']);

                $provRefer=array(
                    'LogID'=>$LogID,
                    'provider_last'=>$data['patientProvider'][1]['ProviderLast'],
                    'provider_first'=>$data['patientProvider'][1]['ProviderFirst'],
                    'provider_middle'=>$data['patientProvider'][1]['ProviderMiddle'],
                    'provider_suffix'=>$data['patientProvider'][1]['ProviderSuffix'],
                    'provider_contact'=>$data['patientProvider'][1]['ProviderContactNo'],
                    'provider_type'=>$data['patientProvider'][1]['ProviderType']);
              
               
               if(!$referInfo){	
                   $response=array(
                   'LogID'=>$LogID,
                   'code'=>'103',
                   'response'=>'Invalid parameter: Please check referral data'.$referInfo,
                   'date'=>date('m/d/Y H:i:s'));
                   $model->insertLog($response);
                   return  json_encode($response);
               }else if(!$patientInfo){
                   $response=array(
                   'LogID'=>$LogID,
                   'code'=>'103',	
                   'response'=>'Invalid parameter: Please check patient data'.$patientInfo,
                   'date'=>date('m/d/Y H:i:s'));
                   $model->insertLog('referral_logs',$response);
                   return  json_encode($response);
               }else if(!$patientDemo){
                   $response=array(
                   'LogID'=>$LogID,
                   'code'=>'103',
                   'response'=>'Invalid parameter: Please check  demographic data'.$patientDemo,
                   'date'=>date('m/d/Y H:i:s'));
                   $model->insertLog($response);
                   return  json_encode($response);
               }else if(!$patientClinic){
                   $response=array(
                   'LogID'=>$LogID,
                   'code'=>'103',
                   'response'=>'Invalid parameter: Please check clinical data!'.$patientClinic,
                   'date'=>date('m/d/Y H:i:s'));
                   $model->insertLog($response);
                   return json_encode($response);
                   
               }else if(!$data['patientProvider']){
                   $response=array(
                   'LogID'=>$LogID,
                   'code'=>'103',
                   'response'=>'Invalid parameter: Please check provider data!'.json_encode($data),
                   'date'=>date('m/d/Y H:i:s'));
                   $model->insertLog($response);
               }else{
                    $param = array(
                    'info'=>$referInfo,
                    'patient'=>$patientInfo,
                    'demo'=>$patientDemo,
                    'clinic'=>$patientClinic,
                    'refer'=>$provRefer,
                    'consu'=>$provConsu,
                );
               
                 $trans = $model->referralTransaction($param);
               if($trans['code'] == '200'){ 
                    $response=array(
                       'LogID'=>$LogID,
                       'code'=>$trans['code'],
                       'message'=>$trans['message'],
                       'response'=>'Referral code:'."=".$LogID."|"."Patient name:".$data['patientLastName']." ".$data['patientFirstName']." ".$data['patientMiddlename'],
                       'date'=>date('m/d/Y H:i:s'));
                       return  json_encode($response);
                    }else{
                        $response=array(
                            'code'=>$trans['code'],
                            'message'=>$trans['message'],
                            'date'=>date('m/d/Y H:i:s'));
                            return  json_encode($response);
                    }
                
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

    if ( !function_exists('getReferralData') ) {
        function getReferralData($transmitData)
        {
            $model = new \App\Models\ReferralModel;
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
			$data= json_decode($input,true);

			if(empty($data['LogID'])){
				$response=array(
				'code'=>"403",
				'message'=>"Invalid LogID!");
				return json_encode($response);

           }else if(empty($data['fhudcode'])){

				$response=array(
				'code'=>"403",
				'message'=>"Invalid Facility Code!");
				return  json_encode($response);
		   }else{
			
			$patientInfo= $model->getPatientInfo($data['LogID'],$data['fhudcode']);
			$clinicalInfo= $model->getClinicalInfo($data['LogID']);
			$providerInfo= $model->getReferProvider($data['LogID']);
	
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

if ( !function_exists('getReferralFhud') ) {
function getReferralFhud($hfhudcode)
		{
            $model = new \App\Models\ReferralModel;
			$info=$model->getReferralfhud($hfhudcode);
          
			$response= [];
			foreach($info as $patientInfo)
			{
				$providerID=$model->getReferProvider($patientInfo['LogID']);
				$clinicalInfo=$model->getClinicalInfo($patientInfo['LogID']);
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

    if ( !function_exists('generateReferCode') ) {
        function generateReferCode($fhudcode)
        {
            $model = new \App\Models\ReferralModel;
            $type =  $model->type($fhudcode)->facility_type;
            if($type == '4' || $type == '1'){
                $code  ='HOSP-';
                $code .= $model->maxID()+1;
                $code .= date('mdyhis');
                return  $code =str_pad($code,6,0, STR_PAD_LEFT);
            }else if($type == '17'){
                $code  ='RHU-';
                $code .= $model->maxID()+1;
                $code .= date('mdyhis');
                return  $code =str_pad($code,6,0, STR_PAD_LEFT);
            }else if($type == '15'){
                $code  ='BiHo-';
                $code .= $model->maxID()+1;
                $code .= date('mdyhis');
                return  $code =str_pad($code,6,0, STR_PAD_LEFT);
            }else if($type == '19'){
                $code  ='MHO-';
                $code .= $model->maxID()+1;
                $code .= date('mdyhis');
                return  $code =str_pad($code,6,0, STR_PAD_LEFT);
            }else if($type == '21'){
                $code  ='PHO-';
                $code .= $model->maxID()+1;
                $code .= date('mdyhis');
                return  $code =str_pad($code,6,0, STR_PAD_LEFT);
            }else{
                return  0;
            }
        }
    }

    
    if ( !function_exists('generateRandomString') ) {
        function generateRandomString($length = 5) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }   
    }
        
   




  

?>