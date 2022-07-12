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
    protected $med = 'referra_medicine';


     public function getReferralList()
     {
        $sql = $this->db->table($this->info);
        $sql->select('*');
        $sql->join($this->track, 'referral_information.LogID = referral_track.LogID','LEFT');
        return $query = $sql->get()->getResultArray();
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
	    return $query = $sql->get()->getResultArray();
	}
	
    public function referUpdate($LogID,$data)
	{
        $sql = $this->db->table($this->track);
		$sql->where('LogID',$LogID);
		$sql->update('referral_track',$data);
		return ($sql->affectedRows() != 1) ? false : true;
	}

	public function getPatientInfo($LogID,$fhudcode)
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
				referral_patientinfo.caseNum,
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
				(select CONCAT(ref_facilities.facility_name,",",ref_facilities.hfhudcode) from ref_facilities where ref_facilities.hfhudcode =referral_information.fhudTo) as inter
			FROM
				referral_information
				LEFT  JOIN referral_patientinfo ON referral_information.LogID=referral_patientinfo.LogID
				LEFT  JOIN referral_patientdemo ON referral_patientdemo.LogID=referral_patientinfo.LogID
			WHERE
				referral_information.LogID = "'.$LogID.'"
				referral_information.fhudTo= "'.$fhudcode.'"');
		 return  $query->getRow();
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
				referral_patientinfo.caseNum,
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
	
	public  function getReferProvider($LogID)
	{	
		$query = $this->db->query('
				referral_provider.provider_last as ProviderLast,
				referral_provider.provider_first as ProviderFirst,
				referral_provider.provider_middle as ProviderMiddle,
				referral_provider.provider_suffix as ProviderSuffix,
				referral_provider.provider_contact as ProviderContactNo,
				referral_provider.provider_type as ProviderType
			FROM
				referral_provider
			INNER JOIN referral_provider.LogID=referral_information.LogID
			WHERE 
				referral_information.LogID= "'.$LogID.'"');
		return $query->getResultArray();
	}
	
	public  function getReferralfhud($fhudcode)
	{
		$query = $this->db->query('
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
			referral_patientinfo.caseNum,
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
			referral_patientdemo.patientZipCode as patientZipAddress
		 FROM
			 referral_information
			LEFT  JOIN referral_patientinfo ON referral_information.LogID=referral_patientinfo.LogID
			LEFT  JOIN referral_patientdemo ON referral_patientdemo.LogID=referral_patientinfo.LogID
		WHERE 
		referral_information.fhudTo = "'.$LogID.'"');
		return $query->getResultArray();
	}
	
	public  function getReferralTreatment($LogID)
	{
		 $this->db->from('referral_treatment');
		 $this->db->join('referral_patientinfo','referral_information.LogID=referral_treatment.LogID','inner');
		 $this->db->where('referral_treatment.LogID',$LogID);
		 return $this->db->get()->result_array();
	}
	
	public  function getReferralMedicine($LogID)
	{
		$this->db->from('refferal_medicine');
		 $this->db->join('referral_patientinfo','referral_information.LogID=refferal_medicine.LogID','inner');
		 $this->db->where('refferal_medicine.LogID',$LogID);
		 return $this->db->get()->result_array();
	}
	
	public  function checkReferral($fhudcode,$casenum)
	{
		$this->db->where('fhudFrom','A');
		$this->db->where('status','A');
		return $this->db->get('referral_information');
	}
	
	public function checkAccount($hfhudcode)
	{
		 $this->db->from('trn_engage_info');
		 $this->db->join('ref_system','trn_engage_info.system_id=ref_system.system_id','inner');
		 $this->db->join('ref_database','ref_database.db_id=ref_system.db_id','inner');
		 $this->db->where('trn_engage_info.hfhudcode',$hfhudcode);
		 return $this->db->get()->row();
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
	 
	 
	public  function checkRef()
	{
		$this->db->select('*');
		$this->db->where('status','R');
		$query = $this->db->get('referral_information');
		return $num = $query->num_rows();
	}
	
	
	public  function checkRefer()
	{
		$this->db->select('*');
		$this->db->where('status','A');
		$query = $this->db->get('referral_information');
		return $num = $query->num_rows();
	}
		
	function existLog($value)
	{
		$query = $this->db->query('
		SELECT COUNT(*) AS count FROM referral_information where referral_information.LogID =="'.$value.'"');
		$row = $query->getRow();
		return ($row->count > 0) ? 1 : 0;
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
	
	function updateOnline($code,$status)
	{	
		$this->db->where('hfhudcode',$code);
		$this->db->update('ref_facilities');
		$this->db->set('online_status',$status);
		return ($this->db->affected_rows() != 1) ? false : true;
	}


    
}
