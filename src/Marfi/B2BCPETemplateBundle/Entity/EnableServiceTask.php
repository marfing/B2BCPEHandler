<?php
namespace Marfi\B2BCPETemplateBundle\Entity;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;

class EnableServiceTask{
	
	protected $enable = false;
	protected $howMany;
	protected $userXml;

	public function __construct(userXML $userXml){ 
		//echo "EnableServiceTask -- construct <br>HowMany: ". $this->howMany;
		$this->userXml = $userXml;
		}
	
	public function getEnable(){ 
		//echo "<br>getEnabled";
		return $this->enable; }
	public function setEnable($enable){ 
		//echo "<br>SetEnable";
		$this->enable= $enable;}
	public function getHowMany(){ 
		//echo "<br>getHowMany";
		return $this->howMany; }
	public function setHowMany( $howmany){ 	
		//echo "<br>SetHowManyBefore: " .$this->howMany;
		//echo var_dump($howmany);
		if(!empty($howmany))
			$this->howMany= $howmany; 
		echo "<br>SetHowManyAfter: " .$this->howMany;

		}
	/*public function isEnableOk(){
		echo "<br>isEnabledOk:  " .(($this->enable)?'true':'false'). "   -- HowMany: " .$this->howMany. "<br>";
		if( ( $this->enable && ($this->howMany == 0))   || ((!$this->enable) && ($this->howMany != 0)) ){
			echo "EnableTask::isEnableOK -> return false";
			return false;
		}
		echo "EnableTask::isEnableOK -> return true";
		return true;
	}*/
	public function isHowManyOk(){
		//echo "<br>isHowManyOk";
		return ($this->howMany <= $this->userXml->howManyCliForServices());
	}
}

?>
