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
							->add('incomingcaller','text', array('label'=>'Incoming caller prefix (from customer to CPE)',
																				'required'=>false,
																				'max_length'=>4))
							->add ('incomingcallertype', 'choice', array('choices'=>array('add' =>'add',
																												'del'=>'del'),
																					'required' => false,
																					'expanded' => true,
																					'multiple'=>false,
																					'label'=>'Type'))
							->add('incomingcalled','text', array('label'=>'Incoming called prefix (from customer to CPE)',
																				'required'=>false))
							->add ('incomingcalledtype', 'choice', array('choices'=>array('add' =>'add',
																												'del'=>'del'),
																					'label'=>'Type',
																					'required' => false,
																					'expanded' => true,
																					'multiple'=>false))
							->add('outgoingcaller','text', array('label'=>'Outgoing caller prefix (from CPE to customer)',
																				'required'=>false))
							->add ('outgoingcallertype', 'choice', array('choices'=>array('add' =>'add',
																												'del'=>'del'),
																					'label'=>'Type',
																					'required' => false,
																					'expanded' => true,
																					'multiple'=>false))
							->add('outgoingcalled','text', array('label'=>'Outgoing called prefix (from CPE to customer)',
																				'required'=>false))
							->add ('outgoingcalledtype', 'choice', array('choices'=>array('add' =>'add',
																												'del'=>'del'),
																					'label'=>'Type',
																					'required' => false,
																					'expanded' => true,
																					'multiple'=>false))	->getForm();
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			if($form->isValid()){
//				echo "port variable: " .$port. "  -- portname variable: " .$portname;
//				echo "<br>task data: " .$task->printData(). "<br>";
				if($task->hasIncomingPrefix())
					$xmlUser->addIncomingPrefixToPort($port, $task->getIncomingCaller(), $task->getIncomingCallerType(),$task->getIncomingCalled(), $task->getIncomingCalledType());
				if($task->hasOutgoingPrefix())
					$xmlUser->addOutgoingPrefixToPort($port, $task->getOutgoingCaller(), $task->getOutgoingCallerType(),$task->getOutgoingCalled(), $task->getOutgoingCalledType());
				$session->set('userxml',  $xmlUser );
				return $this->redirect($this->generateUrl('prefixportlist'));			
//				return new Response();
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
