<?php

require_once('adminforminput.class.php');

class AdminFormTable{
	
	private $_prefix = '';
	private $_table = null;
	private $_inputs = array();
	private $_filter = null;
	private $_values = array();
	private $_autofill = false;
	private $_on_insert = null;
	
	function __construct($table, $prefix){
		$this->_table = $table;
		$this->_prefix = $prefix;
	}
	
	function getPrefix(){
		return $this->_prefix;
	}
	
	function getTable(){
		return $this->_table;
	}
	
	function setFilter($callback){
		$this->_filter = $callback;
	}
	
	function getFilter(){
		return $this->_filter;
	}
	
	function onInsert($callback){
		$this->_on_insert = $callback;
	}
	
	function fireOnInsert($values, $primary_vals, $is_new){
		if($this->_on_insert){
			return call_user_func($this->_on_insert, $values, $primary_vals, $is_new);			
		}
	}
	
	function getAdditionalValues(){
		return $this->_values;
	}
	
	function setAdditionalValues(array $values){
		$this->_values = $values;
	}
	
	function __get($name){
		return $this->_inputs[$name];
	}
	
	function __set($name, $input){
		$this->_inputs[$name] = new AdminFormInput($input, $name);
	}
	
	function getAllReferences(){
		$refs = array();
		foreach($this->_inputs as $name => $input){
			$ref = $input->getReference();
			if($ref) $refs[$name] = $ref;
		}
		return $refs;
	}
	
	function getAllDeletableReferences(){
		$refs = array();
		foreach($this->_inputs as $name => $input){
			$ref = $input->getDeletableReference();
			if($ref) $refs[$name] = $ref;
		}
		return $refs;
	}
	
	function getMultiRowInput(){
		
		foreach($this->_inputs as $input){
			if($input->isMultiRow()){
				return $input;
			}
		}
		
		return null;
	}
	
	function getMultiColumnInputs(){
		$inputs = array();
		foreach($this->_inputs as $name => $input){
			if($input->isMultiColumn()){
				$inputs[$name] = $input;
			}
		}
		return $inputs;
	}
	
	function setAutoFill($bool){
		$this->_autofill = $bool;
	}

	function getAutoFill(){
		return $this->_autofill;
	}
	
	function setValidator(array $inputs, $validator, $messages = array()){
		foreach($this->_inputs as $name => $input){
			if(in_array($name, $inputs ,true) || in_array($input, $inputs, true)){
				$input->setValidator($validator, $messages);
			}
		}
	}
	
}