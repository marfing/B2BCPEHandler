<?php
//example of form usage with arrays

namespace Marfi\TaskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\TaskBundle\Entity\RegUserData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
	public function newAction(Request $request)
    {
		// create a task and give it some dummy data for this example - funziona
		$task = new RegUserData();

		$form = $this->createFormBuilder($task)	->	add('email','email')->add('password','password')	->getForm();

		if ($request->getMethod() == 'POST') 
		{
				$form->bindRequest($request); 
				if($form->isValid())
				{
					$data = $form->getData();
					echo "<h2>Form Data</h2>";
					$data->printValues();
					return new Response('<p>Thanks for your collaboration</p>');
				}
		}
		
		return $this->render('MarfiTaskBundle:Default:index.html.twig', array(
            'form' => $form->createView(),));
	}
}

?>
