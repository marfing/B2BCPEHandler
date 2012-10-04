<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class SinglenumberController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		
		$xmlUser = $session->get('userxml');
		
		$task = new singleNumberTask();
		$form = $this->createFormBuilder($task)
						->add('singleNumber','text',array('label' => 'Insert PUI number',
																		'required' => true))
						->add('bind','checkbox',array('label' => 'Bonding aggregation',
																	'required' => true));
		foreach ($xmlUser->getPortNamesArray() as $value){
			$checkBoxName = $value;
			$checkList['choices'][$checkBoxName] = $checkBoxName; 
		}
		$checkList['expanded'] = true;
		$checkList['multiple'] = true;
		$checkList['label'] = 'Check port list';
		$form = $form->add('portList', 'choice', $checkList)
								->getForm();
		// display errors
		foreach ($session->getFlashBag()->get('error', array()) as $message) {
			echo "<div class='flash-error'>$message</div>";
		}
		
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			$portError = false;
			$portErrorList;
			foreach($task->getPortList() as $port){
				if(!$xmlUser->setSingleNumber($task->getSingleNumber(),$port)){
					$portError = true;
					$portErrorList[] = $port;
				}
			}
			if($portError){
				$errorMessage = "<h2>There ports already have single number or CLI is not valid</h2><table border=\"1\"><tr>";
				foreach ($portErrorList as $port) 
					$errorMessage =  "<td>" . $port . "</td>";
				$errorMessage = $errorMessage . "</tr></table>";
				$session->getFlashBag()->add('error', $errorMessage);
				return $this->redirect($this->generateUrl('singlenumber', array('filename' => 'singlenumber')));
			} else {
				// TO DO - andare sull pagina che chiede se si vuole inserire una nuova PUI di tipo Single, Multi o GNR
			}
			//$responsepage = $task->printData();

			//return new Response($responsepage);
		} else { //GET
			//$this->userXMLModel->printUserXML();
			return $this->render('MarfiB2BCPETemplateBundle:Default:singleNumber.html.twig', 
								array('singlenumber_form' => $form->createView(),));
		}
	//	return new Response("<h2>single number</h2>");
	}
}

class singleNumberTask{
		protected $singleNumber;
		protected $portListNames;
		protected $bind;
		
		public function getSingleNumber(){ 	return $this->singleNumber; }
		public function setSingleNumber($singleNumber){ $this->singleNumber = $singleNumber; 	}
		public function getPortList(){ return $this->portListNames; }
		public function setPortList($portList){ 	$this->portListNames = $portList; }
		public function getBind(){ return $this->bind; }
		public function setBind($bind){ $this->bind = $bind; }
		public function printData(){
			echo "<table border=\"1\"><tr><td>SingleNumber: </td><td>" .$this->singleNumber . "</td></tr>";
			echo "<tr><td>Bind</td><td>" . (($this->bind)?"enabled":"disabled") . "</td></tr>";
			echo "<tr><td>Port List</td><td>";
				foreach ($this->portListNames as $port) 
					echo $port . "<br>";
			echo "</td></tr></table>";	
		}
	}




?>
