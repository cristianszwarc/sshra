<?php

namespace Modules;

use Net_SSH2;

class MySSH {
        //ssh layer, to allow other SSH methods
        private $username;
        private $password;
        private $domain;
        public $ssh;

        function __construct($domain, $username, $password) {
                $this->ssh = new Net_SSH2($domain);
                if($this->ssh->login($username, $password)) return $this; else return NULL;
        }

        public function exec($command){
                return $this->ssh->exec($command);
        }

}

