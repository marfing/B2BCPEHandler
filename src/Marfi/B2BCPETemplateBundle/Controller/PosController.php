<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\EnableTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class PosController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		
		$task = new EnableTask();
		$form = $this->createFormBuilder($task)
						->add('enable','checkbox',array('label' => 'enable pos',
																	'required' => false,
																	'error_bubbling'=>true))
						->add('howMany','integer',array('label' => 'Insert how many CLIs',
																		'required' => false,
																		'error_bubbling'=>true))
						->getForm();

		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			if($form->isValid()){
				if($task->getEnable())
					return $this->redirect($this->generateUrl('poscli', array('filename' =>$task->getHowMany())));
				else
					return $this->redirect($this->generateUrl('prefix', array('filename' =>'prefix')));
			}
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:posForm.html.twig', array('pos_form' => $form->createView()));
	}
}

?>
