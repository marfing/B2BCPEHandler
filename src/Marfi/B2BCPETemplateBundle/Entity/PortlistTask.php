<?php
namespace Marfi\B2BCPETemplateBundle\Entity;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;

class PortlistTask{
	
	protected $portListNames;
	protected $userXml;

	public function __construct(userXML $userxml){$this->userXml = $userxml;}
	public function getPortList(){ return $this->portListNames; }
	public function setPortList($portList){ 	$this->portListNames = $portList; }
}

?>
