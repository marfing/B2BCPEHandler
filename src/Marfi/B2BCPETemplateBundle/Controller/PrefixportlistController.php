<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\PrefixportlistTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class PrefixportlistController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');

		// TO DO  - aggiungere controllo su porte già configurate
		
		$task = new PrefixportlistTask($xmlUser);
		foreach ($xmlUser->getPortNamesArray() as $value){	
			$checkBoxName = $value;
			$checkList['choices'][$checkBoxName] = $checkBoxName; 
		}
		$checkList['label'] = 'Check port list';
		$checkList['expanded'] = true;
		$checkList['multiple'] = false;
		$checkList['required'] = true;
		$checkList['error_bubbling']=true;

		$form = $this->createFormBuilder($task)->add('portList', 'choice', $checkList)	->getForm();
		
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			if($form->isValid()){
				$port = str_replace('/','-',$task->getPortList());
				return $this->redirect($this->generateUrl('prefixcreation', array('portname' => $port)));
				//$task->getPortList())));
			}
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:portListForm.html.twig', 
								array('prefixportlist_form' => $form->createView(),
										'summary'=>$xmlUser));

	}
}

?>
