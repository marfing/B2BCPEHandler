<?php

namespace Marfi\B2BCPETemplateBundle\Entity;

class MultipointTask{
		protected $portListNames;
		
		public function getPortList(){ return $this->portListNames; }
		public function setPortList($portList){ 	$this->portListNames = $portList; }
	}


?>
