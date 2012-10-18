<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class FaxCliController extends Controller
{
	
	public function indexAction(Request $request, $howmany)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		if($howmany == 0)
			$howmany = count($request->get('form')) -1;
		$data = array();
		$form = $this->createFormBuilder($data);
		for($i=1; $i<=$howmany; $i++)
			$form->add('cli'.$i,'text',array('label' => 'cli'.$i,'required' => true,'error_bubbling'=>true));
		$form = $form->getForm();
		if($request->getMethod()=='POST'){
			$form->bindRequest($request);
			$data = $form->getData();
			foreach ($data as $cli) {
				if(!$this->isE164($cli)){
					$errorMessage = "This CLI "  .$cli. " is not a valid italian number. Must be 0<area_code><customer_number>";
					return $this->render('MarfiB2BCPETemplateBundle:Default:cliListForm.html.twig', 
														array('list_form' => $form->createView(),
																'error_msg'=>$errorMessage,
																'servicetype'=>'fax',
																'summary'=>$xmlUser));
				}
				if(!$xmlUser->isGoodForService($cli)){
					$errorMessage = "This CLI "  .$cli. " is not configured or already in use with another service, so cannot be used for a fax service";
					return $this->render('MarfiB2BCPETemplateBundle:Default:cliListForm.html.twig', 
														array('list_form' => $form->createView(),
																'error_msg'=>$errorMessage,
																'servicetype'=>'fax',
																'summary'=>$xmlUser));
				}
			}
			$xmlUser->setFax($data);
			$session->set('userxml',  $xmlUser );
			return $this->redirect($this->generateUrl('pos', array('filename' =>'pos')));
		}
		return $this->render('MarfiB2BCPETemplateBundle:Default:cliListForm.html.twig', array('list_form' => $form->createView(),
																																		'error_msg'=>'',
																																		'summary'=>$xmlUser,
																																		'servicetype'=>'fax'));
	}

	public function isE164($cli){   /// not standards compliant i.e won't meet E.164 etc for validating international phone numbers
		trim($cli);
		if(!preg_match("/^[0]\d{3,12}$/",$cli)) return false;
		return true;
	}

}

?>
