<?php

namespace Modules;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Dashboard implements ControllerProviderInterface
{
	protected $app;
	public $name="dashboard";		//name for the bind
	public $title="Dashboard";		//title for menu
	public $active;					//menu highlighting
	public $dashboard;				//content to attach to the dashboard
	private $conf;					//module configuration
		
	function __construct($app, $conf=NULL) {
		$this->app = $app;
		$this->conf=$conf;
		if(!$this->app['session']->get('loginRun'.$this->name))
		{
		    $this->app['session']->set('loginRun'.$this->name, true);
		
			/* commands to run only on first login */
			
		} else {
			
			/* commands to run on each reconnect */
			
		}
		
		/* run in every request */
		
	}

    public function connect(Application $app)
    {
       	$controllers = $this->app['controllers_factory'];
        $controllers->get('/', array($this, 'index'))->bind($this->name);
        return $controllers;
    }
	
	
	public function index()
	{
		$this->active = 'active';
		return $this->app['twig']->render('dashboard/index.twig', array());
	}

}