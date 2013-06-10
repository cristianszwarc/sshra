<?php

namespace Modules;

use Silex\Application;
use Silex\ControllerProviderInterface;

class Disk implements ControllerProviderInterface
{
        public $name="disk";
        public $title="Disk";
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
            return "disco";
        })->bind($this->name);

        return $controllers;
    }
}
