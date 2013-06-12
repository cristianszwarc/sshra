<?php

namespace Modules;

use Net_SSH2;

class MySSH 
{
	//ssh layer, to allow other SSH methods
	private $username;
	private $password;
	private $domain;
	public $ready=false;
	public $app;
	public $ssh;
	
	function __construct($app) 
	{
		$this->app = $app;
		$this->ready = $app['session']->get('sshReady');
	}
	
	public function login($domain, $username, $password) 
	{
		$this->ready=false;
		
		try
		{
			$this->ssh = new Net_SSH2($domain);
			if($username && $password && $this->ssh->login($username, $password))
				$this->ready = true; else $this->ssh=NULL;
		} catch (\Exception $e) {
			//some error
		}
		$this->app['session']->set('sshReady', $this->ready);
		$this->app['session']->set('sshDomain', $domain);
		$this->app['session']->set('sshUser', $username);
		$this->app['session']->set('sshPass', $password);
		
		return $this->ready;
	}
	public function reconect() 
	{
		
		return $this->login($this->app['session']->get('sshDomain'), $this->app['session']->get('sshUser'), $this->app['session']->get('sshPass'));
	}
	
	public function disconnect() 
	{
		$this->app['session']->set('sshReady', NULL);
		$this->app['session']->set('sshDomain', NULL);
		$this->app['session']->set('sshUser', NULL);
		$this->app['session']->set('sshPass', NULL);
	}
	
	public function exec($command)
	{
		return $this->ssh->exec($command);
	}

}

