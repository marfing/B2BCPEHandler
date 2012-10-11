<?php


namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ModelController extends Controller
{
	protected $fileName;
	protected $xmlHandler;
	
    public function indexAction(Request $request)
    {
			$session = $this->get('session');
			$customerIDData = array('customerIDForm' => 'Insert Customer ID');
			$customerIDForm = $this->createFormBuilder($customerIDData)
							->add('CustomerID','text', array('label'=>'Insert customer ID',
																		'required' => true))
							->getForm();
			if($request->getMethod()=='POST')	{
					$customerIDForm->bindRequest($request);
					$data = $customerIDForm->getData();
					$session->set('customerID', (string)$data['CustomerID']);
					return $this->redirect($this->generateUrl('simcalls', array('filename' => 'simcalls')));
			} else {
				echo "Session filename:" .$session->get('filename'). "<br>";
				$this->fileName = $request->get('filename');
				return $this->render('MarfiB2BCPETemplateBundle:Default:customerForm.html.twig', 
									array('customer_form' => $customerIDForm->createView(),));
			}
		}
  }


?>
