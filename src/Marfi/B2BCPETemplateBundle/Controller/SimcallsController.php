<?php


namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCpeModelXmlHandler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SimcallsController extends Controller
{
		protected $fileName;
		protected $xmlHandler;

		public function simcallsAction(Request $request)
		{
			$session = $this->get('session');		
			//echo "<p stype=\"font-size:10px\">Filename: " .$session->get('filename'). "</p>";
			$this->fileName = $session->get('filename');
			//echo "CustomerID: " .$session->get('customerID'). "<br>";
			$this->xmlHandler = new B2BCpeModelXmlHandler($this->fileName);
			if($this->xmlHandler->fileExist()) 	{
				if($this->xmlHandler->hasSimCalls()){
					$simCalls = $this->xmlHandler->getSimCalls();
					$simCallsData = array('simCallsForm' => 'Choose Sim Calls');
					$simCallsForm = $this->createFormBuilder($simCallsData);
					foreach ($simCalls as $value){
						$checkBoxName = $value;
						$simCallsChoisesList['choices'][$checkBoxName] = $checkBoxName; 
					}
					$simCallsChoisesList['expanded'] = true;
					$simCallsChoisesList['multiple'] = false;
					$simCallsChoisesList['label']='Choose how many sim calls';
					$simCallsForm = $simCallsForm->add('SimCalls', 'choice', $simCallsChoisesList)
											->getForm();
					if($request->getMethod()=='POST'){
						$simCallsForm->bindRequest($request);
						$data = $simCallsForm->getData();
						echo "<p>Sim calls: " .$data['SimCalls']. "</p>";
						$session->set('simcalls', $data['SimCalls']);
						return $this->redirect($this->generateUrl('multipoint', array('filename' => 'multipoint')));
					} else return $this->render('MarfiB2BCPETemplateBundle:Default:simcallsForm.html.twig', 
										array('simcalls_form' => $simCallsForm->createView(),));
				}  else {// xml file doesn't have sim calls tag!!!
					$nextURL = $this->generateUrl('MarfiB2BCPETemplateBundle_homepage');
					$responsepage = "<h2 style=\"color:red\">Error!!</h2>
												<p>No sim calls informations in this device (check configuration XML file), you cannot go on with configuration - Check XML file and press next to move back to start page </p>
												<p><form method=\"link\" action=\"".$nextURL. "\"><input type=\"submit\" value=\" Back to start page\"></form></p>";
					return new Response($responsepage);	
				}
			} else  {
//				echo "<h2>Sorry there is something wrong!!</h2>
//						<p>The XML model file: " .$this->fileName. " is not correct or no more available</p>";
				$nextURL = $this->generateUrl('MarfiB2BCPETemplateBundle_homepage');
				$responsepage = "<h2 style=\"color:red\">Error!!</h2>
											<p>Not valid or unexisting XML file: " .$this->fileName. "<br>Please check configuration XML file and press next to move back to start page </p>
											<p><form method=\"link\" action=\"".$nextURL. "\"><input type=\"submit\" value=\" Back to start page\"></form></p>";
				return new Response($responsepage);	
			}
		}
}
	
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
