<?php
/*
This is a basic and modular external management tool for common linux tasks.
If a connection is made the modules are loaded, on each page run the connection is made again.
Each module can run commands using the shared SSH connection.

for your security run this tool locallyy or t least use a SSL connection.
*/

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application\UrlGeneratorTrait;

error_reporting(-1);
ErrorHandler::register();
ExceptionHandler::register();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../views'));
$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__."/../config/config.yml"));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

 
$app['ssh'] = new Modules\MySSH($app);


/* if not logged and is not home  */
$app->before(function(Request $request) use ($app)
{
    if ( !$app['ssh']->ready && $app['url_generator']->generate('home') != $request->getRequestUri() )
                return $app->redirect($app['url_generator']->generate('home'));
});

/* if is logged connect and load modules */
if( $app['ssh']->ready  ) 
{
    if($app['ssh']->reconect())
    {
	    $modulos=array();
		$modulos['dashboard']=new Modules\Dashboard($app);
        $app->mount('/dashboard', $modulos['dashboard']);

		foreach ($app['config'] as $moduleName => $moduleConfig )
		{
				$objName="Modules\\$moduleName";
				$modulos[$moduleName]=new $objName($app, $moduleConfig );
				$app->mount('/'.$moduleName, $modulos[$moduleName]);
		}
	    	
    	$app["twig"]->addGlobal("modulos", $modulos);
    } else {
		$app['session']->set('loginerror', "Error on reconect");
    }
}


/* basic routes */

/* home */
$app->get('/', function() use ($app) {
    if($app['ssh']->ready) return $app->redirect($app['url_generator']->generate('dashboard'));

    $error = $app['session']->get('loginerror');
    $app['session']->set('loginerror', "");

    return $app['twig']->render('home.twig', array(
        'error'         => $error,
        'last_username' => $app['session']->get('username'),
		'last_domain' => $app['session']->get('domain'),
    ));
    
})->bind('home');


/* login attempt */
$app->post('/', function(Request $request) use ($app) {

	$app['session']->set('domain', $request->get('domain'));
	$app['session']->set('username', $request->get('username'));
	
	/*check user, pass and ssh connection */
	if ($app['ssh']->login($request->get('domain'), $request->get('username'), $request->get('password'))) 
		return $app->redirect($app['url_generator']->generate('home'));
	
	$app['session']->set('loginerror', "connection error");
	return $app->redirect($app['url_generator']->generate('home'));

})->bind('doLogin');

$app->get('/logout', function() use ($app) 
{
    $app['ssh']->disconnect();
    $app['session']->clear();
    return $app->redirect($app['url_generator']->generate('home'));
    
})->bind('logout');




$app->run();
