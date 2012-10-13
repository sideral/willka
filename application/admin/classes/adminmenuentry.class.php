<?php

class AdminMenuEntry{
	
	private $text, $path, $params, $title;

	function __construct($text, $path = '', $params = array(), $title = ''){
		$this->text = $text;
		$this->path = $path;
		$this->params = $params;
		$this->title = $title? $title: $text;		
	}
	
	function getLink(){
		if(!$this->path){
			return '';
		}
		else{
			$builder = new AdminUrlBuilder();
			return $builder->getRootChildUrl($this->path, $this->title, $this->params);
		}
	}
	
	function toArray(){
		return array($this->text, $this->getLink());
	}
	
	function getPath(){
		return $this->path;
	}
	
	function getArguments(){
		return $this->params;
	}
	
}