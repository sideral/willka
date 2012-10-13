<?php

class AdminTableColumn{

	private $name, $type, $key = null, $is_primary = false, $label = '', $target_dirs = array();
	
	function __construct($name, $type){
		$this->name = $name;
		$this->type = $type;
		$this->is_primary = $type == 'primary';
	}
	
	function getName(){
		return $this->name;
	}
	
	function getType(){
		return $this->type;
	}
	
	function setLabel($label){
		$this->label = (string)$label;
	}
	
	function getLabel(){
		return $this->label;
	}
	
	function references($target_table, $target_column, $on_delete = 0){
		$this->key = array($target_table, $target_column, $on_delete);
	}
	
	function getForeignKey(){
		return $this->key;
	}
	
	function setTargetDirs($base_dir, $dirs = array()){
		
		$this->target_dirs[] = $base_dir;
		
		foreach($dirs as $dir){
			$this->target_dirs[] = $base_dir.DS.$dir;
		}
		
	}
	
	function getTargetDirs(){
		return $this->target_dirs;		
	}
	
}
