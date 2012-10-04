<?php
//example of form usage with arrays

namespace Marfi\TaskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Marfi\TaskBundle\Entity\Task;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
	var $registrationForm;
		
	public function formDataProcessAction(Request $request )
	{
		
	
		// il controller viene richiamato anche dopo l'inserimento dei valori nel form (POST), in questo caso possiamo gestire i dati inseriti con la funzione getData
		$this->registrationForm->bindRequest($request);
		if($this->registrationForm->isValid()){
			// data is an array with "name", "email", and "message" keys
			$data = $this->registrationForm->getData();
			echo "<h2>Form Data</h2>";
			//foreach( $data as $key = &gt; $value )
				//echo "<p> Index : ".$key. "-  Value: ". $value. "</p>";
			foreach( $data as $key=>$value )
				echo "<p>" .$key." - Value: ". $value. "</p>";		
		}
	}
	
	
	public function newAction(Request $request)
    {
        // create a task and give it some dummy data for this example
		//$task = new Task();
		//$task->setTask('Write a blog post');
		//$task->setDueDate(new \DateTime('tomorrow'));
		/*$form = $this->createFormBuilder($task)
										->add('task', 'text')
										//->add('dueDate', 'date')
										->getForm();*/
		$defaultData = array('registerForm' => 'Insert your registration data here');
		$this->registrationForm = $this->createFormBuilder($defaultData)
			->add('name ', 'text')
			->add('email ', 'email')
			->add('password ', 'password')
			->getForm();

		if ($request->getMethod() == 'POST') {
			$this->formDataProcessAction($request);
			return new Response('<p>Thanks for your collaboration</p>');
		}
		else
			return $this->render('MarfiTaskBundle:Default:index.html.twig', array(
						'form' => $this->registrationForm->createView(),));
	}
}
		

?>