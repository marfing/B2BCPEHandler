<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\SingleNumberTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class SinglenumberController extends Controller
{

	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		$task = new SingleNumberTask($xmlUser);
		$form = $this->createFormBuilder($task)
						->add('singleNumber','text',array('label' => 'Insert PUI number',
																		'required' => true,
																		'error_bubbling'=>true))
						->add('bind','checkbox',array('label' => 'Bonding aggregation',
																	'required' => false,
																	'error_bubbling'=>true));
		foreach ($xmlUser->getPortNamesArrayWithoutSingleNumber() as $value){	
			$checkBoxName = $value;
			$checkList['choices'][$checkBoxName] = $checkBoxName; 
		}
		$checkList['label'] = 'Check port list';
		$checkList['expanded'] = true;
		$checkList['multiple'] = true;
		$checkList['required'] = true;
		$checkList['error_bubbling']=true;
		$form = $form->add('portList', 'choice', $checkList)
								->getForm();
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			if($form->isValid()){
				foreach ($task->getPortList() as $port)
					$xmlUser->setSingleNumber($task->getSingleNumber(), $port);
				$session->set('userxml',  $xmlUser );
				return $this->redirect($this->generateUrl('newpui', array('filename' => 'nextpui')));
			} 
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:singlenumber.html.twig', 
								array('singlenumber_form' => $form->createView(),
										'summary'=>$xmlUser));
	}
}


?>
