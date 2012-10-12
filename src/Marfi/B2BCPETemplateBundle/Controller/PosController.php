<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\EnableServiceTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class PosController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		$task = new EnableServiceTask($xmlUser);
		$form = $this->createFormBuilder($task)
						->add('enable','checkbox',array('label' => 'enable pos','required' => true,	'error_bubbling'=>true))
						->add('howMany','integer',array('label' => 'Insert how many CLIs','required' => true,'error_bubbling'=>true))->getForm();
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			if($form->isValid()){
//				echo "Form is valid";
				if($task->getEnable())
					return $this->redirect($this->generateUrl('poscli', array('howmany' =>$task->getHowMany())));
				else
					return $this->redirect($this->generateUrl('prefix', array('filename' =>'prefix')));
			}
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:posForm.html.twig', array('pos_form' => $form->createView(),
																																				'error_msg'=>'',
																																				'summary'=>$xmlUser));
	}
}

?>
