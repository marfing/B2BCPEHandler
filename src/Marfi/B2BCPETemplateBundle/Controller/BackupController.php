<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\PortlistTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class BackupController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		$data = array();
		$form = $this->createFormBuilder($data)->add('backup','checkbox', array('label'=>'Enable backup in default port',
																															'required'=>false))->getForm();
		
		//creare task che verifichi che la porta non sia stata usata conm qualche PUI, altrimenti non si può attivare il backup!!!
		// oppure spostare questa configurazione subito dopo le sim calls per eliminare la porta dalla configurazione delle PUI
		
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			$data = $form->getData();
			if($data){
				$xmlUser->setBackup();
				$session->set('userxml',  $xmlUser );
			}
			return $this->render('MarfiB2BCPETemplateBundle:Default:summary.html.twig', array('error_msg'=>'','summary'=>$xmlUser));
			}
		return $this->render('MarfiB2BCPETemplateBundle:Default:backupForm.html.twig', array('backup_form' => $form->createView(),
																																		'error_msg'=>'',
																																		'summary'=>$xmlUser));
	}
}

?>
