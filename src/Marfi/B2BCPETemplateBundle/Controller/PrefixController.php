<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\PrefixcreationTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class PrefixController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');

		// TO DO  - aggiungere controllo su porte già configurate
		
		$task = new PrefixcreationTask($xmlUser);
		foreach ($xmlUser->getNoBackupPortNamesArray() as $value){	
			$checkBoxName = $value;
			$checkList['choices'][$checkBoxName] = $checkBoxName; 
		}
		$checkList['label'] = 'Check port list';
		$checkList['expanded'] = true;
		$checkList['multiple'] = true;
		$checkList['required'] = true;
		$checkList['error_bubbling']=true;

		$form = $this->createFormBuilder($task)->add('portList', 'choice', $checkList)	
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
			//$task->printData();
			if($form->isValid()){
				if($task->hasIncomingPrefix()){
					foreach ($task->getPortList() as $port)
						$xmlUser->addIncomingPrefixToPort($port, $task->getIncomingCaller(), $task->getIncomingCallerType(),$task->getIncomingCalled(), $task->getIncomingCalledType());
				}
				if($task->hasOutgoingPrefix()){
					foreach ($task->getPortList() as $port)
						$xmlUser->addOutgoingPrefixToPort($port, $task->getOutgoingCaller(), $task->getOutgoingCallerType(),$task->getOutgoingCalled(), $task->getOutgoingCalledType());
				}
				$session->set('userxml',  $xmlUser );
//				return new Response();
				return $this->redirect($this->generateUrl('prefix'));			
			}
 		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:prefixForm.html.twig', 
								array('prefix_form' => $form->createView(),
										'summary'=>$xmlUser));
	}
}

?>
