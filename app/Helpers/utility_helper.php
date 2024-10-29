<?php
if ( !function_exists('receive') ) {
    function receive($transmitData) 
    { 
        try 
        {
            $model = new \App\Models\ReferralModel;
            
            // Ensure proper UTF-8 encoding
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data = json_decode($input, true);

            // Validate that LogID is set, numeric, and non-empty
            if(empty($data['LogID'])){
                $response = array(
                    'code' => '403',
                    'response' => 'Reference ID is Invalid, must be numeric and non-empty',
                    'date' => date('m/d/Y H:i:s')
                );
                return json_encode($response);
            }

            // Validate receivedDate is set and is a valid date
            if(empty($data['receivedDate'])){
                $response = array(
                    'code' => '403',
                    'response' => 'Received date is Invalid, must be a valid date',
                    'date' => date('m/d/Y H:i:s')
                );
                return json_encode($response);
            }

            // Validate receivedPerson is set, non-empty, and not just whitespace
            if(empty($data['receivedPerson']) || trim($data['receivedPerson']) === ''){
                $response = array(
                    'code' => '403',
                    'response' => 'Received person is Invalid, must not be empty',
                    'date' => date('m/d/Y H:i:s')
                );
                return json_encode($response);
            }

            // Prepare data for insertion
            $param  = array(
                'LogID' => $data['LogID'],
                'receivedDate' => date("Y-m-d H:i:s", strtotime($data['receivedDate'])),
                'receivedPerson' => $data['receivedPerson']
            );

            // Insert the data into the model
            $model->insertTrack($param);

            // Return success response
            $response = array(
                'LogID' => $data['LogID'],
                'code' => '200',
                'response' => 'Patient received',
                'date' => date('m/d/Y H:i:s')
            );
            return json_encode($response);
        } 
        catch (Exception $exception) 
        {
            return json_encode(array(
                'code' => '500',
                'response' => 'An error occurred: ' . $exception->getMessage(),
                'date' => date('m/d/Y H:i:s')
            ));
        } 
    }
}
if ( !function_exists('admit') ) {
    function admit($transmitData) 
    { 
        try 
        {
            $model = new \App\Models\ReferralModel;
            
            // Ensure proper UTF-8 encoding
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data = json_decode($input, true);

            // Validate that LogID is set, numeric, and non-empty
            if (empty($data['LogID'])) {
                $response = array(
                    'code' => '403',
                    'response' => 'Reference ID is Invalid, must be numeric and non-empty',
                    'date' => date('m/d/Y H:i:s')
                );
                return json_encode($response);
            }

            // Validate 'date' field is set and is a valid date
            if (empty($data['date'])) {
                $response = array(
                    'code' => '403',
                    'response' => 'Admit date is Invalid, must be a valid date',
                    'date' => date('m/d/Y H:i:s')
                );
                return json_encode($response);
            }

            // Validate 'disp' (disposition) field is set, non-empty, and not just whitespace
            if (empty($data['disp']) || trim($data['disp']) === '') {
                $response = array(
                    'code' => '403',
                    'response' => 'Admit disposition is Invalid, must not be empty',
                    'date' => date('m/d/Y H:i:s')
                );
                return json_encode($response);
            }

            // Prepare data for admission
            $param = array(
                'LogID' => $data['LogID'],
                'admDate' => date("Y-m-d H:i:s", strtotime($data['date'])),
                'admDisp' => $data['disp']
            );

            // Call model method to admit the patient
            $model->admiPatient($data['LogID'], $param);

            // Return success response
            $response = array(
                'LogID' => $data['LogID'],
                'code' => '200',
                'response' => 'Patient is now admitted',
                'date' => date('m/d/Y H:i:s')
            );
            return json_encode($response);
        } 
        catch (Exception $exception) 
        {
            // Handle any exceptions
            return json_encode(array(
                'code' => '500',
                'response' => 'An error occurred: ' . $exception->getMessage(),
                'date' => date('m/d/Y H:i:s')
            ));
        }
    }
}


if ( !function_exists('getDischargeData') ) {
    function getDischargeData($transmitData) {
        try {
            $model = new \App\Models\ReferralModel;
            
            // Ensure proper UTF-8 encoding
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data = json_decode($input, true);

            // Validate that LogID is set, numeric, and non-empty
            if (empty($data['LogID'])) {
                $response = array(
                    'code' => '403',
                    'response' => 'Reference ID is Invalid, must be numeric and non-empty',
                    'date' => date('m/d/Y H:i:s')
                );
                return json_encode($response);
            }

            // Fetch discharge data from model
            $dischargeData = $model->getdischargeInformation($data['LogID']);

            // Check if data was successfully retrieved
            if (empty($dischargeData)) {
                $response = array(
                    'code' => '404',
                    'response' => 'No discharge data found for the given LogID',
                    'date' => date('m/d/Y H:i:s')
                );
                return json_encode($response);
            }

            // Return the retrieved discharge data
            return json_encode(array(
                'code' => '200',
                'data' => $dischargeData,
                'date' => date('m/d/Y H:i:s')
            ));
        } 
        catch (Exception $exception) {
            // Handle any exceptions that occur during the process
            return json_encode(array(
                'code' => '500',
                'response' => 'An error occurred: ' . $exception->getMessage(),
                'date' => date('m/d/Y H:i:s')
            ));
        }
    }
}

if ( !function_exists('discharge') ) {
    function discharge($transmitData) 
    { 
        try 
        {
            $model = new \App\Models\ReferralModel;

            // Ensure proper UTF-8 encoding
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($transmitData));
            $data = json_decode($input, true);

            // Validate LogID
            if (empty($data['LogID'])) {
                return json_encode(array(
                    'code' => '403',
                    'response' => 'Reference ID is Invalid or null',
                    'date' => date('m/d/Y H:i:s')
                ));
            }

            // Validate Admission Date
            if (empty($data['admDate'])) {
                return json_encode(array(
                    'code' => '403',
                    'response' => 'Admission date is required and must be valid!',
                    'date' => date('m/d/Y H:i:s')
                ));
            }

            // Validate Discharge Date
            if (empty($data['dischDate'])) {
                return json_encode(array(
                    'code' => '403',
                    'response' => 'Discharge date is required and must be valid!',
                    'date' => date('m/d/Y H:i:s')
                ));
            }

            // Validate Disposition
            if (empty($data['disposition'])) {
                return json_encode(array(
                    'code' => '403',
                    'response' => 'Disposition is Invalid or null',
                    'date' => date('m/d/Y H:i:s')
                ));
            }

            // Validate Condition
            if (empty($data['condition'])) {
                return json_encode(array(
                    'code' => '403',
                    'response' => 'Condition is Invalid or null',
                    'date' => date('m/d/Y H:i:s')
                ));
            }

            // Validate Follow Up Status
            if (empty($data['hasFollowUp'])) {
                return json_encode(array(
                    'code' => '403',
                    'response' => 'Follow Up status is required!',
                    'data' => 'Y if Yes & N for No',
                    'date' => date('m/d/Y H:i:s')
                ));
            }

            // Validate Medicine Status
            if (empty($data['hasMedicine'])) {
                return json_encode(array(
                    'code' => '403',
                    'response' => 'Medicine status is required!',
                    'data' => 'Y if Yes & N for No',
                    'date' => date('m/d/Y H:i:s')
                ));
            }

            // Validate Diagnosis
            if (empty($data['diagnosis'])) {
                return json_encode(array(
                    'code' => '403',
                    'response' => 'Diagnosis is required!',
                    'data' => 'text format only',
                    'date' => date('m/d/Y H:i:s')
                ));
            }

            // Prepare discharge information
            $discharge = array(
                'LogID' => $data['LogID'],
                'admDate' => date("Y-m-d H:i:s", strtotime($data['admDate'])),
                'dischDate' => date("Y-m-d H:i:s", strtotime($data['dischDate'])),
                'dischDisp' => $data['disposition'],
                'dischCond' => $data['condition'],
                'diagnosis' => $data['diagnosis'],
                'trackRemarks' => $data['remarks'] ?? '',  // Optional field
                'disnotes' => $data['disnotes'] ?? '',      // Optional field
                'hasFollowUp' => $data['hasFollowUp'],
                'hasMedicine' => $data['hasMedicine']
            );

            // Prepare follow-up information if available
            $folUp = array();
            if (isset($data['schedule']['LogID']) && isset($data['schedule']['date']) && strtotime($data['schedule']['date'])) {
                $folUp = array(
                    'LogID' => $data['schedule']['LogID'],
                    'scheduleDateTime' => date("Y-m-d H:i:s", strtotime($data['schedule']['date']))
                );
            }

            // Prepare parameters for discharge transaction
            $param = array(
                'LogID' => $data['LogID'],
                'discharge' => $discharge,
                'medicine' => $data['drugs'] ?? '',  // Medicine can be optional
                'followup' => !empty($folUp) ? $folUp : null
            );

            // Call discharge transaction method
            $trans = $model->dischargeTransaction($param);

            // Check if the transaction was successful
            if ($trans['code'] == '200') { 
                return json_encode(array(
                    'LogID' => $data['LogID'],
                    'code' => $trans['code'],
                    'message' => $trans['message'],
                    'date' => date('m/d/Y H:i:s')
                ));
            } else {
                return json_encode(array(
                    'code' => $trans['code'],
                    'message' => $trans['message'],
                    'date' => date('m/d/Y H:i:s')
                ));
            } 
        } 
        catch (Exception $exception) 
        {
            // Handle any exceptions and return error response
            return json_encode(array(
                'code' => '500',
                'response' => 'An error occurred: ' . $exception->getMessage(),
                'date' => date('m/d/Y H:i:s')
            ));
        } 
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
   
    $data = json_decode($input, true);
  

    // Basic validations
    $requiredFields = [
        'fhudFrom', 'fhudto', 'patientLastName', 'patientFirstName', 
        'patientBirthDate', 'patientSex', 'referralDate', 'referralTime', 
        'typeOfReferral'
    ];

    foreach ($requiredFields as $field) {
        if (empty(trim($data[$field]))) {
            $response = [
                'code' => '400',
                'response' => "Missing required field: $field",
                'date' => date('m/d/Y H:i:s')
            ];
            return json_encode($response);
        }
    }

    // Validate sex
    if (!in_array($data['patientSex'], ['M', 'F'])) {
        $response = [
            'code' => '400',
            'response' => "Invalid value for patientSex. Expected 'M' or 'F'.",
            'date' => date('m/d/Y H:i:s')
        ];
        return json_encode($response);
    }

    // Check facility existence
    if ($model->checkFacility(trim($data['fhudFrom'])) != '1') {
        log_message("error", 'Referring Facility Not Exist');
        $response = [
            'code' => '403',
            'response' => 'Contact system administrator: referring facility does not exist.',
            'date' => date('m/d/Y H:i:s')
        ];
        return json_encode($response);
    }

    if ($model->checkFacility(trim($data['fhudto'])) != '1') {
        $response = [
            'code' => '403',
            'response' => 'Contact system administrator: referral facility does not exist.',
            'date' => date('m/d/Y H:i:s')
        ];
        return json_encode($response);
    }

    // Prepare referral data
    $referralToIDs = [
        "patientLastName" => $data['patientLastName'],
        "patientFirstName" => $data['patientFirstName'],
        "patientBirthDate" => date('Y-m-d', strtotime($data['patientBirthDate'])),
        "patientSex" => $data['patientSex'],
        "refferalDate" => $data['referralDate'],
        "refferalTime" => $data['referralTime'],
        "typeOfReferral" => $data['typeOfReferral'],
        "fhudFrom" => $data['fhudFrom'],
        "fhudTo" => $data['fhudto']
    ];

    // Check if referral already exists
      $referralToExist = $model->checkReferralToExist($referralToIDs);
    if ($referralToExist) {
    
        $response = [
            'LogID' => $referralToExist->LogID,
            'code' => "200",
            'message' => 'Referral already exists!',
            'response' => 'Referral code:=' . $referralToExist->LogID . "| Patient name:" . $data['patientLastName'] . " " . $data['patientFirstName'] . " " . $data['patientMiddlename'],
            'date' => date('m/d/Y H:i:s')
        ];
        return json_encode($response);
    }

    // Generate LogID and check its existence
     $LogID = generateReferCode($data['fhudFrom']);
    $check = $model->existLog($LogID)->count;
    if ($check == 0) {
        // Prepare data for insertion
        $referInfo = [
            'LogID' => $LogID,
            'fhudFrom' => $data['fhudFrom'], 
            'fhudTo' => $data['fhudto'],
            'typeOfReferral' => $data['typeOfReferral'],
            'referralReason' => $data['referralReason'] ?? '',
            'otherReasons' => $data['otherReasons'] ?? '',
            'patientPan' => $data['pan'] ?? '',
            'remarks' => $data['remarks'] ?? '',
            'referralContactPerson' => $data['referralContactPerson'] ?? '',
            'referringProviderContactNumber' => $data['referringContactNumber'] ?? '',
            'referralContactPersonDesignation' => $data['referralPersonDesignation'] ?? '',
            'rprhreferral' => $data['rprhreferral'] ?? '',
            'rprhreferralmethod' => $data['rprhreferralmethod'] ?? '',
            'status' => $data['status'] ?? '',
            'refferalDate' => $data['referralDate'],
            'refferalTime' => $data['referralTime'],
            'referralCategory' => $data['referralCategory'] ?? '',
            'logDate' => date('Y/m/d H:i:s')
        ];

        $patientInfo = array(
          
            'LogID' => isset($LogID) ? $LogID : null, 
            'FamilyID' => isset($data['familyNumber']) ? $data['familyNumber'] : null,
            'phicNum' => isset($data['phicNumber']) ? $data['phicNumber'] : null,
            'patientLastName' => isset($data['patientLastName']) ? $data['patientLastName'] : '',
            'patientFirstName' => isset($data['patientFirstName']) ? $data['patientFirstName'] : '',
            'patientSuffix' => isset($data['patientSuffix']) ? $data['patientSuffix'] : '',
            'patientMiddlename' => isset($data['patientMiddlename']) ? $data['patientMiddlename'] : '',
            'patientBirthDate' => isset($data['patientBirthDate']) ? $data['patientBirthDate'] : '',
            'patientSex' => isset($data['patientSex']) ? $data['patientSex'] : null,
            'patientContactNumber' => isset($data['patientContactNumber']) ? $data['patientContactNumber'] : null,
            'patientCivilStatus' => isset($data['patientCivilStatus']) ? $data['patientCivilStatus'] :null,
            'patientReligion' => isset($data['patientReligion']) ? $data['patientReligion'] : null,
            'patientBloodType' => isset($data['patientBloodType']) ? $data['patientBloodType'] : null,
            'patientBloodTypeRH' => isset($data['patientBloodTypeRH']) ? $data['patientBloodTypeRH'] : null
        );

        $patientDemo = array(
            'LogID' => isset($LogID) ? $LogID : null,
            'patientStreetAddress' => isset($data['patientStreetAddress']) ? $data['patientStreetAddress'] : '',
            'patientBrgyCode' => isset($data['patientBrgyAddress']) ? $data['patientBrgyAddress'] : '',
            'patientMundCode' => isset($data['patientMunAddress']) ? $data['patientMunAddress'] : '',
            'patientProvCode' => isset($data['patientProvAddress']) ? $data['patientProvAddress'] : '',
            'patientRegCode' => isset($data['patientRegAddress']) ? $data['patientRegAddress'] : '',
            'patientZipCode' => isset($data['patientZipAddress']) ? $data['patientZipAddress'] : ''
        );
     
        $patientClinic = array(
            'LogID' => isset($LogID) ? $LogID : null,
            'clinicalDiagnosis' => isset($data['clinicalDiagnosis']) ? $data['clinicalDiagnosis'] : '',
            'clinicalHistory' => isset($data['clinicalHistory']) ? $data['clinicalHistory'] : '',
            'physicalExamination' => isset($data['physicalExamination']) ? $data['physicalExamination'] : '',
            'chiefComplaint' => isset($data['chiefComplaint']) ? $data['chiefComplaint'] : '',
            'findings' => isset($data['findings']) ? $data['findings'] : '',
            'vitals' => isset($data['vitalSign']) ? $data['vitalSign'] : ''
        );
      
        
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
        // Insert the referral transaction
        
        $param = array(
            'info'=>$referInfo,
            'patient'=>$patientInfo,
            'demo'=>$patientDemo,
            'clinic'=>$patientClinic,
            'refer'=>$provRefer,
            'consu'=>$provConsu);
       
         $trans = $model->referralTransaction($param);
       
        if ($trans['code']==="200") { 
            $response = [
                'LogID' => $LogID,
                'code' => $trans['code'],
                'message' => $trans['message'],
                'response' => 'Referral code:=' . $LogID . "| Patient name:" . $data['patientLastName'] . " " . $data['patientFirstName'] . " " . $data['patientMiddlename'],
                'date' => date('m/d/Y H:i:s')
            ];
            return json_encode($response);
        } else {
            $response = [
                'code' => $trans['code'],
                'response' => $trans['message'],
                'date' => date('m/d/Y H:i:s')
            ];
            return json_encode($response);
        }
    } else {
        $response = [
            'LogID' => $LogID,
            'code' => '104',
            'response' => 'Referral already exists!',
            'date' => date('m/d/Y H:i:s')
        ];
        return json_encode($response);
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
			'referringContactNumber'=>($patientInfo->referringProviderContactNumber)?"": $patientInfo->referringProviderContactNumber,
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
			'patientPan'=>$patientInfo->patientPan,
			'findings'=>$clinicalInfo->findings,
			'vitalSign'=>stripslashes($clinicalInfo->vitalSign),
			'patientProvider'=>$providerInfo);
			
			return json_encode($response);
         
        }
    }
}
if (!function_exists('getReferralFhud')) {
    function getReferralFhud($hfhudcode)
    {
        // Validate the input
        if (empty($hfhudcode) || !is_string($hfhudcode)) {
            return json_encode(['error' => 'Invalid HFHUD code provided']);
        }

        $model = new \App\Models\ReferralModel;
        $response = ['data' => []];

        // Fetch referral data based on HFHUD code
        try {
            $info = $model->getReferralfhud($hfhudcode);
            
            foreach ($info as $patientInfo) {
                // Validate patient info fields
                if (empty($patientInfo['LogID']) || empty($patientInfo['fhudFrom']) || empty($patientInfo['fhudto'])) {
                    continue;  // Skip incomplete records
                }

                // Get the referring provider information
                $providerID = $model->getReferProvider($patientInfo['LogID']);
                $clinicalInfo = $model->getClinicalInfo($patientInfo['LogID']);

                // Validate the clinical information
                if (empty($clinicalInfo)) {
                    $clinicalInfo = (object) ['findings' => null, 'chiefComplaint' => null, 'clinicalDiagnosis' => null, 'physicalExamination' => null, 'clinicalHistory' => null, 'vitalSign' => null]; // Assign empty object if no clinical info found
                }

                // Prepare response for each referral entry
                $response['data'][] = [
                    'LogID' => $patientInfo['LogID'] ?? null,
                    'fhudFrom' => $patientInfo['fhudFrom'] ?? null,
                    'fhudto' => $patientInfo['fhudto'] ?? null,
                    'typeOfReferral' => $patientInfo['typeOfReferral'] ?? null,
                    'referralReason' => $patientInfo['referralReason'] ?? null,
                    'otherReasons' => $patientInfo['otherReasons'] ?? null,
                    'remarks' => $patientInfo['remarks'] ?? null,
                    'referralContactPerson' => $patientInfo['referralContactPerson'] ?? null,
                    'referralPersonDesignation' => $patientInfo['referralContactPersonDesignation'] ?? null,
                    'rprhreferral' => $patientInfo['referral'] ?? null,
                    'rprhreferralmethod' => $patientInfo['rprhreferralmethod'] ?? null,
                    'status' => $patientInfo['status'] ?? null,
                    'referringContactNumber' => $patientInfo['referringProviderContactNumber'] ?? null,
                    'referralDate' => $patientInfo['refferalDate'] ?? null,
                    'referralTime' => $patientInfo['refferalTime'] ?? null,
                    'referralCategory' => $patientInfo['referralCategory'] ?? null,
                    'familyNumber' => $patientInfo['FamilyID'] ?? null,
                    'phicNumber' => $patientInfo['phicNum'] ?? null,
                    'patientLastName' => $patientInfo['patientLastName'] ?? null,
                    'patientFirstName' => $patientInfo['patientFirstName'] ?? null,
                    'patientSuffix' => $patientInfo['patientSuffix'] ?? null,
                    'patientMiddlename' => $patientInfo['patientMiddlename'] ?? null,
                    'patientBirthDate' => $patientInfo['patientBirthDate'] ?? null,
                    'patientSex' => $patientInfo['patientSex'] ?? null,
                    'patientCivilStatus' => $patientInfo['patientCivilStatus'] ?? null,
                    'patientReligion' => $patientInfo['patientReligion'] ?? null,
                    'patientBloodType' => $patientInfo['patientBloodType'] ?? null,
                    'patientBlooTypeRH' => $patientInfo['patientBloodTypeRH'] ?? null,
                    'patientStreetAddress' => $patientInfo['patientStreetAddress'] ?? null,
                    'patientBrgyAddress' => $patientInfo['patientBrgyAddress'] ?? null,
                    'patientMunAddress' => $patientInfo['patientMunAddress'] ?? null,
                    'patientProvAddress' => $patientInfo['patientProvAddress'] ?? null,
                    'patientRegAddress' => $patientInfo['patientRegAddress'] ?? null,
                    'patientZipAddress' => $patientInfo['patientZipAddress'] ?? null,
                    'patientContactNumber' => $patientInfo['patientContactNumber'] ?? null,
                    'findings' => $clinicalInfo->findings ?? null,
                    'chiefComplaint' => $clinicalInfo->chiefComplaint ?? null,
                    'clinicalDiagnosis' => $clinicalInfo->clinicalDiagnosis ?? null,
                    'physicalExamination' => $clinicalInfo->physicalExamination ?? null,
                    'clinicalHistory' => $clinicalInfo->clinicalHistory ?? null,
                    'vitalSign' => stripslashes($clinicalInfo->vitalSign) ?? null,
                    'patientPan' => stripslashes($patientInfo['patientPan']) ?? null,
                    'patientProvider' => $providerID ?? null
                ];
            }
        } catch (\Exception $e) {
            return json_encode(array(
                'code' => '500',
                'response' => 'An error occurred: ' . $exception->getMessage(),
                'date' => date('m/d/Y H:i:s')
            ));
        }

        return json_encode($response);
    }
}

    if ( !function_exists('generateReferCode') ) {
        function generateReferCode($fhudcode)
        {
            $model = new \App\Models\ReferralModel;
            $facility = $model->type($fhudcode); // Get the facility object
        
            if (is_null($facility) || !isset($facility->facility_type)) {
                // Handle the case where $facility or facility_type is not valid
                return 0;
            }
        
            $type = $facility->facility_type;
        
            // Ensure maxID() returns a numeric value or a property of stdClass
            $maxIDObject = $model->maxID(); // Get the result, assuming it's an object
        
            // Check if the result is an object, and extract the property, like `id`
            if (is_object($maxIDObject) && isset($maxIDObject->id)) {
                $maxID = (int)$maxIDObject->id; // Extract the numeric value from the object
            } else if (is_numeric($maxIDObject)) {
                // If it's already numeric, cast it directly
                $maxID = (int)$maxIDObject;
            } else {
                // If there's no valid number, handle the error or return default value
                $maxID = 0;
            }
        
            // Now continue with generating the code
            if ($type == '4' || $type == '1') {
                $code = 'HOSP-';
                $code .= $maxID + 1;
                $code .= date('mdyhis');
                return str_pad($code, 6, 0, STR_PAD_LEFT);
            } else if ($type == '17') {
                $code = 'RHU-';
                $code .= $maxID + 1;
                $code .= date('mdyhis');
                return str_pad($code, 6, 0, STR_PAD_LEFT);
            } else if ($type == '15') {
                $code = 'BiHo-';
                $code .= $maxID + 1;
                $code .= date('mdyhis');
                return str_pad($code, 6, 0, STR_PAD_LEFT);
            } else if ($type == '19') {
                $code = 'MHO-';
                $code .= $maxID + 1;
                $code .= date('mdyhis');
                return str_pad($code, 6, 0, STR_PAD_LEFT);
            } else if ($type == '21') {
                $code = 'PHO-';
                $code .= $maxID + 1;
                $code .= date('mdyhis');
                return str_pad($code, 6, 0, STR_PAD_LEFT);
            } else {
                return 0;
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
        
   


    if ( !function_exists('wsCheck') ) {
        function wsCheck() 
        { 
            $current_date = date('d-m-Y H:i:s');
            $data=array(
            "Response"=>'Webservice Is Online',
            "DateTime"=>$current_date);
            return  json_encode($data);
        }

    }
  

?>