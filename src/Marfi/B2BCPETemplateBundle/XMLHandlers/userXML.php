<?php

namespace Marfi\B2BCPETemplateBundle\XMLHandlers;
use Marfi\B2BCPETemplateBundle\XMLHandlers\portType;
use Marfi\B2BCPETemplateBundle\XMLHandlers\modelPort;;


class userXML
{
	protected $vendorName;
	protected $modelName;
	protected $simCallsNumber;
	protected $portsHandler;
	protected $cliServices;
	protected $customerId;
	protected $hasGnr = false;
	protected 	$gnrRoot;
	protected 	$gnrExtension;
	protected $multinumbersArray;
	protected $multinumberPacketsCounter = 0; //max 4
	protected $cliList = array();
	protected $hasFax = false;
	protected $faxCliArray = array();
	protected $hasPos= false;
	protected $posCliArray = array();
	
	public function __construct($vendor, $model, $simcalls, $customerid){
		$this->vendorName = $vendor;
		$this->modelName = $model;
		$this->simCallsNumber = $simcalls;
		$this->customerId = $customerid;
		$this->portsHandler = new userXMLportsHandler();
	}

	public function addPort($name, $type, $pobri){ $this->portsHandler->addPort($name, $type, $pobri);}
	public function addModelPort($port){ $this->portsHandler->addModelPort($port);}
	public function setMultipointBRIports($briportsnamearray){
		if($this->portsHandler->hasPorts()){
			foreach($briportsnamearray as $name){
				$this->portsHandler->setMultimodePort($name);
			}
		}
		else 
			echo "<h2 style=\"color:red\">ERROR - trying to configure multimode without any BRI port available!!</h2>";
	}
	public function setSingleNumber($cliString, $portName){
		$this->portsHandler->setSingleNumber($cliString, $portName);
		if(!$this->isOneOfMyCli($cliString))
			$this->cliList[]=$cliString;
	}
	public function getSingleNumbersArray(){return $this->portsHandler->getSingleNumbersArray();}
	public function setGnr($root, $did, $digits, $portName){
		$this->hasGnr = true;
		$this->gnrRoot = $root;
		$this->gnrExtension = $digits;
		$this->portsHandler->setGnr($root, $did, $digits, $portName);
	}
	public function hasGnr(){return $this->hasGnr;}
	public function getPortNamesArray(){ return $this->portsHandler->getPortNamesArray();}
	public function getPortNamesArrayWithoutSingleNumber(){ 	return $this->portsHandler->getPortNamesArrayWithoutSingleNumber();}
	public function hasPortsWithoutSingleNumberAvailable(){	return  count($this->portsHandler->getPortNamesArrayWithoutSingleNumber())>0;}
	public function getBRIPortNamesArray(){ return $this->portsHandler->getBRIPortNamesArray();}
	public function hasBRIPorts(){return $this->portsHandler->hasBRIPorts();}
	public function getNumberOfBRIPorts(){return $this->portsHandler->getNumberOfBRIPorts();}
	public function printUserXML(){
		echo "<table border=\"1\" ><tr><td>CustomerID</td><td> " .$this->customerId. "</td></tr>";
		echo "<tr><td>Vendor</td><td> "  .$this->vendorName. "</td></tr>";
		echo "<tr><td>Model</td><td> "  .$this->modelName. "</td></tr>";
		echo "<tr><td>Sim calls</td><td> "  .$this->simCallsNumber. "</td></tr>";
		echo "</table>";
		if($this->portsHandler->hasPorts()){
			echo $this->portsHandler->printPorts(). "<br>";
		}
		if($this->multinumberPacketsCounter>0){
			echo "<table border=\"1\">";
			foreach ($this->multinumbersArray as $key => $multinumber){
				echo "<tr><td>Multinumber " .$key. "</td></tr>";
				echo "<tr><td>Bind: " . ($multinumber->hasBind()?"enabled":"disabled") . "</td>";
				echo "<td><table border=\"1\"><tr><td>Ports: </td><td><ul>";
				foreach ($multinumber->getPortsNameArray() as $port){
					echo "<li>" .$port . "</li>";
				} echo "</ul></td></tr></table>";
				echo "<td><table border=\"1\"><tr><td>Cli: </td><td><ul>";
				foreach ($multinumber->getCliList() as $cli){
					echo "<li>" .$cli. "</li>";
				} echo "</ul></td></tr></table>";
			} echo "</table>";
		}
		if($this->hasFax || $this->hasPos){
			echo "<table border=\"1\"><tr><td>Services</tr></td>";
			if($this->hasFax){
				echo "<tr><td>FAX service cli list</td><td><ul>";
				foreach ($this->faxCliArray as $cli) echo "<li>" .$cli. "</li>";
				echo "</ul></tr></td>";
			}
			if($this->hasPos){
				echo "<tr><td>POS service cli list</td><td><ul>";
				foreach ($this->posCliArray as $cli) echo "<li>" .$cli. "</li>";
				echo "</ul></tr></td>";
			}
			echo "</table>";
		}
		//TO DO - aggiungere gli altri elementi man mano che li si crea
	}
	public function printUserXMLOutside(){return $this->portsHandler->getPrintPorts();}
	public function setMultinumber($bind, $portsnamearray, $clilist){ 		
		echo "userXML::setMultinumber - clilist: " .var_dump($clilist). "<br><br>";
		$this->multinumbersArray[] = new multiNumber($bind, $portsnamearray, $clilist);
		$this->multinumberPacketsCounter++;
		foreach ($clilist as $cli) 
			if(!$this->isOneOfMyCli($cli))
				$this->cliList[]=$cli;
	}
	public function multinumberPacketsLimitReached(){	return ($this->multinumberPacketsCounter < 4) ? false : true; 	}
	public function isOneOfMyCli($cli){
		if($this->cliList)
		foreach($this->cliList as $mycli) 
			if($cli == $mycli) return true;
		if($this->hasGnr){
			echo "Has gnr - Extension: " .$this->gnrExtension;
			for($i=1; $i<=$this->gnrExtension; $i++){
				$base = intval($this->gnrRoot)*pow(10,$i);
				echo "base: " .$base;
				$finalnumber = $base + pow(10,$i);
				echo "Final number: " .$finalnumber;
				$counter = 1;
				for($j=$base; $j<$finalnumber;$j++){
					$mygnrcli = '0' . ($base+$counter);
					echo "Checking number: " .$mygnrli;
					if($cli == $mygnrcli) return true;
					$counter++;
				}
			}
		}
		return false;
	}
	public function howManyCli(){
		$counter = count($this->cliList);
		if($this->hasGnr)
			$counter = $counter+pow(10,$this->gnrExtension-1);
		return $counter;}
	public function howManyCliForServices(){
		$counter = count($this->cliList);
		echo "<br>Counter: " .print_r($this->cliList);
		echo "<br>userXML::Single & multi CLI configured: " .$counter;
		if($this->hasGnr) $counter = $counter+pow(10,$this->gnrExtension-1);
		$service = 0;
		echo "<br>userXML::CLI configured: " .$counter;
		if($this->hasFax) $service = $service + count($this->faxCliArray);
		if($this->hasPos) $service = $service + count($this->faxPosArray);
		echo "<br>userXML::Servicecli used: " .$service;
		return $counter-$service;	
	}
	public function isGoodForService($cli){return (($this->isOneOfMyCli($cli) && !$this->hasAService($cli)));}
	public function setFax($cliArray){
		$this->faxCliArray = $cliArray;
		$this->hasFax = true;
		}
	public function setPos($cliArray){
		$this->posCliArray = $cliArray;
		$this->hasPos = true;
		}
	public function hasAService($cli){
		if($this->hasFax)
			foreach($this->faxCliArray as $mycli)
				if($mycli == $cli) return true;
		if($this->hasPos)
			foreach($this->faxPosArray as $mycli)
				if($mycli == $cli) return true;
		return false;
	}
}

class userXMLportsHandler
{
	protected $portsNumber = 0;
	protected $portsArray;
	
	public function addPort($name, $typeString, $pobri){ 
		$this->portsArray[] = new userXMLport($name, $typeString, $pobri) ;
		$this->portsNumber++;
	}
	public function addModelPort($modelPort){
		$this->portsArray[] = new userXMLport($modelPort->getName(),$modelPort->getTypeString(),$modelPort->hasPoBRI());
		$this->portsNumber++;
	}
	public function setMultimodePort($portName){
		foreach($this->portsArray as $port){
			if($port->isMyName($portName)){
				$port->setMultipointMode();
			}
		}
	}
	public function hasPorts(){
		if($this->portsNumber != 0) return true;
		else return false;
	}
	public function hasBRIPorts(){
		if($this->hasPorts())
			foreach ($this->portsArray as $port)
				if($port->isBRI())
					return true;
		return false;
	}
	public function printPorts(){
		if($this->portsNumber != 0){
			echo "<table border=\"2\">";
			echo "<tr><td>Ports</td></tr><tr><td>Name</td>";
			foreach($this->portsArray as $port) 	echo "<td>" . $port->getName(). "</td>";
			echo "</tr><tr><td>Type</td>";
			foreach($this->portsArray as $port) 	echo "<td>" . $port->getTypeString(). "</td>";
			echo "</tr><tr><td>Mode</td>";
			foreach($this->portsArray as $port){
				if($port->isBRI())
					if($port->isMultipoint()) echo "<td>point-to-multipoint</td>";
					else echo "<td>point-to-point</td>";
				else echo "<td>-</td>";
			}
			echo "</tr><tr><td>PoBRI</td>";
			foreach($this->portsArray as $port){
				if($port->hasPoBRI()) echo "<td>Available</td>";
				else echo "<td>-</td>";
			}
			echo "</tr><tr><td>SingleNumber</td>";
			foreach($this->portsArray as $port){
				if($port->hasSingleNumber()) echo "<td>" .$port->getSingleNumber(). "</td>";
				else echo "<td>-</td>";
			}
			echo "</tr><tr><td>Gnr</td>";
			foreach($this->portsArray as $port){
				if($port->hasGnr()) echo "<td><table border=\"1\">
																<tr><td>root</td><td>" .$port->getGnrRoot(). "</td></tr>
																<tr><td>did</td><td>" .(($port->hasGnrDid() ? "enabled" : "disabled")). "</td></tr>
																<tr><td>digits</td><td>" .$port->getExtensionDigits(). "</td></tr></table>";
				else echo "<td></td>";
			}
			echo "</tr></table>";
		} else echo "<h2 style=\"color:red\">ERROR - Trying to print ports that does not exist!!!!</h2>";
	}
	public function getPrintPorts(){
		if($this->portsNumber != 0){
			$printed =  "<table border=\"2\"><tr><td>Ports</td></tr><tr><td>Name</td>";
			foreach($this->portsArray as $port) 	$printed = $printed . "<td>" . $port->getName(). "</td>";
			$printed = $printed .  "</tr><tr><td>Type</td>";
			foreach($this->portsArray as $port) 	$printed = $printed .  "<td>" . $port->getTypeString(). "</td>";
			$printed = $printed .  "</tr><tr><td>Mode</td>";
			foreach($this->portsArray as $port){
				if($port->isBRI())
					if($port->isMultipoint()) $printed = $printed .  "<td>point-to-multipoint</td>";
					else $printed = $printed .  "<td>point-to-point</td>";
				else $printed = $printed .  "<td>-</td>";
			}
			$printed = $printed .  "</tr><tr><td>PoBRI</td>";
			foreach($this->portsArray as $port){
				if($port->hasPoBRI()) $printed = $printed .  "<td>Available</td>";
				else $printed = $printed .  "<td>-</td>";
			}
			$printed = $printed .  "</tr><tr><td>SingleNumber</td>";
			foreach($this->portsArray as $port){
				if($port->hasSingleNumber()) $printed = $printed .  "<td>" .$port->getSingleNumber(). "</td>";
				else $printed = $printed .  "<td>-</td>";
			}
			$printed = $printed .  "</tr><tr><td>Gnr</td>";
			foreach($this->portsArray as $port){
				if($port->hasGnr()) $printed = $printed .  "<td><table border=\"1\" class=\"gnr\">
																<tr><td>root</td><td>" .$port->getGnrRoot(). "</td></tr>
																<tr><td>did</td><td>" .(($port->hasGnrDid() ? "enabled" : "disabled")). "</td></tr>
																<tr><td>digits</td><td>" .$port->getExtensionDigits(). "</td></tr></table>";
				else $printed = $printed .  "<td></td>";
			}
			$printed = $printed .  "</tr></table>";
		} else $printed = $printed .  "<h2 style=\"color:red\">ERROR - Trying to print ports that does not exist!!!!</h2>";
		return $printed;
	}
	public function setSingleNumber($cliString, $portName){
		foreach($this->portsArray as $port){
			if($port->isMyName($portName) && !$port->hasSingleNumber()) {
				$port->setSingleNumber($cliString);
			} 
		}	
	}
	public function getSingleNumbersArray(){
		$singleNumbersArray[]='empty';
		foreach($this->portsArray as $port)
			if($port->hasSingleNumber())
				$singleNumbersArray[] = $port->getSingleNumber();
		return $singleNumbersArray;
	}
	public function getPortNamesArray(){
		$portNamesArray;
		foreach ($this->portsArray as $port) $portNamesArray[] = $port->getName();
		return $portNamesArray;
	}
	public function getPortNamesArrayWithoutSingleNumber(){
		$portNameArray = array();
		foreach ($this->portsArray as $port){
			if(!$port->hasSingleNumber()){
				$portNameArray[] = $port->getName();
			}
		}
		return $portNameArray;
	}
	public function getBRIPortNamesArray(){
		$portNamesArray = array();
		if($this->hasBRIPorts()){
			foreach ($this->portsArray as $port)
				if($port->isBRI())
					$portNamesArray[]=$port->getName();
			return $portNamesArray;
		} return $portNamesArray[]='empty';
	}
	public function getNumberOfBRIPorts(){
		$counter = 0;
		foreach ($this->portsArray as $port)
			if($port->isBRI()) $counter++;
		return $counter;
	}
	public function setGnr($root, $did, $digits, $portName){
		foreach($this->portsArray as $port){
			if($port->isMyName($portName) && !$port->hasGnr()) {
				$port->setGnr($root, $did, $digits);
			} 
		}	
		
	}
}

class userXMLport
{
	protected $name = 'empty';
	protected $typeString;
	protected $pobri = false;
	protected $singleNumber ='empty';
	protected $hasSingleNumber = false;
	protected $gnr;
	protected $hasGnr = false;
	protected $incomingPrefixRule;
	protected $outgoingPrefixRule;
	protected $multipointMode = false;
	
	public function __construct($name, $typeString, $pobri){
		$this->name = $name;
		$this->pobri = $pobri;
		$this->typeString = (string)$typeString;
		$this->singleNumber = new singleNumber();
		$this->incomingPrefixRule = new prefixRule(true);
		$this->outgoingPrefixRule = new prefixRule(false);
	}
	public function setPointToPointMode(){ $this->multipointMode = false; }
	public function setMultipointMode(){$this->multipointMode=true;}
	public function isMultipoint(){return $this->multipointMode;}
	public function isPointToPoint(){return !$this->multipointMode;}
	public function isMyName($name){
		if($name == $this->name) return true;
		else return false;
	}
	public function getName(){ return $this->name; }
	public function getTypeString(){return (string)$this->typeString;}
	public function hasPoBRI(){return $this->pobri;}
	public function isBRI(){ return ($this->typeString == 'BRI'); }
	public function setSingleNumber($cliString){
		$this->singleNumber->setCli($cliString);
		$this->hasSingleNumber = true;
	}
	public function getSingleNumber(){return $this->singleNumber->getCli();}
	public function hasSingleNumber(){	return $this->hasSingleNumber;}
	public function setGnr($root, $did, $digits){
		$this->gnr = new gnr($root, $did, $digits);
		$this->hasGnr = true;
	}
	public function hasGnr(){return $this->hasGnr;}
	public function getGnrRoot(){return $this->gnr->getRoot();}
	public function hasGnrDid(){return $this->gnr->hasDid();}
	public function getExtensionDigits(){return $this->gnr->getExtensionDigits();}
}

class prefixRule
{
	protected $enabled = false;
	protected $prefix;
	protected $isIncoming=false;
	
	public function __construct($isincoming){
		$this->isIncoming = $isincoming;
		$this->callerPrefix = new prefix();
		$this->calledPrefix = new prefix();
	}
	
	public function addPrefixToCaller($prefix){ $this->callerPrefix->addPrefix($prefix); 	}
	public function deletePrefixFromCaller($prefix){ $this->callerPrefix->delPrefix($prefix); }
	public function addPrefixToCalled($prefix){ 	$this->calledPrefix->addPrefix($prefix); }
	public function deletePrefixFromCalled($prefix){ $this->calledPrefix->delPrefix($prefix); 	}
}

class prefix
{
	protected $enabled = false;
	protected $toAdd = false;
	protected $prefix;
	
	public function addPrefix($prefix){
		$this->prefix = $prefix;
		$this->toAdd = true;
	}
	public function delPrefix($prefix){
		$this->prefix = $prefix;
		$this->toAdd = false;
	}
	public function isToAdd(){return $this->add;}
}

class singleNumber
{
	protected $enabled = false;
	protected $cli = 'empty';
	
	public function setCli($cliString){
			$this->cli = $cliString;
			$this->enabled = true;
	}
	public function isEnabled(){ 	return $this->enabled;	}
	public function getCli(){ return (string)$this->cli; }
}

class gnr
{
	protected $root;
	protected $did = false;
	protected $extensionDigits = 0;
	
	public function __construct($root, $did, $extens){
		$this->root = $root;
		$this->did = $did;
		$this->extensionDigits = $extens;
	}
	public function setRoot($root){ $this->root = $root; }
	public function setDid($did){ $this->did = $did;}
	public function setExtensionDigits($extens){$this->extensionDigits=$extens;}
	public function getRoot(){ return $this->root; }
	public function hasDid(){ return $this->did;}
	public function getExtensionDigits(){return $this->extensionDigits;}
}

class multiNumber{
	protected $bind = false;
	protected $portNamesArray;
	protected $cliList;
	
	public function __construct($bind, $portsnamearray, $clilist){
		$this->bind = $bind;
		$this->portNamesArray = $portsnamearray;
		$this->cliList = $clilist;
	}
	
	public function hasBind(){return $this->bind;}
	public function getPortsNameArray(){return $this->portNamesArray;}
	public function getCliList(){return $this->cliList;}
}

?>
