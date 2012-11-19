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
		$filename = $_SERVER['DOCUMENT_ROOT'] . "xml/customerXML.xml";

		$xmlFile = simplexml_load_file($filename);
//		echo var_dump($xmlFile);
						//->value = $xmlUser->getCustomerID();
		$xmlFile->project_manager['uname'] = $this->get('security.context')->getToken()->getUser()->getUserName();
		$xmlFile->customer_id['value'] = $xmlUser->getCustomerID();
//		echo "<br><br> After customer_id change: " .  var_dump($xmlFile);
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
//			$single_number=$port->single_number;
			if($xmlUser->hasPortSingleNumber($portname)){
				$single_number[0]['enabled']='true';
				$single_number[0] = $xmlUser->getPortSingleNumber($portname);
			} else $single_number[0]['enabled']='false';

			$gnr=$port->addChild('GNR');
			if($xmlUser->hasPortGnr($portname)){
				$gnr[0]['enabled'] = 'true';
				$gnr[0]['root'] = (string)$xmlUser->getPortGnrRoot($portname);
				if($xmlUser->hasPortGnrDid($portname)){
					$gnr[0]['DID'] = $xmlUser->getPortGnrDid($portname);
					$gnr[0]['extensiondigits'] = $xmlUser->getPortGnrExtension($portname);
				}
			} else $gnr[0]['enabled'] = 'false';

			$incoming_prefix=$port->addChild('incoming_prefix');
			if($xmlUser->hasPortIncomingPrefix($portname)){
				$incoming_prefix[0]['enabled']='true';
				if($xmlUser->hasPortIncomingCallerPrefix($portname)){
					$caller_prefix=$incoming_prefix[0]->addChild('caller_prefix');
					$caller_prefix[0]['enabled']='true';
					$caller_prefix[0]['type']=$xmlUser->getPortIncomingCallerPrefixType($portname);
					$caller_prefix[0]=$xmlUser->getPortIncomingCallerPrefix($portname);
				} else 	$caller_prefix[0]['enabled']='false';
				if($xmlUser->hasPortIncomingCalledPrefix($portname)){
					$called_prefix=$incoming_prefix->addChild('called_prefix');
					$called_prefix[0]['enabled']='true';
					$called_prefix[0]['type']=$xmlUser->getPortIncomingCalledPrefixType($portname);
					$called_prefix[0]=$xmlUser->getPortIncomingCalledPrefix($portname);
				} else 	$called_prefix[0]['enabled']='false';
			} else $incoming_prefix[0]['enabled']='false';

			$outgoing_prefix=$port->addChild('outgoing_prefix');
			if($xmlUser->hasPortOutgoingPrefix($portname)){
				$outgoing_prefix[0]['enabled']='true';
				if($xmlUser->hasPortOutgoingCallerPrefix($portname)){
					$caller_prefix=$outgoing_prefix[0]->addChild('caller_prefix');
					$caller_prefix[0]['enabled']='true';
					$caller_prefix[0]['type']=$xmlUser->getPortOutgoingCallerPrefixType($portname);
					$caller_prefix[0]=$xmlUser->getPortOutgoingCallerPrefix($portname);
				} else 	$caller_prefix[0]['enabled']='false';
				if($xmlUser->hasPortOutgoingCalledPrefix($portname)){
					$called_prefix=$outgoing_prefix[0]->addChild('called_prefix');
					$called_prefix[0]['enabled']='true';
					$called_prefix[0]['type']=$xmlUser->getPortOutgoingCalledPrefixType($portname);
					$called_prefix[0]=$xmlUser->getPortOutgoingCalledPrefix($portname);
				} else 	$called_prefix[0]['enabled']='false';
			} else $outgoing_prefix[0]['enabled']='false';
		} //fine foreach delle porte

		if($xmlUser->hasMultinumber()){
			$xmlFile->customer_id->model->multinumbers['enable']='true';
			$xmlFile->customer_id->model->multinumbers['how_many']=$xmlUser->getHowManyMultinumbers();
			for($i=0;  $i < $xmlUser->getHowManyMultinumbers(); $i++){
				$multinumber = $xmlFile->customer_id->model->multinumbers->addChild('multinumber');
				$counter=0;
				foreach ($xmlUser->getMultinumberCliArray($i) as $clivalue){
					$multinumber->cli[$counter] = $clivalue;
					$counter++;
				}
				$ports=$multinumber->addChild('ports');
				$ports['how_many']=count($xmlUser->getMultinumberPortsArray($i));
				$counter = 0;
				foreach ($xmlUser->getMultinumberPortsArray($i) as $portname){
					$ports->port[$counter] = $portname;
					$counter++;
				}
			} //end multinumber
		}  else { //no multinumbers
			$xmlFile->customer_id->model->multinumbers['enable']='false';
			$xmlFile->customer_id->model->multinumbers['how_many']='0';
		}

		if($xmlUser->hasServices()){
			$xmlFile->customer_id->model->services['enabled'] ='true';
			$fax = $xmlFile->customer_id->model->services->fax;
			if($xmlUser->hasFax()){
				$fax['enabled']='true';
				$fax['number_of_cli']=$xmlUser->getHowManyCliHasFax();
				$counter = 0;
				foreach ($xmlUser->getCliFaxArray() as $clivalue){
					$fax->cli[$counter] = $clivalue;
					$counter++;
				}
			} else {
				$fax['enabled']='false';
				$fax['number_of_cli']=0;
			}
		
			$pos = $xmlFile->customer_id->model->services->pos;
			if($xmlUser->hasPos()){
				$pos['enabled']='true';
				$pos['number_of_cli']=$xmlUser->getHowManyCliHasPos();
				$counter = 0;
				foreach ($xmlUser->getCliPosArray() as $clivalue){
					$cli = $pos->addChild('cli');
					$pos->cli[$counter] = $clivalue;
					$counter++;
				}
			} else {
				$pos['enabled']='false';
				$pos['number_of_cli']=0;
			}

			$clir = $xmlFile->customer_id->model->services->clir;
			if($xmlUser->hasClir()){
				$clir['enabled']='true';
				if($xmlUser->hasClirForAll()) 	$clir['number_of_cli']='all';
				else{
					$clir['number_of_cli']=$xmlUser->getHowManyCliHasClir();
					$counter = 0;
					foreach ($xmlUser->getCliClirArray() as $clivalue){
						$clir->cli[$counter] = $clivalue;
						$counter++;
					}
				}
			} else {
				$clir['enabled']='false';
				$clir['number_of_cli']=0;
			}
		} else {  //no services
			$xmlFile->customer_id->model->services['enabled'] ='false';
		}

		//Format XML to save indented tree rather than one line
		$dom = new \DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xmlFile->asXML());
		$date = date("Ymd\_His");
		$filename = $_SERVER['DOCUMENT_ROOT'] . "usersXML/" . $xmlUser->getCustomerID() . "_" . $xmlUser->getVendor() .  $xmlUser->getModel() .  "_" . $date .".xml";
		if ($dom->save($filename)) echo "File has been saved in: " . $filename . "<br>";
		 else echo "ERROR, XML file not saved in: " .$filename. "!!";
		echo "If you are using Firefox to display xml file in this browser page, right click and  select \"View page source\" or \"View source\"<br>";
		return new Response($dom->saveXML());
//		return $this->render('MarfiB2BCPETemplateBundle:Default:xmlshow.html.twig', array('xmlfile'=>$dom->saveXML()));
	}
}

?>


