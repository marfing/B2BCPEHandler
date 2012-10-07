<?php

namespace Marfi\B2BCPETemplateBundle\Entity;

use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;


class SingleNumberTask{
		protected $singleNumber;
		protected $portListNames;
		protected $bind = false;
		protected $usedSingleNumbersArray;
		
		public function __construct($usedSingleNumbersArray){$this->usedSingleNumbersArray = $usedSingleNumbersArray;	}
		
		public function getSingleNumber(){ 	return $this->singleNumber; }
		public function setSingleNumber($singleNumber){ $this->singleNumber = $singleNumber; 	}
		public function getPortList(){ return $this->portListNames; }
		public function setPortList($portList){ 	$this->portListNames = $portList; }
		public function getBind(){ return $this->bind; }
		public function setBind($bind){ $this->bind = $bind; }
		public function printData(){
			echo "<table border=\"1\"><tr><td>SingleNumber: </td><td>" .$this->singleNumber . "</td></tr>";
			echo "<tr><td>Bind</td><td>" . (($this->bind)?"enabled":"disabled") . "</td></tr>";
			echo "<tr><td>Port List</td><td>";
				foreach ($this->portListNames as $port) 
					echo $port . "<br>";
			echo "</td></tr></table>";	
		}
		public function isE164(){   /// not standards compliant i.e won't meet E.164 etc for validating international phone numbers
			trim($this->singleNumber);
			if(!preg_match("/^[0]\d{3,12}$/",$this->singleNumber)) return false;
			return true;
		}
		public function isBindOk(){
			if( ($this->bind && (count($this->portListNames) < 2)) || (!$this->bind && (count($this->portListNames))>1))
				return false;
			return true;
		}
		public function isSingleNumberUnused(){
			foreach($this->usedSingleNumbersArray as $usedNumber)
				if($usedNumber == $this->singleNumber)
					return false;
			return true;
		}
	}

?>
