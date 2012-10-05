<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\singleNumberTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class SinglenumberController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		
		$xmlUser = $session->get('userxml');
		
		$task = new SingleNumberTask();
		$form = $this->createFormBuilder($task)
						->add('singleNumber','text',array('label' => 'Insert PUI number',
																		'required' => true,
																		'error_bubbling'=>true))
						->add('bind','checkbox',array('label' => 'Bonding aggregation',
																	'required' => false,
																	'error_bubbling'=>true));
		foreach ($xmlUser->getPortNamesArray() as $value){
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
		
		// display errors
		foreach ($session->getFlashBag()->get('error', array()) as $message) {
			echo "<div class='flash-error'>$message</div>";
		}
		
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			if($form->isValid()){
				return new Response('Form valid');
			} 
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:singleNumber.html.twig', 
								array('singlenumber_form' => $form->createView(),));
	}
}



?>
