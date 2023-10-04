<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\RawSql;

class ReferralModel extends Model
{
    protected $info = 'referral_information';
    protected $clinic = 'referral_clinical';
    protected $logs = 'referral_logs';
    protected $demographic = 'referral_patientdemo';
    protected $patient = 'referral_patientinfo';
    protected $provider = 'referral_provider';
    protected $return = 'referral_return';
    protected $track = 'referral_track';
    protected $med = 'referral_medicine';
	protected $followup = 'referral_followup';
	protected $meds = 'referral_medicine';
	protected $response =[];
	function existLog($value)
	{
		$sql = $this->db->table($this->info);
        $sql->select('COUNT(LogID) as count');
        $sql->where('referral_information.LogID' ,$value);
		return $query = $sql->get()->getRow();
	}

	public function onlineFacilities($region)
	{
		$query = $this->db->query('
		SELECT
			facility_name,online_status
		FROM 
			ref_facilities
		WHERE
			region_code = "'.$region.'"');
		return $query->getResultArray();
	}

	public  function maxID()
	{
		$sql = $this->db->table($this->info);
        $sql->selectMax('logDate');
		return $query = $sql->get()->getRow();
	}

	public  function type($hfhudcode)
	{
		$sql = $this->db->table('ref_facilities');
        $sql->select('facility_type');
		$sql->where('ref_facilities.hfhudcode' ,$hfhudcode);
		return $query = $sql->get()->getRow();
	}
	
     public function getReferralList()
     {
        $sql = $this->db->table($this->info);
        $sql->select('*');
        $sql->join($this->track, 'referral_information.LogID = referral_track.LogID','LEFT');
        return $query = $sql->get()->getResultArray();
     }

	 public function getdischargeInformation($LogID)
     {
		try {
			$this->db->transBegin();
				$record = $this->db->table($this->track);
				$record->select('referral_track.*');
				$record->where('referral_track.LogID',$LogID);
				$record->where('referral_track.dischDate is NOT NULL', NULL, FALSE);
				$record->where('referral_track.admDate is NOT NULL', NULL, FALSE);
			 $record_query = $record->get()->getRow();
			 $record_count = $record->countAllResults();
			if(!$record) throw new Exception($this->db->_error_message(), $this->db->_error_number());


			if($record_count === 0){
				return  $this->response = "No record found!";
			}else{
				
				$result_record= array(
					'LogID' =>$record_query->LogID,
					'admDateTime' => date('m/d/Y H:i:s',strtotime($record_query->admDate)),
					'dischDateTime' => date('m/d/Y H:i:s',strtotime($record_query->dischDate)),
					'diagnosis' => ($record_query->diagnosis == null)? "Diagnosis not specified" :$record_query->diagnosis,
					'dischDisp' => $record_query->dischDisp,
					'dischCond' => $record_query->dischCond,
					'disnotes' => $record_query->disnotes,
					'hasFollowUp' => $record_query->hasFollowup,
					'hasMedicine' => $record_query->hasMedicine,
					'remarks' =>  ($record_query->trackRemarks == null)? "" : $record_query->trackRemarks);
			
				if($record_query->hasFollowup === 'Y'){
					$schedule = $this->db->table($this->followup);
					$schedule->select('scheduleDateTime');
					$schedule->where('LogID',$LogID);
					$schedule_query = $schedule->get()->getRow();
				}

				if($record_query->hasMedicine === 'Y'){
					$med = $this->db->table($this->meds);
					$med->select('drugcode,generic,instruction');
					$med->where('LogID',$LogID);
					$med_query = $med->get()->getResultArray();
				}

				return $this->response = array(
					'dischargeData'=>$result_record,
					'drugs'=>$med_query,
					'schedule'=>$schedule_query);

			}

			if ($this->db->transStatus() === false) {
				$this->db->transRollback();
				return $response=array(
					'code'=>'500',
					'message'=>'Error on Database!');
			} else {
				$this->db->transCommit();
				return  $this->response;
			}	
		}catch (\Exception $e) {
			$this->db->transRollback();
			return $response=array(
				  'code'=>$e->getCode(),
				  'message'=>$e->getMessage());
		  log_message('error', sprintf('%s : %s : DB transaction failed. Error no: %s, Error msg:%s, Last query: %s', __CLASS__, __FUNCTION__, $e->getCode(), $e->getMessage(), print_r($this->main_db->last_query(), TRUE)));
		}
     }
	
	
	public function referralTransaction($param)
	{
		try {
			$this->db->transBegin();
				$insInfo = $this->db->table($this->info);
					$insInfo->insert($param['info']);
					if(!$insInfo) throw new Exception($this->db->_error_message(), $this->db->_error_number());
				$insPat = $this->db->table($this->patient);
					$insPat->insert($param['patient']);
					if(!$insPat) throw new Exception($this->db->_error_message(), $this->db->_error_number());
				$insDemo = $this->db->table($this->demographic);
					$insDemo->insert($param['demo']);
					if(!$insDemo) throw new Exception($this->db->_error_message(), $this->db->_error_number());
				$insClinic = $this->db->table($this->clinic);	
					$insClinic->insert($param['clinic']);
					if(!$insClinic) throw new Exception($this->db->_error_message(), $this->db->_error_number());
				$insCons = $this->db->table($this->provider);
					$insCons->insert($param['consu']);
					if(!$insCons) throw new Exception($this->db->_error_message(), $this->db->_error_number());
				$insRefer = $this->db->table($this->provider);
					$insRefer->insert($param['refer']);
					if(!$insRefer) throw new Exception($this->db->_error_message(), $this->db->_error_number());
			if ($this->db->transStatus() === false) {
				$this->db->transRollback();
				return $response=array(
					'code'=>'500',
					'message'=>'Failed!');
			} else {
				$this->db->transCommit();
				return $response=array(
					'code'=>'200',
					'message'=>'Success!');
			}	
		}catch (\Exception $e) {
			$this->db->transRollback();
			 
			  return $response=array(
					'code'=>$e->getCode(),
					'message'=>$e->getMessage());
			log_message('error', sprintf('%s : %s : DB transaction failed. Error no: %s, Error msg:%s, Last query: %s', __CLASS__, __FUNCTION__, $e->getCode(), $e->getMessage(), print_r($this->main_db->last_query(), TRUE)));
		}
	}
	
	public  function dischargeTransaction($param)
	{
		try {
			$this->db->transBegin();
				$insDisch = $this->db->table($this->track);
				$insDisch->where('LogID', $param['LogID']);
				$insDisch->update($param['discharge']);
			if(!$insDisch) throw new Exception($this->db->_error_message(), $this->db->_error_number());
				if($param['discharge']['hasFollowUp']=='Y')
				{
						$insertFollowup = $this->db->table($this->followup);
						$insertFollowup->insert($param['followup']);
						if(!$insertFollowup) throw new Exception($this->db->_error_message(), $this->db->_error_number());
				}
				if($param['discharge']['hasMedicine']=='Y')
				{
						$insertMedicine = $this->db->table($this->meds);
						$insertMedicine->insertBatch($param['medicine']);
						if(!$insertMedicine) throw new Exception($this->db->_error_message(), $this->db->_error_number());
				}
			if ($this->db->transStatus() === false) {
				$this->db->transRollback();
				return $response=array(
					'code'=>'500',
					'message'=>'Failed!');
			} else {
				$this->db->transCommit();
				return $response=array(
					'code'=>'200',
					'message'=>'Success!');
			}	
		}catch (\Exception $e) {
			$this->db->transRollback();
			 
			  return $response=array(
					'code'=>$e->getCode(),
					'message'=>$e->getMessage());
			log_message('error', sprintf('%s : %s : DB transaction failed. Error no: %s, Error msg:%s, Last query: %s', __CLASS__, __FUNCTION__, $e->getCode(), $e->getMessage(), print_r($this->main_db->last_query(), TRUE)));
		}

	}

	

	public function insertLog($data)
	{
		$sql = $this->db->table($this->logs);
		return $sql->insert($data);
	}

	public function getPatientInfo($LogID,$fhudcode)
	{
		$sql = $this->db->table($this->info);
		$sql->select('
				referral_information.fhudFrom,
				referral_information.fhudto,
				referral_information.typeOfReferral,
				referral_information.referralReason,
				referral_information.otherReasons,
				referral_information.remarks,
				referral_information.referralContactPerson,
				referral_information.referralContactPersonDesignation,
				referral_information.rprhreferral,
				referral_information.rprhreferralmethod,
				referral_information.status,
				referral_information.refferalDate,
				referral_information.refferalTime,
				referral_information.referralCategory,
				referral_information.referringProviderContactNumber,
				referral_information.referringProvider,
				referral_patientinfo.FamilyID,
				referral_patientinfo.phicNum,
				referral_patientinfo.patientLastName,
				referral_patientinfo.patientFirstName,
				referral_patientinfo.patientMiddlename,
				referral_patientinfo.patientSuffix,
				referral_patientinfo.patientBirthDate,
				referral_patientinfo.patientSex,
				referral_patientinfo.patientCivilStatus,
				referral_patientinfo.patientReligion,
				referral_patientinfo.patientBloodType,
				referral_patientinfo.patientBloodTypeRH,
				referral_patientdemo.patientStreetAddress,
				referral_patientdemo.patientBrgyCode as patientBrgyAddress,
				referral_patientdemo.patientMundCode as patientMunAddress,
				referral_patientdemo.patientProvCode as patientProvAddress,
				referral_patientdemo.patientRegCode as patientRegAddress,
				referral_patientdemo.patientZipCode as patientZipAddress,
				(select CONCAT(ref_facilities.facility_name,",",ref_facilities.hfhudcode)  from ref_facilities  where  ref_facilities.hfhudcode =referral_information.fhudFrom) as intra,
				(select CONCAT(ref_facilities.facility_name,",",ref_facilities.hfhudcode) from ref_facilities where ref_facilities.hfhudcode =referral_information.fhudTo) as inter');
				$sql->join('referral_patientinfo','referral_information.LogID=referral_patientinfo.LogID','left');
				$sql->join('referral_patientdemo','referral_patientdemo.LogID=referral_patientinfo.LogID','left');
				$sql->where('referral_information.LogID' ,$LogID);	
				$sql->where('referral_information.fhudTo' ,$fhudcode);	
		 return $sql->get()->getRow();
	}

	public  function getClinicalInfo($LogID)
	{
        $sql = $this->db->table($this->clinic);
        $sql->select('
            referral_clinical.clinicalDiagnosis,
            referral_clinical.clinicalHistory,
            referral_clinical.vitals as vitalSign,
            referral_clinical.physicalExamination,
            referral_clinical.chiefComplaint,
            referral_clinical.findings');
        $sql->where('referral_clinical.LogID',$LogID);
	    return $query = $sql->get()->getRow();
	}

	public  function getReferProvider($LogID)
	{	
		$sql = $this->db->table($this->provider);
		$sql->select('
				referral_provider.provider_last as ProviderLast,
				referral_provider.provider_first as ProviderFirst,
				referral_provider.provider_middle as ProviderMiddle,
				referral_provider.provider_suffix as ProviderSuffix,
				referral_provider.provider_contact as ProviderContactNo,
				referral_provider.provider_type as ProviderType');
	   $sql->join('referral_information','referral_provider.LogID=referral_information.LogID','left');
	   $sql->where('referral_information.LogID',$LogID);	
		return $sql->get()->getResultArray();
	}

	public  function checkFacility($fhudcode)
	{	
		$sql =$this->db->table('ref_facilities')->selectCount('hfhudcode')->where('hfhudcode',$fhudcode);
		return $sql->get()->getRow()->hfhudcode;
	}

	public  function getReferralfhud($fhudcode)
	{
		$sql = $this->db->table($this->info);
		$sql->select('
			referral_information.LogID,
			referral_information.fhudFrom,
			referral_information.fhudto,
			referral_information.typeOfReferral,
			referral_information.referralReason,
			referral_information.otherReasons,
			referral_information.remarks,
			referral_information.referralContactPerson,
			referral_information.referralContactPersonDesignation,
			referral_information.rprhreferral,
			referral_information.rprhreferralmethod, 
			referral_information.status,
			referral_information.refferalDate,
			referral_information.refferalTime,
			referral_information.referralCategory,
			referral_information.referringProviderContactNumber,
			referral_information.referringProvider,
			referral_patientinfo.FamilyID,
			referral_patientinfo.phicNum,
			referral_patientinfo.patientLastName,
			referral_patientinfo.patientFirstName,
			referral_patientinfo.patientMiddlename,
			referral_patientinfo.patientSuffix,
			referral_patientinfo.patientBirthDate,
			referral_patientinfo.patientSex,
			referral_patientinfo.patientCivilStatus,
			referral_patientinfo.patientReligion,
			referral_patientinfo.patientBloodType,
			referral_patientinfo.patientBloodTypeRH,
			referral_patientinfo.patientContactNumber,
			referral_patientdemo.patientStreetAddress,
			referral_patientdemo.patientBrgyCode as patientBrgyAddress,
			referral_patientdemo.patientMundCode as patientMunAddress,
			referral_patientdemo.patientProvCode as patientProvAddress,
			referral_patientdemo.patientRegCode as patientRegAddress,
			referral_patientdemo.patientZipCode as patientZipAddress');
		$sql->join('referral_patientinfo','referral_information.LogID=referral_patientinfo.LogID','left');
		$sql->join('referral_patientdemo','referral_patientdemo.LogID=referral_patientinfo.LogID','left');
		$sql->join('referral_track','referral_track.LogID=referral_patientinfo.LogID','left');
		$sql->where('referral_track.receivedDate IS NULL');
		$sql->where('referral_information.fhudTo' ,$fhudcode);	
		return $sql->get()->getResultArray();
	}

	public function insertTrack($data)
	{
		$sql = $this->db->table($this->track);
		return $sql->insert($data);
	}

	public function admiPatient($id,$param)
	{
		$sql = $this->db->table($this->track);
		$sql->set('admDate',$param['date'], false);
		$sql->set('admDisp',$param['disp'], false);
		$sql->where('LogID', $id);
		return $sql->update();
	}

	public function dischargePatient($id,$param)
	{
		$sql = $this->db->table($this->track);
		$sql->where('LogID', $id);
		return $sql->update($data);
	}

	public function insertFollowUp($data)
	{
		$sql = $this->db->table($this->followup);
		return $sql->insert($data);
	}

	public function insertMedicine($data)
	{
		$sql = $this->db->table($this->meds);
		return $sql->insert($data);
	}
	
	
	

	//OLD  CODES
	
	



    public function referUpdate($LogID,$data)
	{
        $sql = $this->db->table($this->track);
		$sql->where('LogID',$LogID);
		$sql->update('referral_track',$data);
		return ($sql->affectedRows() != 1) ? false : true;
	}
	
	
	public function referralInfo($LogID)
	{
		$query = $this->db->query('
				referral_information.fhudFrom,
				referral_information.fhudto,
				referral_information.typeOfReferral,
				referral_information.referralReason,
				referral_information.otherReasons,
				referral_information.remarks,
				referral_information.referralContactPerson,
				referral_information.referralContactPersonDesignation,
				referral_information.rprhreferral,
				referral_information.rprhreferralmethod,
				referral_information.status,
				referral_information.refferalDate,
				referral_information.refferalTime,
				referral_information.referralCategory,
				referral_information.referringProviderContactNumber,
				referral_information.referringProvider,
				referral_patientinfo.FamilyID,
				referral_patientinfo.phicNum,
				referral_patientinfo.patientLastName,
				referral_patientinfo.patientFirstName,
				referral_patientinfo.patientMiddlename,
				referral_patientinfo.patientSuffix,
				referral_patientinfo.patientBirthDate,
				referral_patientinfo.patientSex,
				referral_patientinfo.patientCivilStatus,
				referral_patientinfo.patientReligion,
				referral_patientinfo.patientBloodType,
				referral_patientinfo.patientBloodTypeRH,
				referral_patientdemo.patientStreetAddress,
				referral_patientdemo.patientBrgyCode as patientBrgyAddress,
				referral_patientdemo.patientMundCode as patientMunAddress,
				referral_patientdemo.patientProvCode as patientProvAddress,
				referral_patientdemo.patientRegCode as patientRegAddress,
				referral_patientdemo.patientZipCode as patientZipAddress
		 FROM
				referral_information
				LEFT  JOIN referral_patientinfo ON referral_information.LogID=referral_patientinfo.LogID
				LEFT  JOIN referral_patientdemo ON referral_patientdemo.LogID=referral_patientinfo.LogID
				WHERE 
		 referral_information.LogID = "'.$LogID.'"');
		 return  $query->getRow();
	}
	
	
	
	public function checkPatient(array $ids=[])
	{
		$sql = $this->db->table($this->patient);
		$sql->where($ids);
		$rowCount= $sql->get()->getNumRows();
		return 	$rowCount > 0 ? true : false;
	}

	
	
	public  function checkReferral($fhudcode,$casenum)
	{
		$this->db->where('fhudFrom','A');
		$this->db->where('status','A');
		return $this->db->get('referral_information');
	}
	                                
	public function referralReceive($LogID,$data)
	{
		$this->db->where('LogID',$LogID);
		$this->db->update('referral_track',$data);
		return ($this->db->affected_rows() != 1) ? false : true;
	}
	
	public function updateInfo($LogID,$data)
	{
		$this->db->where('LogID',$LogID);
		$this->db->update('referral_patientinfo');
		return ($this->db->affected_rows() != 1) ? false : true;
	}
	
	public function referralRefer($LogID,$data)
	{
		$this->db->where('LogID',$LogID);
		$this->db->update('referral_track',$data);
		return ($this->db->affected_rows() != 1) ? false : true;
	}
	 
	public  function checkRefer()
	{
		$this->db->select('*');
		$this->db->where('status','A');
		$query = $this->db->get('referral_information');
		return $num = $query->num_rows();
	}

	
	function existsReturn($value)
	{
		$query = $this->db->query('
		SELECT COUNT(*) AS count FROM referral_return where referral_return.LogID ="'.$value.'"');
		$row = $query->getRow();
		return ($row->count > 0) ? 1 : 0;
	}
	
	function deleteall()
	{
		$this->db->delete('referral_clinical');
		$this->db->delete('referral_information');
		$this->db->delete('referral_logs');
		$this->db->delete('referral_patientdemo');
		$this->db->delete('referral_patientinfo');
		$this->db->delete('referral_track');
	}
	

	
	function updateOnline($code,$status)
	{	
		$this->db->where('hfhudcode',$code);
		$this->db->update('ref_facilities');
		$this->db->set('online_status',$status);
		return ($this->db->affected_rows() != 1) ? false : true;
	}

	public  function checkReferralToExist($ids)
	{	
		$query = $this->db->table('referral_patientinfo');
		$query->select('referral_information.logid');
		$query->join('referral_information', 'referral_information.logid = referral_patientinfo.logid','inner');
		$query->where($ids);		
		return $query->get()->getRow();
	}
    
}
