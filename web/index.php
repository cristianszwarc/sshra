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

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/../views',
	));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());


$app['ssh']=NULL;

/* if not logged and is not home  */
$app->before(function(Request $request) use ($app){
    if ( !$app['ssh'] && $app['url_generator']->generate('home') != $request->getRequestUri() )
                return $app->redirect($app['url_generator']->generate('home'));
});

/* if is logged connect and load modules */
if($app['session']->get('sshEnabled') ) {
    $app['ssh'] = new Modules\MySSH($app['session']->get('domain'), $app['session']->get('username'), $app['session']->get('password'));
    if($app['ssh']){
	    $modulos=array();

            $modulos['dashboard']=new Modules\Dashboard($app);
            $app->mount('/dashboard', $modulos['dashboard']);

	    $modulos['ram']=new Modules\Ram($app);
            $app->mount('/ram', $modulos['ram']);

	    $modulos['disk']=new Modules\Disk($app);
	    $app->mount('/disk', $modulos['disk']);

    	    $app["twig"]->addGlobal("modulos", $modulos);
    } else {
	$app['session']->set('loginerror', "Error on reconect");
    }
}

/* basic routes */

/* home */
$app->get('/', function() use ($app) {
    /* if logged then go dashboard */
    if($app['session']->get('sshEnabled')){
	return $app->redirect($app['url_generator']->generate('dashboard'));
    }

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
	$domain = $request->get('domain');
	$username = $request->get('username');
	$password = $request->get('password');
	$app['session']->set('username', $username);
	$app['session']->set('password', $password);
	$app['session']->set('domain', $domain);

	$app['session']->set('sshEnabled', false);

	/*check user, pass and ssh connection */
    	if ($username && $password) {
		$app['ssh'] = new Modules\MySSH($domain, $username, $password);
		if ($app['ssh']) {
			$app['session']->set('sshEnabled', true);
			return $app->redirect($app['url_generator']->generate('home'));
		}
	}

	$app['session']->set('loginerror', "connection error");
	return $app->redirect($app['url_generator']->generate('home'));
})->bind('doLogin');

/* logout */
$app->get('/logout', function() use ($app) {
    $app['session']->set('sshEnabled', false);
    $app['session']->set('password', false);
    $app['session']->clear();
    return $app->redirect($app['url_generator']->generate('home'));
})->bind('logout');


$app->run();
