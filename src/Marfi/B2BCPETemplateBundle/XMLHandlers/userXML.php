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
	protected $multinumbersArray;
	protected $cliServices;
	protected $customerId;
	
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
	public function printUserXML(){
		echo "<div class=\"summary\"><table border=\"1\" ><tr><td>CustomerID</td><td> " .$this->customerId. "</td></tr>";
		echo "<tr><td>Vendor</td><td> "  .$this->vendorName. "</td></tr>";
		echo "<tr><td>Model</td><td> "  .$this->modelName. "</td></tr>";
		echo "<tr><td>Sim calls</td><td> "  .$this->simCallsNumber. "</td></tr>";
		echo "</table>";
		if($this->portsHandler->hasPorts()){
			echo $this->portsHandler->printPorts(). "<br>";
		}
		echo "</div>";
		//TO DO - aggiungere gli altri elementi man mano che li si crea
	}
	public function setSingleNumber($cliString, $portName){$this->portsHandler->setSingleNumber($cliString, $portName);}
	public function getSingleNumbersArray(){return $this->portsHandler->getSingleNumbersArray();}
	public function getPortNamesArray(){ return $this->portsHandler->getPortNamesArray();}
	public function getPortNamesArrayWithoutSingleNumber(){ 	return $this->portsHandler->getPortNamesArrayWithoutSingleNumber();}
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
			echo "</tr></table>";
		} else echo "<h2 style=\"color:red\">ERROR - Trying to print ports that does not exist!!!!</h2>";
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
		foreach ($this->portsArray as $port){
			if(!$port->hasSingleNumber())
				$portNamesArray[] = $port->getName();
		}
		return $portNamesArray;
	}
	
}


class userXMLport
{
	protected $name = 'empty';
	protected $typeString;
	protected $pobri = false;
	protected $singleNumber ='empty';
	protected $hasSingleNumber = false;
	protected $gnrContainer;
	protected $incomingPrefixRule;
	protected $outgoingPrefixRule;
	protected $multipointMode = false;
	
	public function __construct($name, $typeString, $pobri){
		$this->name = $name;
		$this->pobri = $pobri;
		$this->typeString = (string)$typeString;
		$this->singleNumber = new singleNumber();
		$this->gnrContainer = new gnrContainer();
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
	public function printPort(){
		echo "Port name: " . $this->name. "<br>";
		echo "Port type: " .$this->type->getTypeString(). "<br>";
		echo "Port Mode: ";
		if($this->multipointMode && $this->isBRI()) 	echo "point-to-multipoint<br>";
		else 	echo "point-to-point<br>";
		echo "Port PoBRI: ";
		if($this->pobri) 	echo "enabled<br>";
		else 	echo "disabled<br>";
		// TO DO - aggiungere altri elementi man mano che li gestisco
	}
	public function getName(){ return $this->name; }
	public function getTypeString(){return (string)$this->typeString;}
	public function hasPoBRI(){return $this->pobri;}
	public function isBRI(){ return ($this->typeString == 'BRI'); }
	public function setSingleNumber($cliString){
		//echo "<br>userXMLport::setSingleNumber: " .$cliString. " port: " .$this->name ;
		$this->singleNumber->setCli($cliString);
		$this->hasSingleNumber = true;
	}
	public function getSingleNumber(){return $this->singleNumber->getCli();}
	public function hasSingleNumber(){	return $this->hasSingleNumber;}
}

class prefixRule
{
	protected $enabled = false;
	protected $callerPrefix;
	protected $calledPrefix;
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

class gnrContainer
{
	protected $gnrArray;
	protected $howManyGnr = 0;
	protected $enabled = false;
	
	public function createGnr($root, $did, $extens){
		$gnr = new gnr($root, $did, $extens);
		$this->gnrArray[] = $gnr;
		$this->howManyGnr++;
	}
	public function howManyGnr(){return $this->howManyGnr;}
	public function isEnabled(){
		return $this->enabled;
	}
}

class gnr
{
	protected $enabled = false;  //TO DO - verificare se serve, perchè alla fine se esiste è per forza abilitato
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

?>
