<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\PrefixcreationTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class PrefixcreationController extends Controller
{
	
	public function indexAction(Request $request, $portname)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		// TO DO  - aggiungere controllo su porte già configurate
		$port = str_replace('-','/',$portname);

		$task = new PrefixcreationTask($xmlUser);
		$form = $this->createFormBuilder($task)
							->add('incoming','text')
							->add ('incomingtype', 'choice', array('choices'=>array('add' =>'add',
																												'del'=>'del'),
																					'required' => true,
																					'expanded' => true,
																					'multiple'=>false))
							->add('outgoing','text')
							->add ('outgoingtype', 'choice', array('choices'=>array('add' =>'add',
																												'del'=>'del'),
																					'required' => true,
																					'expanded' => true,
																					'multiple'=>false))->getForm();
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			if($form->isValid()){
//				echo "Choise: " .$task->getPortList();
//				return new Response();
//				return $this->redirect($this->generateUrl('prefixcreation', array('portname' => $task->getPortList())));				
				return new Response('Port: ' .$port. " -- " .$task->printData());
			}
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:prefixcreationForm.html.twig', 
								array('prefixcreation_form' => $form->createView(),
										'summary'=>$xmlUser,
										'portname'=>$portname,
										'port'=>$port));

	}
}

?>
