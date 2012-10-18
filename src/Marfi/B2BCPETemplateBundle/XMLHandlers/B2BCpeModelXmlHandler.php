<?php

/** XML file example
 *
<?xml version="1.0" encoding="ISO-8859-1"?>
<model_parameters>
	<vendor>OneAccess</vendor>
	<name>4b2v</name>
	<sim_calls>
		<value>6</value>
		<value>8</value>
	</sim_calls>
	<port_details number="6">
		<port name="5/0" type="BRI" backup="false"/>
		<port name="5/1" type="BRI" backup="false"/>
		<port name="5/2" type="BRI" backup="false"/>
		<port name="5/3" type="BRI" backup="true"/>
		<port name="5/4" type="POTS"/>
		<port name="5/5" type="POTS" />
	</port_details>
</model_parameters>
 
 */


namespace Marfi\B2BCPETemplateBundle\XMLHandlers;

class B2BCpeModelXmlHandler
{
	protected $fileName;
	protected $vendor;
	protected $modelName;
	protected $simCallsArray;
	protected $portsNumber =0;
	protected $briPortsNumber =0;
	protected $potsPortsNumber=0;
	protected $priPortsNumber=0;
	protected $backupPort = "no backup port";
	protected $fileExist = false;
	protected $portsArray;
	protected $hasSimCalls = false;
	protected $hasVendor = false;
	protected $hasName = false;
	protected $hasPortDetails = false;
	
	function __construct($filename){
		$this->fileName = $_SERVER['DOCUMENT_ROOT'] . "xml/" . $filename;
		$this->loadXMLFile();
	}
	
	public function loadXMLFile(){
		libxml_use_internal_errors(true);
		if(file_exists($this->fileName))	{
			$xml = simplexml_load_file($this->fileName);
			if(!$xml){
				echo "Failed loading XML\n";
				foreach(libxml_get_errors() as $error) 	echo "\t", $error->message;
			}
			foreach($xml->children() as $childname){
				switch($childname->getName()) {
					case "vendor": $this->hasVendor=true; break;
					case "name": $this->hasName=true; break;
					case "sim_calls": if($childname->count() != 0) $this->hasSimCalls=true; break;
					case "port_details":  if($childname->count() != 0) $this->hasPortDetails=true; break;
				}
			}
			if($this->hasVendor)
				$this->vendor = $xml->vendor;
			else {
				echo "<h1>XML File ERROR!! - Missing vendor tag</h1>";
				return false;
			}
			if($this->hasName)
				$this->modelName = $xml->name;
			else {
				echo "<h1>XML File ERROR!! - Missing model name tag</h1><p> &ltname&gtmodel_name&lt/name&gt </p>";
				return false;
			}
			if($this->hasSimCalls){
				foreach ($xml->sim_calls->value as $value)
					$this->simCallsArray[] = (int)$value;
			}
			else {
				echo "<h1>XML File ERROR!! - Missing sim calls tag</h1><p> &ltsim_calls&gt<br>&ltvalue&gtnumber&lt/value&gt<br>&lt/sim_calls&gt </p>";
				return false;
			}
			if($this->hasPortDetails){
				$this->portsNumber = $xml->port_details['number'];
				foreach ($xml->port_details->port as $value){
					$boolValue = ($value['backup'] == 'true') ? true : false;
					$port = new modelPort((string)$value['name'], $boolValue, (string)$value['type']);
					$this->portsArray[] = $port;
					if($port->isBRI()) $this->briPortsNumber++;
					elseif ($port->isPOTS())$this->potsPortsNumber++; 
					elseif ($port->isPRI()) $this->priPortsNumber++; 
					if($value['backup'] && $value['type']=='BRI') 
						$this->backupPort = $value['name'];
				}
			}
			else {
				echo "<h1>XML File ERROR!! - Missing port details tag</h1><p> &ltport_details&gt<br>&ltport name=\"name\" type=\"TYPE\" backup=\"bool\" /&gt<br>&lt/port_details&gt </p>";
				return false;
			}
			$this->fileExist = true;
			return  true;
		} 
		else 
			echo "<h1>XML File: " . $this->fileName . " does not exist</h1>";
		return false;
	}
	public function printModelparameters() {
		echo "<ul style=\"font-size:10px\"><li>XML File name: " .  $this->fileName . "</li>" .
				"<li>Vendor: " .  $this->vendor . "</li>" .
				"<li>Model name: " .  $this->modelName . "</li>" ;
		foreach ($this->simCallsArray as $value)
			echo "<li>Sim calls: " . $value . "</li>";
		echo "<li>Ports number: " .  $this->portsNumber . "</li>" .
				"<li>Backup port: " . $this->backupPort . "</li>";
		echo "<li>BRI ports number: " .$this->briPortsNumber. "</li>";
		echo "<li>POTS ports number: " .$this->potsPortsNumber. "</li>";
		echo "<li>PRI ports number: " .$this->priPortsNumber. "</li></ul>";
	}
	public function fileExist(){ return $this->fileExist;}
	public function getSimCalls() { return $this->simCallsArray; }
	public function getPortsNumber(){ return (int)$this->portsNumber; }
	public function hasBRIPorts(){
		if($this->briPortsNumber != 0) return true;
		else return false;
	}
	public function hasSimCalls(){return $this->hasSimCalls;}
	public function getBRIportsNameArray()	{
		$briPortsNameArray;
		if($this->briPortsNumber != 0){
			foreach($this->portsArray as $port) 	{
				if($port->isBRI())
						$briPortsNameArray[]=$port->getName();
			}
			return $briPortsNameArray;
		} else $briPortsNameArray = array ("NO-BRI-PORTS!!");
	}
	public function getVendor(){ return (string)$this->vendor; }
	public function getModelName(){ return (string)$this->modelName; }
	public function getModelPortArray(){return $this->portsArray;	}
	public function getModelPortNamesArray(){
		$portsNameArray;
		foreach($this->portsArray as $port) 	$portsNameArray[]=$port->getName();
		return $briPortsNameArray;
	}
}

class modelPort
{
	protected $name;
	protected $backup = false;
	protected $type;
	
	public function __construct($name, $enable, $type){
		$this->setName($name);
		$this->setType($type);
		if($this->type->getTypeString() == 'BRI') {$this->setBackup($enable);} else {$this->backup=false;}
		if(($this->type->getTypeString() != 'BRI') && $this->backup){
			echo "<h2 style=\"color:red\">ERROR - port " .$name. " cannot have backup because it is not a BRI port!!!!!!!</h2>Please check XML model file and correct it!!";
		}
	}
	public function setName($name){ $this->name = $name;}
	public function setBackup($enable){ $this->backup = $enable;}
	public function setType($type){ $this->type = new portType($type); }
	public function getName(){ return (string)$this->name;}
	public function hasBackup(){ 	return (string)$this->backup;}
	public function getTypeString(){ return $this->type->getTypeString();}
	public function printPort(){
		echo "<table border=\"1\"><tr><td>" .$this->getName() . "</td>";
		if($this->backup)
			{echo "<td>Backup Enabled</td>";} 
		else {echo "<td>Backup Disabled</td>";}
		echo "<td>" . $this->type->getTypeString(). "</td>";
		echo "</tr></table>";
	}
	public function isBRI(){ if($this->type->isBRI())	return true; else return false; }
	public function isPOTS(){ 	if($this->type->isPOTS())	return true; else return false; }
	public function isPRI(){ if($this->type->isPRI())	return true; else return false; }
}

class portType {

	protected $typeArray=array( "POTS","BRI", "PRI", );
	protected $typeString = "NULL";
	
	public function __construct($type){
		if($this->checkType($type)){
			$this->typeString = $type;
		} else echo"<h3>portType::ERROR - WRONG PORT TYPE!! </h3>";
	}
	public function checkType($typestring){
		foreach($this->typeArray as $value)
			if($value == $typestring)
				return true;
		return false;
	}
	public function getTypeString(){ return (string)$this->typeString;	}
	public function isBRI(){ return $this->typeString=="BRI"; }
	public function isPRI(){ return $this->typeString=="PRI";}
	public function isPOTS(){return $this->typeString=="POTS";}
}
?>
