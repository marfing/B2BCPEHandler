<?php

namespace Marfi\B2BCPETemplateBundle\XMLHandlers;

class B2bCpeListXmlHandler
{
	protected $filename;
	protected $xml;
	protected $vendors_n=0;
	protected $vendor_list;
	protected $fileExist = false;
	
	function __construct($filename)	{
		$this->filename = $filename;
		$this->loadXMLFile();
		//echo "Filename: " . $this->filename . "</br>";
	}
	public function loadXMLFile(){
		if(file_exists($this->filename)) 	{
			$this->xml = simplexml_load_file($this->filename);
			$this->vendors_n = $this->xml->vendors[0]['number'];
			$this->fileExist = true;
			//echo "<p>B2bCpeListXmlHandler::LoadXMLFile - vendors number:  " . $this->vendors_n . "</p>";
			$this->LoadVendors();
		} else echo "XML File: " . $this->filename . " does not exist</br>";
	}
	public function loadVendors(){
			for ($i=0; $i<$this->vendors_n; $i++){
				//echo "<p>B2bCpeListXmlHandler::LoadVendors -- loop i=" .$i . "</p>";
				$xml_vendor = $this->xml->vendors->vendor[$i];
				$vendor = new B2BCPEVendor($xml_vendor['name'], $xml_vendor['models_number']);
				//echo "<p>B2bCpeListXmlHandler::LoadVendors.  Model name: " . $xml_vendor['name'] . " -- Models Number: " . $xml_vendor['models_number'] . "</p>";
				for ($j=0;  $j<$xml_vendor['models_number']; $j++) {
					$xml_model = $xml_vendor->model[$j];
					$vendor->setModel($xml_model['name'], $xml_model['filename']);
				}
				//echo "<p>B2bCpeListXmlHandler::LoadVendors.  Exiting models setup loop - i= " . $i. "</p>";
				$this->vendor_list[] = $vendor;
			}
	}
	public function getVendorsNumber(){ 	return $this->vendors_n; 	}
	public function getVendorName($vendor_index){ return $this->vendor_list[$vendor_index]->getName();	}
	public function getModelsNumber($vendor_index){ return $this->vendor_list[$vendor_index]->getModelsNumber();	}
	public function getModelName($vendor_index, $model_index){ return $this->vendor_list[$vendor_index]->getModelName($model_index);	}
	public function getModelFilename($vendor_index, $model_index){ return $this->vendor_list[$vendor_index]->getModelFileName($model_index);	}
	public function getModelFilenameByName($formName)	{
		$array = explode('-',$formName);
		$vendor = trim($array[0]);
		$model = trim($array[1]);
		
		for($i=0; $i<$this->vendors_n; $i++ ) {
			if($this->vendor_list[$i]->isItYou($vendor)) {
				if($this->vendor_list[$i]->isItYour($model)) {
					return $this->vendor_list[$i]->getModelFilenameByName($model);
				} else return "ERRORRRR - model name wrong!!";
			}
		}
	}
	public function fileExist(){return $this->fileExist;}
}


class B2bCpeVendor
{
	protected $name = "empty";
	protected $models_number = 0;
	protected $model_list;  //contains name and filename for every B2B CPE loaded from model_list.xml
	
	function __construct($cpe_vendor_name, $models_number) {
		$this->name = $cpe_vendor_name;
		$this->models_number = $models_number;
	}
	public function isItYou($name) {
		if($name == $this->name) 
			return true;
		else 
			return false;
	}
	public function isItYour($model) {
		$mine = false;
		for ($i = 0; $i < $this->models_number; $i++) {
			$mine = $this->model_list[$i]->isItYou($model);
			if($mine) 
				break;
		}
	return $mine;
}
	public function setModel($model_name, $model_filename){ $this->model_list[] = new B2BCpeModel($model_name, $model_filename); 	}
	public function getName(){ return $this->name; }
	public function getModelsNumber()	{ return $this->models_number; }
	public function getModelName($index){ return $this->model_list[$index]->getName(); }
	public function getModelFilename($index){ return $this->model_list[$index]->getFileName(); }
	public function getModelFilenameByName($modelName){
		for($i=0; $i< $this->models_number; $i++)
		{
			if($this->model_list[$i]->isItYou($modelName))
					return $this->model_list[$i]->getFileName();
		}
	}
}


class B2BCpeModel
{
	protected $name;
	protected $file_name;
	
	function __construct($model_name, $model_filename)	{
		$this->name = $model_name;
		$this->file_name = $model_filename;
		//echo "<p>B2BCpeModel::__constructor -- name: " .$model_name . " Filename: " . $model_filename . "</p>";
	}
	public function getName(){ return $this->name; }
	public function getFileName(){ return (string)$this->file_name;}
	public function isItYou($name){
		if($name == $this->name) return true;
		else 	return false;
	}
}

	?>
