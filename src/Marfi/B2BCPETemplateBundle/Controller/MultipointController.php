<?php


namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;
use Marfi\B2BCPETemplateBundle\Entity\MultipointTask;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class MultipointController extends Controller
{
	protected $fileName;
	protected $simcalls;
	protected $customerId;
	protected $numberOfPorts;
	protected $userXMLModel;
	protected $vendor;
	protected $model;
	protected $xmlHandler;
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$this->fileName = $session->get('filename');
		$this->customerId = $session->get('customerID');
		$this->simcalls = $session->get('simcalls');

		$this->xmlHandler = new B2BCpeModelXmlHandler($this->fileName);
		if($this->xmlHandler->fileExist()) 	{
			$this->numberOfPorts = $this->xmlHandler->getPortsNumber();
			$this->vendor = $this->xmlHandler->getVendor();
			$this->model = $this->xmlHandler->getModelName();
			
			$this->userXMLModel = new userXML($this->vendor, $this->model, $this->simcalls, $this->customerId);
			$modelPortsArray = $this->xmlHandler->getModelPortArray();
			foreach ($modelPortsArray as $port){ $this->userXMLModel->addModelPort($port); }
			$session=$this->get('session');
			$session->set('userxml',  $this->userXMLModel );

			if($this->xmlHandler->hasBRIPorts()){
				$task = new MultipointTask();
	
				$form = $this->createFormBuilder($task);
				foreach ($this->xmlHandler->getBRIportsNameArray() as $value){
						$checkBoxName = $value;
						$checkList['choices'][$checkBoxName] = $checkBoxName; 
				}
				$checkList['expanded'] = true;
				$checkList['multiple'] = true;
				$checkList['label'] = 'Set (if needed) point to multipoint on ISDN ports: ';
				$checkList['required'] = true;
				$form = $form->add('portList', 'choice', $checkList)
										->getForm();
				if($request->getMethod()=='POST'){
					$form->bindRequest($request);
					if($form->isValid()){
						$this->userXMLModel->setMultipointBRIports($task->getPortList());

						$session=$this->get('session');
						$session->set('userxml',  $this->userXMLModel );
						$nextURL = $this->generateUrl('newpui', array('filename' => 'newpui'));
						echo "<div class=\"wrap\"><h2 style=\"color:red\">Summary</h2>";
						$responsepage = "<p><form method=\"link\" action=\"".$nextURL. "\"><input type=\"submit\" value=\" Next\"></form></p></div>";
						$this->userXMLModel->printUserXML();
						return new Response($responsepage);
					}
				}  // GET
					return $this->render('MarfiB2BCPETemplateBundle:Default:multipointForm.html.twig', 
										array('multipoint_form' => $form->createView(),));
			} else { //no BRI ports in this model
					$this->userXMLModel->printUserXML();
					$nextURL = $this->generateUrl('singlenumber', array('filename' => 'singlenumber'));
					$responsepage = "<h2 style=\"color:red\">Warning!!</h2>
												<p>No BRI ports in this device (check configuration XML file), you cannot choose multipoint settings, GNR and multinumbers. Jump directly into single number configuration</p>
												<p><form method=\"link\" action=\"".$nextURL. "\"><input type=\"submit\" value=\" Next\"></form></p>";
					return new Response($responsepage);	
			}
		} else { //XML file does not exist
				$nextURL = $this->generateUrl('MarfiB2BCPETemplateBundle_homepage');
				$responsepage = "<h2 style=\"color:red\">Error!!</h2>
											<p>Not valid or unexisting XML file: " .$this->fileName. "<br>Please check configuration XML file and press next to move back to start page </p>
											<p><form method=\"link\" action=\"".$nextURL. "\"><input type=\"submit\" value=\" Back to start page\"></form></p>";
				return new Response($responsepage);	
		}
	}
}


?>
