<?php
namespace Marfi\B2BCPETemplateBundle\Entity;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;

class PrefixcreationTask{
	
	protected $incoming;
	protected $outgoing;
	protected $incomingType;
	protected $outgoingType;
	
	protected $userXml;

	public function __construct(userXML $userxml){$this->userXml = $userxml;}
	
	public function getIncoming(){ return $this->incoming; }
	public function setIncoming($incoming){ 	$this->incoming = $incoming; }
	public function getOutgoing(){ return $this->outgoing; }
	public function setOutgoing($outgoing){ 	$this->outgoing = $outgoing; }

	public function getIncomingType(){ return $this->incomingType; }
	public function setIncomingType($incomingtype){ 	$this->incomingType = $incomingtype; }
	public function getOutgoingType(){ return $this->outgoingType; }
	public function setOutgoingType($outgoingtype){ 	$this->outgoingType = $outgoingtype; }
	
	public function printData(){
		echo "Incoming prefix: " .$this->incoming;
		echo "<br>Incoming type: " .$this->incomingType;
		echo "<br>Outgoing prefix: " .$this->outgoing;
		echo "<br>Outgoing type: " .$this->outgoingType;
	}

	
	
	
	
}

?>
