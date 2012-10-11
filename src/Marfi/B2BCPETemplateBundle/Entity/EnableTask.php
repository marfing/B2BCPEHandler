<?php
namespace Marfi\B2BCPETemplateBundle\Entity;

class EnableTask{
	
	protected $enable = false;
	protected $howMany;
	protected $howManyCliInUserXml=0;

	public function __construct($howmanyinuserxml){
		$this->howManyCliInUserXml = $howmanyinuserxml;
	}
	
	public function getEnable(){ return $this->enable; }
	public function setEnable($enable){ $this->enable= $enable;}
	public function getHowMany(){ return $this->howMany; }
	public function setHowMany($howmany){ 	$this->howMany= $howmany; }
	public function isEnableOk(){
		if( ( $this->enable && ($this->howMany == 0))   || ((!$this->enable) && ($this->howMany > 0))  )
			return false;
		return true;
	}
	public function isHowManyOk(){
		return ($this->howMany <= $this->howManyCliInUserXml);
	}
}

?>
