<?php

namespace Modules;

use Silex\Application;
use Silex\ControllerProviderInterface;

class Ram implements ControllerProviderInterface
{
	public $name="ram";
	public $title="Ram";
	public $dashboard="";

	public $totalRam;

	function __construct($app) {
       		if(!$app['session']->get('loginRun'.$this->name)){
		    $app['session']->set('loginRun'.$this->name, true);

		    $this->totalRam=$app['ssh']->exec("awk '/MemTotal/ { print $2 }' /proc/meminfo");

		    $this->totalRam=number_format ($this->totalRam /1048576, 2) . "mb";
		    $app['session']->set('totalRam', $this->totalRam);
		} else {
		    $this->totalRam=$app['session']->get('totalRam');
		}
		$this->dashboard = 'Total ram: '.$this->totalRam;

        }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
    		$status= $app['ssh']->exec('free -m');
    		return $app['twig']->render('ram/index.twig', array('status'=>$status));
	})->bind($this->name);

        return $controllers;
    }
}

