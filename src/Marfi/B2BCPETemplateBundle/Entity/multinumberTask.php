<?php

namespace Marfi\B2BCPETemplateBundle\Entity;

// TO DO - passare nel construttore il numero di multinumero creati fino a quel momento.
// Si tenga conto infatti che:
// 1) Max 4 pacchetti multinumero ammessi
// 2) max 6 numeri per ogni pacchetto multinumero

class MultinumberTask{
	
	protected $portListNames;
	protected $bind = false;
	protected $cli1="";
	protected $cli2="";
	protected $cli3="";
	protected $cli4="";
	protected $cli5="";
	protected $cli6="";
	protected $cliList;

	public function getPortList(){ return $this->portListNames; }
	public function setPortList($portList){ 	$this->portListNames = $portList; }
	public function getBind(){ return $this->bind; }
	public function setBind($bind){ $this->bind = $bind; }
	public function getCli1(){ return $this->cli1; }
	public function setCli1($cli1){ $this->cli1 = $cli1; }
	public function getCli2(){ return $this->cli2; }
	public function setCli2($cli2){ $this->cli2 = $cli2; }
	public function getCli3(){ return $this->cli3; }
	public function setCli3($cli3){ $this->cli3 = $cli3; }
	public function getCli4(){ return $this->cli4; }
	public function setCli4($cli4){ $this->cli4 = $cli4; }
	public function getCli5(){ return $this->cli5; }
	public function setCli5($cli5){ $this->cli5 = $cli5; }
	public function getCli6(){ return $this->cli6; }
	public function setCli6($cli6){ $this->cli6= $cli6; }
	public function printData(){
		echo "<table border=\"1\"><tr><td>Multinumbers: </td><td>" .$this->howMany . "</td></tr>";
		echo "<tr><td>Bind</td><td>" . (($this->bind)?"enabled":"disabled") . "</td></tr>";
		echo "<tr><td>Port List</td><td>";
			foreach ($this->portListNames as $port) 
				echo $port . "<br>";
		echo "</td></tr></table>";	
	}
	public function getCliList(){
		if(!empty($this->cli1))
			$this->cliList[] = $this->cli1;
		if(!empty($this->cli2))
			$this->cliList[] = $this->cli2;
		if(!empty($this->cli3))
			$this->cliList[] = $this->cli3;
		if(!empty($this->cli4))
			$this->cliList[] = $this->cli4;
		if(!empty($this->cli5))
			$this->cliList[] = $this->cli5;
		if(!empty($this->cli6))
			$this->cliList[] = $this->cli6;
		return $this->cliList;
	}
	public function isCliListOk(){
		foreach ($this->getCliList() as $validcli)
			if(!$this->isE164($validcli)) return false;
		else return true;
	}
	public function isE164($cli){   /// not standards compliant i.e won't meet E.164 etc for validating international phone numbers
		trim($cli);
		if(!preg_match("/^[0]\d{3,12}$/",$cli)) return false;
		return true;
	}
	public function isBindOk(){
		if( ($this->bind && (count($this->portListNames) < 2)) || (!$this->bind && (count($this->portListNames))>1))
			return false;
		return true;
	}
}


?>
