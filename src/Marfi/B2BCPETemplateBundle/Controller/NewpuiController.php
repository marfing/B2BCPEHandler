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
		
		echo "<div class=\"summary\"><h2 style=\"color:red\">Summary</h2>";
		$xmlUser->printUserXML();
		echo "</div>";
		$task = new newPuiTask();
		
		$checkList['choices']['singlenumber']='Single Number';
		if($xmlUser->hasBRIPorts()){
			$checkList['choices']['multinumber']='Multi Number';
			if($xmlUser->getNumberOfBRIPorts() > 1)
				$checkList['choices']['gnr']='GNR';
		}


		$checkList['expanded'] = true;
		$checkList['multiple'] = false;
		$checkList['required'] = true;
		$checkList['label'] = 'PUI Types';
		
		$form = $this->createFormBuilder($task)	->add('puiType','choice',$checkList);
		$form = $form->getForm();

		if($request->getMethod()=='POST'){
			$form->bindRequest($request);

			switch ($task->getPuiType()) {
				case 'gnr':
					return $this->redirect($this->generateUrl('gnr', array('filename' => 'gnr')));
					break;
				case 'singlenumber':
					return $this->redirect($this->generateUrl('singlenumber', array('filename' => 'singlenumber')));
					break;
				case 'multinumber':
					echo "TO BE DONE!!!";
					return $this->redirect($this->generateUrl('multinumber', array('filename' => 'multinumber')));
					break;
			}
			$responsepage = $task->printData();
			return new Response($responsepage);
		} else  //GET
			return $this->render('MarfiB2BCPETemplateBundle:Default:newpuiForm.html.twig', 
								array('newpui_form' => $form->createView(),));
	}
 }

class newPuiTask{
		protected $puitype;
		
		public function getPuiType(){ return $this->puitype; }
		public function setPuiType($puiType){ $this->puitype = $puiType; }
		public function printData(){ echo "<table border=\"1\"><tr><td>PuiType: </td><td>" . $this->puitype. "</td></tr></table>" ; }
}







?>
