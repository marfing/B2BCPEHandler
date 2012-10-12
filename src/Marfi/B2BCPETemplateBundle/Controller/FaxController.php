<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\EnableTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class FaxController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		$task = new EnableTask($xmlUser->howManyCli());
		$form = $this->createFormBuilder($task)
						->add('enable','checkbox',array('label' => 'enable fax',
																	'required' => false,
																	'error_bubbling'=>true))
						->add('howMany','integer',array('label' => 'Insert how many PUIs',
																		'required' => false,
																		'error_bubbling'=>true))
						->getForm();
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			if($form->isValid()){
				if($task->getEnable())
					return $this->redirect($this->generateUrl('faxcli', array('howmany' =>$task->getHowMany())));
				else
					return $this->redirect($this->generateUrl('pos', array('filename' =>'pos')));
			}
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:faxForm.html.twig', array('fax_form' => $form->createView()));
	}
}

?>
