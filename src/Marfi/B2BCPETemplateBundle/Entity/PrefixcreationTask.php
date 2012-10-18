<?php
namespace Marfi\B2BCPETemplateBundle\Entity;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;

class PrefixcreationTask{
	
	protected $incomingCaller;
	protected $incomingCalled;
	protected $outgoingCaller;
	protected $outgoingCalled;
	protected $incomingCallerType;
	protected $incomingCalledType;
	protected $outgoingCallerType;
	protected $outgoingCalledType;
	protected $userXml;
	protected $hasIncomingPrefix = false;
	protected $hasOutgoingPrefix = false;

	public function __construct(userXML $userxml){$this->userXml = $userxml;}
	
	public function getIncomingCaller(){ return $this->incomingCaller; }
	public function getIncomingCalled(){ return $this->incomingCalled; }
	public function setIncomingCaller($incomingcaller){ 	$this->incomingCaller = $incomingcaller; }
	public function setIncomingCalled($incomingcalled){ 	$this->incomingCalled = $incomingcalled; }
	
	public function getOutgoingCaller(){ return $this->outgoingCaller; }
	public function getOutgoingCalled(){ return $this->outgoingCalled; }
	public function setOutgoingCaller($outgoingcaller){ 	$this->outgoingCaller = $outgoingcaller; }
	public function setOutgoingCalled($outgoingcalled){ 	$this->outgoingCalled = $outgoingcalled; }

	public function getIncomingCallerType(){ return $this->incomingCallerType; }
	public function getIncomingCalledType(){ return $this->incomingCalledType; }
	public function setIncomingCallerType($incomingcallertype){ 	
		echo "<br>setIncomingCallerType";
		$this->hasIncomingPrefix = true;
		$this->incomingCallerType = $incomingcallertype; }
	public function setIncomingCalledType($incomingcalledtype){ 	
		echo "<br>setIncomingCalledType";
		$this->hasIncomingPrefix = true;
		$this->incomingCalledType = $incomingcalledtype; }

	public function getOutgoingCallerType(){ return $this->outgoingCallerType; }
	public function getOutgoingCalledType(){ return $this->outgoingCalledType; }
	public function setOutgoingCallerType($outgoingcallertype){ 	
		echo "<br>setOutgoingCallerType";
		$this->hasOutgoingPrefix = true;
		$this->outgoingCallerType = $outgoingcallertype; }
	public function setOutgoingCalledType($outgoingcalledtype){
		echo "<br>setOutgoingCalledType";
		$this->hasOutgoingPrefix = true;
		$this->outgoingCalledType = $outgoingcalledtype; }
	
	public function isPrefixOk($prefix){
		return (strlen($prefix) <= 4) && (ctype_digit($prefix));
	}
	public function isFormOk(){
		if ( !empty($this->incomingCaller) )
			if( !$this->isPrefixOk($this->incomingCaller) ) return false;
				else if (empty($this->incomingCallerType)) return false;
		if ( !empty($this->incomingCalled) )
			if( !$this->isPrefixOk($this->incomingCalled) ) return false;
				else if (empty($this->incomingCalledType)) return false;
		if ( !empty($this->outgoingCaller) )
			if( !$this->isPrefixOk($this->outgoingCaller) ) return false;
				else if (empty($this->outgoingCallerType)) return false;
		if ( !empty($this->outgoingCalled) )
			if( !$this->isPrefixOk($this->outgoingCalled) ) return false;
				else if (empty($this->outgoingCalledType)) return false;
		return true;
	}
	public function printData(){
		echo "<br>Incoming caller prefix: " .$this->incomingCaller;
		echo "<br>Incoming caller type: " .$this->incomingCallerType;
		echo "<br>Incoming called prefix: " .$this->incomingCalled;
		echo "<br>Incoming called type: " .$this->incomingCalledType;

		echo "<br>Outgoing caller prefix: " .$this->outgoingCaller;
		echo "<br>Outgoing caller type: " .$this->outgoingCallerType;
		echo "<br>Outgoing called prefix: " .$this->outgoingCalled;
		echo "<br>Outgoing called type: " .$this->outgoingCalledType;
	}
	public function hasIncomingPrefix(){return $this->hasIncomingPrefix;}
	public function hasOutgoingPrefix(){return $this->hasOutgoingPrefix;	}
}

?>
