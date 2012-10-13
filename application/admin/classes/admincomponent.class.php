<?php

abstract class AdminComponent extends Utility{
	
	/**
	 * A reference to the Admin instance.
	 * @var Admin 
	 */
	protected $admin;
	protected $schema, $node, $name, $args, $valid;

	final function setup($name, array $args, Admin $admin){
		$this->admin = $admin;
		$this->node = $admin->node;
		$this->schema = $admin->schema;
		$this->name = $name;
		$this->args = $args;
		$this->valid = $this->load->validator();
	}

	final function getHtml(){

		$template = '/'.$this->context->getModule().'/'.strtolower(substr(get_class($this), 0, -7));

		$values = $this->getValues();
		$values['_component'] = $this->name;

		return (string)$this->load->block('/admin/component',
				array('template' => $template, 'values' =>  $values)
		);

	}

	final function getName(){
		return $this->name;
	}

	abstract function initialize();
	abstract function getValues();

	function getSubComponents(){
		return array();
	}

}
