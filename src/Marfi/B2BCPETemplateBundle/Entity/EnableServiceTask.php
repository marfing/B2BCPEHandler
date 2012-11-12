<?php
namespace Marfi\B2BCPETemplateBundle\Entity;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;

class EnableServiceTask{
	
	protected $enable = false;
	protected $howMany;
	protected $userXml;
	protected $forall = false;

	public function __construct(userXML $userXml){ $this->userXml = $userXml;}
	
	public function getEnable(){ 	return $this->enable; }
	public function getForall(){ 	return $this->forall; }
	public function setEnable($enable){	$this->enable= $enable;}
	public function setForall($forall){$this->forall=$forall;}
	public function getHowMany(){ return $this->howMany; }
	public function setHowMany( $howmany){ 
		if(!empty($howmany))
			$this->howMany= $howmany; 
		}
	public function isHowManyOk(){
		if(!$this->forall)
			return ($this->howMany <= $this->userXml->howManyCliForServices());
		else //forall true
			return true; //a prescindere da howmany
	} 
	public function isCorrect(){return ($this->howMany > 0 || $this->forall);}
}

?>
