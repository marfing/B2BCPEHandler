<?php


namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\singleNumberTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class MultinumberController extends Controller
{
	protected $xmlUser;
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$this->xmlUser = 	$session->get('userxml');
		$task = new MultiNumberTask();
		
			
		$form = $this->createFormBuilder($task)
						->add('howMany','integer',array('label' => 'Insert how many PUIs',
																		'required' => true,
																		'error_bubbling'=>true))
						->add('bind','checkbox',array('label' => 'Bonding aggregation',
																	'required' => false,
																	'error_bubbling'=>true));
		foreach ($this->xmlUser->getBRIPortNamesArray() as $value){	
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
				/*
				foreach ($task->getPortList() as $port){
					$this->xmlUser->setSingleNumber($task->getSingleNumber(), $port);
				 */
				}
				$session->set('userxml',  $this->xmlUser );
				return $this->redirect($this->generateUrl('multinumber', array('filename' => 'multinumber')));
			} 
		return $this->render('MarfiB2BCPETemplateBundle:Default:multinumber.html.twig', 
								array('multinumber_form' => $form->createView(),));

	}
}

?>
