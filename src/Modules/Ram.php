<?php

namespace Modules;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Ram implements ControllerProviderInterface
{
	protected $app;
	public $name="ram";		//name for the bind
	public $title="Ram";	//title for menu
	public $active;			//menu highlighting
	public $dashboard;		//content to attach to the dashboard
	private $conf;			//module configuration
	
	public $totalRam;		//custom var
	
	function __construct($app, $conf=NULL) {
		$this->app = $app;
		$this->conf=$conf;
		if(!$this->app['session']->get('loginRun'.$this->name))
		{
		    $this->app['session']->set('loginRun'.$this->name, true);
		
			/* commands to run only on first login */
			$this->totalRam=$app['ssh']->exec("awk '/MemTotal/ { print $2 }' /proc/meminfo");
			
			$this->totalRam=number_format(($this->totalRam/1024), 2). "mb";
			$app['session']->set('totalRam', $this->totalRam);
			
		} else {
			
			/* commands to run on each reconnect */
			$this->totalRam=$app['session']->get('totalRam');
		}
		
		/* run in every request */
		$this->dashboard = 'Total ram: '.$this->totalRam;
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
		$result = $this->app['ssh']->exec('free -m');
		return $this->app['twig']->render('ram/index.twig', array('result'=>$result));
	}

}