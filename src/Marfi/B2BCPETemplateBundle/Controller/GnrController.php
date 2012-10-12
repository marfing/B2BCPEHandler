<?php


namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\GnrTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class GnrController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
	
		$task = new GnrTask($xmlUser);
		$form = $this->createFormBuilder($task)
						->add('rootNumber','text',array('label' => 'Insert root number', //text and not integer because otherwise forst zero will be cutted
																		'required' => true,
																		'error_bubbling'=>true))
						->add('bind','checkbox',array('label' => 'Bonding aggregation',
																	'required' => false,
																	'error_bubbling'=>true))
						->add('did','checkbox',array('label' => 'DID enabled',  
																	'required' => false,
																	'error_bubbling'=>true))
						->add('gnrExtension','integer',array('label' => 'Insert gnr extension digits (DID must be enabled)', //max = 3
																				'required' => true,
																				'error_bubbling'=>true));
		
		foreach ($xmlUser->getBRIPortNamesArray() as $value){	
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
				foreach ($task->getPortList() as $port){
					$xmlUser->setGnr($task->getRootNumber(), $task->getDid(), $task->getGnrExtension(), $port);
				}
				$session->set('userxml',  $xmlUser );
				return $this->redirect($this->generateUrl('newpui', array('filename' => 'nextpui')));
			} 
		}
	return $this->render('MarfiB2BCPETemplateBundle:Default:gnr.html.twig', array('gnr_form' => $form->createView(),
																																'summary'=>$xmlUser));
	}
}


?>
