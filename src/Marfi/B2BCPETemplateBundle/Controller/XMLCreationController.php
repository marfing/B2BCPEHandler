<?php

namespace Marfi\B2BCPETemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\B2BCPETemplateBundle\XMLHandlers\userXML;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;

class XMLCreationController extends Controller
{
	
	public function indexAction(Request $request)
	{
		$session = $this->get('session');		
		$xmlUser = 	$session->get('userxml');
		$xmlstr = '<?xml version="1.0" encoding="ISO-8859-1"?>
			<customer_id>
				<model>
					<sim_calls></sim_calls>
					<ports></ports>
					<multinumbers></multinumbers>
					<cli_services></cli_services>
				</model>
			</customer_id>';
		$xmlFile = simplexml_load_string($xmlstr);
		$xmlFile->customer_id['value'] = $xmlUser->getCustomerID();
		$xmlFile->customer_id->model['vendor']=$xmlUser->getVendor();
		$xmlFile->customer_id->model['model']=$xmlUser->getModel();
		$xmlFile->customer_id->model->sim_calls=$xmlUser->getSimCalls();
		$xmlFile->customer_id->model->ports['number_of_ports']=$xmlUser->getNumberOfPorts();
		foreach($xmlUser->getPortNamesArray() as $portname){
			$port = $xmlFile->customer_id->model->ports->addChild('port'); 
			$port['name'] = $portname;
			$port['type']=$xmlUser->getPortType($portname);
			$port['voice_enabled']=(($xmlUser->isPortEnabled($portname)) ? 'true': 'false');
			$port['multipoint']=(($xmlUser->isPortMultipoint($portname)) ? 'true': 'false');
			$port['PoBRI']=(($xmlUser->hasPortPoBRI($portname)) ? 'true': 'false');
			if($xmlUser->hasPortBackupAvailable($portname))
				$port['backup_enabled']=(($xmlUser->hasPortBackupEnabled($portname)) ? 'true': 'false');
			if($xmlUser->hasPortSingleNumber($portname)){
				$single_number=$port->addChild('single_number');
				$single_number = $xmlUser->getPortSingleNumber($portname);
			}
			if($xmlUser->hasPortGnr($portname)){
				$gnr=$port->addChild('GNR');
				$gnr['root'] = $xmlUser->getPortGnrRoot($portname);
				if($xmlUser->hasPortGnrDid($portname)){
					
					//TO DO - da completare questi due metodi
					$gnr['DID'] = $xmlUser->getPortGnrDid($portname);
					$gnr['extensiondigits'] = $xmlUser->getPortGnrExtension($portname);
				}
			}
		}
		
		echo var_dump($xmlFile);
		return new Response();
		//return $this->render('MarfiB2BCPETemplateBundle:Default:summary.html.twig', array('summary'=>$xmlUser));
	}
}

/*
foreach ($movies->movie[0]->rating as $rating) {
    switch((string) $rating['type']) { // Get attributes as element indices
		*/

?>


