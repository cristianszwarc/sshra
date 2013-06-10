<?php
namespace Modules;

use Silex\Application;
use Silex\ControllerProviderInterface;

class Dashboard implements ControllerProviderInterface
{
        public $name="dashboard";
        public $title="Dashboard";
        public $dashboard="";

        function __construct($app) {
                if(!$app['session']->get('loginRun'.$this->name)){
                    $app['session']->set('loginRun'.$this->name, true);


                } else {

                }


        }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
            return $app['twig']->render('dashboard/index.twig', array());
        })->bind($this->name);

        return $controllers;
    }
}
