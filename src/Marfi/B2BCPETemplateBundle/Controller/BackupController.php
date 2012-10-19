<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;
use Marfi\B2BCPETemplateBundle\Entity\PortlistTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class BackupController extends Controller
{
	public function indexAction(Request $request)
	{

		$session = $this->get('session');		
		$fileName = $session->get('filename');
		$customerId = $session->get('customerID');
		$simcalls = $session->get('simcalls');

		$xmlHandler = new B2BCpeModelXmlHandler($fileName);
		if($xmlHandler->fileExist()) 	{
			$vendor = $xmlHandler->getVendor();
			$model = $xmlHandler->getModelName();
			
			$userXML = new userXML($vendor, $model, $simcalls, $customerId);
			$modelPortsArray = $xmlHandler->getModelPortArray();
			foreach ($modelPortsArray as $port){ $userXML->addModelPort($port); }
			$session=$this->get('session');
			$session->set('userxml',  $userXML);
		} else return Response();
		
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		$label = 'Enable backup in port ' .$userXML->getBackupPort();
		$data = array();
		$form = $this->createFormBuilder($data)->add('backup','checkbox', array('label'=>$label,
																															'required'=>true))->getForm();
		
		//creare task che verifichi che la porta non sia stata usata conm qualche PUI, altrimenti non si può attivare il backup!!!
		// oppure spostare questa configurazione subito dopo le sim calls per eliminare la porta dalla configurazione delle PUI
		
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			$data = $form->getData();
			if($data){
				$xmlUser->setBackup();
				$session->set('userxml',  $xmlUser );
			}
			return $this->redirect($this->generateUrl('multipoint', array('filename' => 'multipoint')));
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:backupForm.html.twig', array('backup_form' => $form->createView(),
																																		'error_msg'=>'',
																																		'summary'=>$xmlUser));
	}
}

?>
