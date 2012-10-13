<?php

abstract class AdminUtility extends Utility{

	protected $schema, $node, $admin, $url, $history,  $components, $valid;

	final function initialize($admin, $components){
		$this->node = $admin->node;
		$this->history = AdminHistory::getInstance();
		$this->admin = $admin;
		$this->components = $components;
		$this->valid = $this->load->validator();
	}

	final function execute(){

		$action_ptr = array(&$this, $this->node->getAction());

		if(is_callable($action_ptr)){
			call_user_func($action_ptr);
		}
		else{
			trigger_error("Could not execute action '{$this->node->getAction()}'", E_USER_WARNING);
		}
		
	}
}
