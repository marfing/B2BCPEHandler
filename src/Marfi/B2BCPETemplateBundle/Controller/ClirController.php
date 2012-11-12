<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\EnableServiceTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class ClirController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		$task = new EnableServiceTask($xmlUser);
		$form = $this->createFormBuilder($task)
						->add('enable','checkbox',array('label' => 'enable clir','required' => true,	'error_bubbling'=>true))
						->add('forall','checkbox',array('label' => 'for all numbers', 'required' => false,	'error_bubbling'=>true))
						->add('howMany','integer',array('label' => 'Insert how many CLIs','required' => false,'error_bubbling'=>true))->getForm();
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			$data = $form->getData();
			if($form->isValid()){
				if($task->getEnable())
					if($task->getForall()){
						$xmlUser->setClirForall();
						$session->set('userxml',  $xmlUser );
						return $this->redirect($this->generateUrl('prefix', array('filename' =>'prefix')));
					}
					else
						return $this->redirect($this->generateUrl('clircli', array('howmany' =>$task->getHowMany())));
				else
					return $this->redirect($this->generateUrl('prefix', array('filename' =>'prefix')));
			}
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:clirForm.html.twig', array('clir_form' => $form->createView(),
																																				'error_msg'=>'',
																																				'summary'=>$xmlUser));
	}
}

?>
