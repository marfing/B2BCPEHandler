<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\B2BCPEListXmlHandler;
use Marfi\B2BCPETemplateBundle\Controller\testSessionObject;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class DefaultController extends Controller
{
	protected $cpeListForm;
	protected $xmlCpeListReader;
	protected $selectedModel;
	protected $selectedModelFileName;
	protected $modelListFilename;
	protected $session;
	
    public function indexAction(Request $request) {
			$this->session = $this->get('session');
			$this->session->clear();
			
			/* test to be removed if it works
			$testSession = new testSessionObject();
			$testSession->name = 'marco';
			$testSession->surname = 'figus';
			
			$this->session->set('test',  $testSession );
			*/
			$this->loadModelListXML();
			
			$this->createModelListForm();
			
			if ($request->getMethod() == 'POST'){
				$this->getSelectedModelFilename($request);
				echo "<p>Model file name: " . $this->selectedModelFileName . "</p>";
				$this->session->set('filename',  $this->selectedModelFileName );
				return $this->redirect($this->generateUrl('modelroute', array('filename' => $this->selectedModelFileName)));
			} else {
				echo "<h1 style=\" font-family: arial \">B2B CPE Model List</h1>";
				return $this->render('MarfiB2BCPETemplateBundle:Default:index.html.twig', 
								array('model_form' => $this->cpeListForm->getForm()->createView(),));
			}
    }
	
	public function loadModelListXML()	{
			$this->modelListFilename = $_SERVER['DOCUMENT_ROOT'] . "xml/model_list.xml";
			$this->xmlCpeListReader = new B2bCpeListXmlHandler($this->modelListFilename);
	}
	public function createModelListForm(){
			$cpeModelData = array('registerForm' => 'Choose Cpe Model');
			$this->cpeListForm = $this->createFormBuilder($cpeModelData);
			
			for ($i=0;  $i< $this->xmlCpeListReader->getVendorsNumber(); $i++)	{
				for($j=0; $j<$this->xmlCpeListReader->getModelsNumber($i); $j++){
					$checkBoxName = $this->xmlCpeListReader->getVendorName($i) . " - " . $this->xmlCpeListReader->getModelName($i, $j);
					$cpeChoisesList['choices'][$checkBoxName] = $checkBoxName; 
				}
			}
			$cpeChoisesList['expanded'] = true;
//			$cpeChoisesList['required'] = false;
			$cpeChoisesList['multiple'] = false;
			$this->cpeListForm->add('Model', 'choice', $cpeChoisesList);
	}
	public function getSelectedModelFilename(Request $request){
				$this->selectedModel = $request->get('form');
				$this->selectedModel = $this->selectedModel['Model'];
				$this->selectedModelFileName = $this->xmlCpeListReader->getModelFileNameByName($this->selectedModel);
	}
}

