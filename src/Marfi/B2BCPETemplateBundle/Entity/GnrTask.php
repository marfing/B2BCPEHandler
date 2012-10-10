<?php


namespace Marfi\B2BCPETemplateBundle\Entity;

class GnrTask{
	
	protected $rootNumber;
	protected $portListNames;
	protected $bind = false;
	protected $did = false;
	protected $gnrExtension = 0;


	public function getRootNumber(){ return $this->rootNumber; }
	public function setRootNumber($root){ $this->rootNumber= $root;}
	public function getPortList(){ return $this->portListNames; }
	public function setPortList($portList){ 	$this->portListNames = $portList; }
	public function getBind(){ return $this->bind; }
	public function setBind($bind){ $this->bind = $bind; }
	public function getDid(){ return $this->did; }
	public function setDid($did){ $this->did = $did; }
	public function getGnrExtension(){ return $this->gnrExtension; }
	public function setGnrExtension($ext){ $this->gnrExtension = $ext; }
	public function isBindOk(){
		if( ($this->bind && (count($this->portListNames) < 2)) || (!$this->bind && (count($this->portListNames))>1))
			return false;
		return true;
	}
	public function isRootNumberValid(){   /// not standards compliant i.e won't meet E.164 etc for validating international phone numbers
		trim($this->rootNumber);
		if(!preg_match("/^[0]\d{5,11}$/",$this->rootNumber)) return false;
		return true;
	}
	public function isDidOk(){
		if( ($this->did && ($this->gnrExtension == 0)) || (!$this->did && ($this->gnrExtension !=0)) )
			return false;
		return true;
	}
}





?>
