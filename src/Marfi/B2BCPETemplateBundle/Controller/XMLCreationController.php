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
			else
				$port['backup_enabled']= 'false';

			$single_number=$port->addChild('single_number');
			if($xmlUser->hasPortSingleNumber($portname)){
				$single_number = $xmlUser->getPortSingleNumber($portname);
				$single_number['enabled']='true';
			} else $single_number['enabled']='false';

			$gnr=$port->addChild('GNR');
			if($xmlUser->hasPortGnr($portname)){
				$gnr['root'] = $xmlUser->getPortGnrRoot($portname);
				$gnr['enabled'] = 'true';
				if($xmlUser->hasPortGnrDid($portname)){
					$gnr['DID'] = $xmlUser->getPortGnrDid($portname);
					$gnr['extensiondigits'] = $xmlUser->getPortGnrExtension($portname);
				}
			} else $gnr['enabled'] = 'false';

			$incoming_prefix=$port->addChild('incoming_prefix');
			if($xmlUser->hasPortIncomingPrefix($portname)){
				$incoming_prefix['enabled']='true';
				if($xmlUser->hasPortIncomingCallerPrefix()){
					$caller_prefix=$incoming_prefix->addChild('caller_prefix');
					$caller_prefix['enabled']='true';
					$caller_prefix['type']=$xmlUser->getPortIncomingCallerPrefixType($portname);
					$caller_prefix=$xmlUser->getPortIncomingCallerPrefix();
				} else 	$caller_prefix['enabled']='false';
				if($xmlUser->hasPortIncomingCalledPrefix()){
					$called_prefix=$incoming_prefix->addChild('called_prefix');
					$called_prefix['enabled']='true';
					$called_prefix['type']=$xmlUser->getPortIncomingCalledPrefixType($portname);
					$called_prefix=$xmlUser->getPortIncomingCalledPrefix();
				} else 	$called_prefix['enabled']='false';
			} else $incoming_prefix['enabled']='false';

			$outgoing_prefix=$port->addChild('outgoing_prefix');
			if($xmlUser->hasPortOutgoingPrefix($portname)){
				$outgoing_prefix['enabled']='true';
				if($xmlUser->hasPortOutgoingCallerPrefix()){
					$caller_prefix=$outgoing_prefix->addChild('caller_prefix');
					$caller_prefix['enabled']='true';
					$caller_prefix['type']=$xmlUser->getPortOutgoingCallerPrefixType($portname);
					$caller_prefix=$xmlUser->getPortOutgoingCallerPrefix();
				} else 	$caller_prefix['enabled']='false';
				if($xmlUser->hasPortOutgoingCalledPrefix()){
					$called_prefix=$outgoing_prefix->addChild('called_prefix');
					$called_prefix['enabled']='true';
					$called_prefix['type']=$xmlUser->getPortOutgoingCalledPrefixType($portname);
					$called_prefix=$xmlUser->getPortOutgoingCalledPrefix();
				} else 	$called_prefix['enabled']='false';
			} else $outgoing_prefix['enabled']='false';
		} //fine foreach delle porte
		if($xmlUser->hasMultinumber()){
			$xmlFile->customer_id->model->multinumbers['enable']='true';
			$xmlFile->customer_id->model->multinumbers['how_many']=$xmlUser->getHowManyMultinumbers();
			for($i=0;  $i <= $xmlUser->getHowManyMultinumbers(); $i++){
				$multinumber = $xmlFile->customer_id->multinumbers->addChild('multinumber');
				foreach ($xmlUser->getMultinumberCliArray($i) as $clivalue){
					$cli = $multinumber->addChild('cli');
					$cli = $clivalue;
				}
				$ports=$multinumber->addChild('port');
				$ports['how_many']=count($xmlUser->getMultinumberPortsArray());
				foreach ($xmlUser->getMultinumbersPortsArray() as $portname){
					$ports = $multinumber->addChild('port');
					$ports = $portname;
				}
			} //end multinumber
		}  else { //no multinumbers
			$xmlFile->customer_id->model->multinumbers['enable']='false';
			$xmlFile->customer_id->model->multinumbers['how_many']='0';
		}

		$fax = $xmlFile->customer_id->model->cli_services->addChild('fax');
		if($xmlUser->hasFax()){
			$fax['enabled']='true';
			$fax['number_of_cli']=$xmlUser->getHowManyCliHasFax();
			foreach ($xmlUser->getCliFaxArray() as $clivalue){
				$cli = $fax->addChild('cli');
				$cli = $clivalue;
			}
		} else {
			$fax['enabled']='false';
			$fax['number_of_cli']=0;
		}
		
		$pos = $xmlFile->customer_id->model->cli_services->addChild('pos');
		if($xmlUser->hasPos()){
			$pos['enabled']='true';
			$pos['number_of_cli']=$xmlUser->getHowManyCliHasPos();
			foreach ($xmlUser->getCliPosArray() as $clivalue){
				$cli = $pos->addChild('cli');
				$cli = $clivalue;
			}
		} else {
			$pos['enabled']='false';
			$pos['number_of_cli']=0;
		}

		$clir = $xmlFile->customer_id->model->cli_services->addChild('clir');
		if($xmlUser->hasClir()){
			$clir['enabled']='true';
			$clir['number_of_cli']=$xmlUser->getHowManyCliHasClir();
			foreach ($xmlUser->getCliClirArray() as $clivalue){
				$cli = $clir->addChild('cli');
				$cli = $clivalue;
			}
		} else {
			$clir['enabled']='false';
			$clir['number_of_cli']=0;
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


