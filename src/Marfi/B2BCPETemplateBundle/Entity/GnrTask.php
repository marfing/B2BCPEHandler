<?php
namespace Marfi\B2BCPETemplateBundle\Entity;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;

class GnrTask{
	
	protected $rootNumber;
	protected $portListNames;
	protected $bind = false;
	protected $did = false;
	protected $gnrExtension = 0;
	protected $userXml;

	public function __construct(userXML $userxml){$this->userXml = $userxml;}
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
		if(!preg_match("/^[0]\d{4,11}$/",$this->rootNumber)) return false;
		return true;
	}
	public function isDidOk(){
		if( ($this->did && ($this->gnrExtension == 0)) || (!$this->did && ($this->gnrExtension !=0)) )
			return false;
		return true;
	}
	public function isExtensionOk(){return $this->gnrExtension<=4;}
	public function isCliAlreadyUsed(){
		for($i=1; $i<=$this->gnrExtension; $i++){
			$base = intval($this->rootNumber)*pow(10,$i);
			$finalnumber = $base + pow(10,$i);
			$counter = 1;
			for($j=$base; $j<$finalnumber;$j++){
				$cli = '0' . ($base+$counter);
				if($this->userXml->isOneOfMyCli($cli)){
					echo "<h2>This number belonging to GNR range is already configured: " .$cli. " !!!!!</h2>";
					return false;
				}
				$counter++;
			}
		}
		return true;
	}
}

?>
