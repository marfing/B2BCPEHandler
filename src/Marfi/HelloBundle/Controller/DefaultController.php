<?php


/** caricato in localhost/symfony_test1/web/app_dev.php/hello/name */

namespace Marfi\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
		
		/**symfony approach*/
		$request = Request::createFromGlobals();
		
		// the URI being requested (e.g. /about) minus any query parameters
		echo "<h3>HTTP Request Info</h3>";
		echo "<p>Method: " .$request->getMethod() ."</p>";
		echo "pathInfo: " .$request->getPathInfo();
		echo "<p>from: " .$request->headers->get('host') ."</p>";
		echo "<p>User-Agent: " .$request->headers->get('user_agent') ."</p>";
		
		//echo "<p>Request attributes: " .$request->attributes->all() ."</p>";
		
		//echo "<p>All PHP code inserted here in DefaultController.php indexAction method is executed before index.html.twig template</p>";	
		//echo "The URI requested is: " .$uri;
		
		$response = new Response();
		$response->setContent('<html><body><h1>Hello world!</h1></body></html>');
		$response->setStatusCode(200);
		$response->headers->set('Content-Type', 'text/html');
		
		// prints the HTTP headers followed by the content
		$response->send();
		
		return array('name' => $name);
    }
}
