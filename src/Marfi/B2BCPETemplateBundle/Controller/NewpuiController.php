<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class NewpuiController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = $session->get('userxml');
		$task = new newPuiTask();
		
		$checkList['choices']['GNR']='gnr';
		$checkList['choices']['SingleNumber']='singlenumber';
		$checkList['choices']['MultiNumber']='multinumber';
		$checkList['expanded'] = true;
		$checkList['multiple'] = false;
		$checkList['required'] = true;
		$checkList['label'] = 'PUI Types';
		
		$form = $this->createFormBuilder($task)	->add('puitype','choice',$checkList)->getForm();

		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
/*			$portError = false;
			$portErrorList;
			$task->printData();
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
			}*/
			$responsepage = $task->printData();
			return new Response($responsepage);
		} else { //GET
			//$this->userXMLModel->printUserXML();
			return $this->render('MarfiB2BCPETemplateBundle:Default:newpuiForm.html.twig', 
								array('newpui_form' => $form->createView(),));
		}
	//	return new Response("<h2>single number</h2>");
	}
 }

class newPuiTask{
		protected $puitype;
		
		public function getPuiType(){ return $this->puitype; }
		public function setPuiType($puiTypeList){ 	$this->puitype = $puiTypeList; }
		public function printData(){
			echo "<table border=\"1\"><tr><td>PuiTypeList: </td><td>" ;
				foreach ($this->puitype as $type) 
					echo $type . "<br>";
			echo "</td></tr></table>";	
		}
}







?>
