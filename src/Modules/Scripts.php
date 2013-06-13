<?php

namespace Modules;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Scripts implements ControllerProviderInterface
{
	protected $app;
	public $name="scripts";		//name for the bind
	public $title="Scripts";	//title for menu
	public $active;				//menu highlighting
	public $dashboard;			//content to attach to the dashboard
	private $conf;				//module configuration
	
	public $commands;		//custom var
	
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
        $controllers = $app['controllers_factory'];
        $controllers->get('/', array($this, 'index'))->bind($this->name);
        $controllers->get('/run/{command}', array($this, 'run'))->bind($this->name.'.run');
        $controllers->post('/run/{command}', array($this, 'run'))->bind($this->name.'.doRun');
        return $controllers;
    }
	
	private function loadCommandList(){
		
		$grouped=array();
		foreach ($this->conf as $title => $command) 
		{
			if(strpos($command, '*'))
			{
				$output = $this->app['ssh']->exec('ls '.$command);
				$output=explode("\n", $output);
				foreach ($output as $line) 
				{
					$line=trim($line);
					if($line && !strpos($line, 'cannot access'))
					{
						$md5=md5($line);
						$grouped[$title][basename($line)]=$md5;
						$this->commands[$md5]=$line;
					}
				}
				
			} else {
				$md5=md5($command);
				$grouped['General'][$title]=$md5;
				$this->commands[$md5]=$command;
			}
		}
		
		return $grouped;
	
	}
	
	
	public function run(Application $app, Request $request)
	{
		$this->active = 'active';
		$this->loadCommandList();
		$command = $request->get('command');
		$commandDetail = $this->commands[$command];
		$parameter = $request->get('parameter');
		
		if($parameter!==NULL) $result = $this->app['ssh']->exec("$commandDetail $parameter"); else $result=null;
				
		return $this->app['twig']->render('scripts/run.twig', array('result'=>$result, 'last_parameter'=>$parameter, 'command'=>$command, 'commandDetail'=>$commandDetail));
	}
	public function index()
	{
		$this->active = 'active';
		return $this->app['twig']->render('scripts/index.twig', array('grouped'=>$this->loadCommandList()));
	}

}
