<?php

class AdminNode{
	
	private $node, $id;
	
	function __construct($path, $title, $parent_node_id, array $args = array(), $component = ''){
		
		$path_parts = explode('/', $path);

		if($path_parts[0] == ''){
			array_shift($path_parts);
		}
		
		if(count($path_parts) != 2){
			trigger_error('Node data is invalid. A correct path is required.', E_USER_ERROR);
		}
	
		$this->node = array(
			'path' => implode('/', $path_parts),
			'parent' => $parent_node_id,
			'title' => $title,
			'component' => $component,
			'args' => $args			
		);
		
	}
	
	function getId(){
		if(!$this->id){
			$this->id = md5(serialize($this->node));		
		}
		return $this->id;
	}
	
	function toArray(){
		return $this->node;
	}
	
	function getArguments(){
		return $this->node['args'];		
	}
	
	function getParent(){
		return $this->node['parent'];
	}
	
	function getPath($explode = false){
		return $explode? explode('/', $this->node['path']) : $this->node['path'];
	}
	
	function setPath($path){
		$path_parts = explode('/', $path);

		if($path_parts[0] == ''){
			array_shift($path_parts);
		}
		
		if(count($path_parts) != 2){
			trigger_error('Node data is invalid. A correct path is required.', E_USER_ERROR);
		}
		
		$this->id = null;
		$this->node['path'] = implode('/', $path_parts);
	}
	
	function getModule(){
		$path = $this->getPath(true);
		return $path[0];
	}

	function getAction(){
		$path = $this->getPath(true);
		return $path[1];
	}
	
	function getTitle(){
		return $this->node['title'];
	}
	
	function setTitle($title){
		$this->id = null;
		$this->node['title'] = $title;
	}
	
	function getComponent(){
		return $this->node['component'];
	}
	
	function arg($name){
		return isset($this->node['args'][$name])? $this->node['args'][$name] : false;
	}
	
	function removeArgument($name){
		$this->id = null;
		unset($this->node['args'][$name]);
	}
	
	function setArgument($name, $value){
		$this->id = null;
		$this->node['args'][$name] = $value;
	}
	
	function createChildNode($path, $title, $args = array(), $component = ''){
		return new AdminNode($path, $title, $this->getId(), $args, $component);
	}
	
	function getParentNode(){
		$history = AdminHistory::getInstance();
		return $history->getNode($this->node['parent']);		
	} 
	
}