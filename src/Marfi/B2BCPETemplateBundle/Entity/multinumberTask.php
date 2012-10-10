<?php


namespace Marfi\B2BCPETemplateBundle\Entity;

// TO DO - passare nel construttore il numero di multinumero creati fino a quel momento.
// Si tenga conto infatti che:
// 1) Max 4 pacchetti multinumero ammessi
// 2) max 6 numeri per ogni pacchetto multinumero

class MultinumberTask{
	
	protected $howMany = 0;
		protected $portListNames;
		protected $bind = false;
		protected $existingPacketsNumber = 0;
		
		public function __construct($alreadyConfiguredPackets){$this->existingPacketsNumber = $alreadyConfiguredPackets;}

		
		public function getHowMany(){ return $this->howMany; }
		public function setHowMany($howmany){ $this->howMany= $howmany;}
		public function getPortList(){ return $this->portListNames; }
		public function setPortList($portList){ 	$this->portListNames = $portList; }
		public function getBind(){ return $this->bind; }
		public function setBind($bind){ $this->bind = $bind; }
		public function printData(){
			echo "<table border=\"1\"><tr><td>Multinumbers: </td><td>" .$this->howMany . "</td></tr>";
			echo "<tr><td>Bind</td><td>" . (($this->bind)?"enabled":"disabled") . "</td></tr>";
			echo "<tr><td>Port List</td><td>";
				foreach ($this->portListNames as $port) 
					echo $port . "<br>";
			echo "</td></tr></table>";	
		}
		public function isNumberLimitOk(){ return ($this->howMany <= 7);}
		public function isPacketLimitOk(){ return ($this->existingPacketsNumber<=4);}
	}


?>
