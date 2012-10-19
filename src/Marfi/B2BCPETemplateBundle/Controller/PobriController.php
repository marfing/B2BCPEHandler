<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\PortlistTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class PobriController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');

		// TO DO  - aggiungere controllo su porte già configurate
		
		$task = new PortlistTask($xmlUser);
		foreach ($xmlUser->getNoBackupBRIPortNamesArray() as $value){	
			$checkBoxName = $value;
			$checkList['choices'][$checkBoxName] = $checkBoxName; 
		}
		$checkList['label'] = 'Check port list';
		$checkList['expanded'] = true;
		$checkList['multiple'] = true;
		$checkList['required'] = true;
		$checkList['error_bubbling']=true;

		$form = $this->createFormBuilder($task)->add('portList', 'choice', $checkList)	->getForm();
		
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			if($form->isValid()){
				$xmlUser->setPoBRI($task->getPortList());
//				$session->set('userxml',  $xmlUser );
				return $this->redirect($this->generateUrl('summary'));
			}
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:pobriForm.html.twig', 
								array('portlist_form' => $form->createView(),
										'summary'=>$xmlUser));
	}
}

?>
