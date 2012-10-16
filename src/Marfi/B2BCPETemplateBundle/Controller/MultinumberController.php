<?php


namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\MultiNumberTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class MultinumberController extends Controller
{

	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		$task = new MultiNumberTask($xmlUser);
		$form = $this->createFormBuilder($task)
							->add('bind','checkbox',array('label' => 'Bonding aggregation',
																		'required' => false,
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
							->add('cli1','text', array('label'=>'Insert cli1',
																'required' => true,
																'error_bubbling'=>true))
							->add('cli2','text', array('label'=>'Insert cli2',
																'required' => true,
																'error_bubbling'=>true))
							->add('cli3','text', array('label'=>'Insert cli3',
																'required' => false,
																'error_bubbling'=>true))
							->add('cli4','text', array('label'=>'Insert cli4',
																'required' => false,
																'error_bubbling'=>true))
							->add('cli5','text', array('label'=>'Insert cli5',
																'required' => false,
																'error_bubbling'=>true))
							->add('cli6','text', array('label'=>'Insert cli6',
																'required' => false,
																'error_bubbling'=>true))
							->getForm();
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			if($form->isValid()){
				echo "MultinumberController::index: " .var_dump($task->getCliList()). "<br><br>";
				$xmlUser->setMultinumber($task->getBind(), $task->getPortList(), $task->getCliList());
				return $this->redirect($this->generateUrl('newpui', array('filename' => 'newpui')));
			}
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:multinumberForm.html.twig', array('multinumber_form' => $form->createView(),
																																							'summary'=>$xmlUser));
	}
}

?>
