<?php

require_once('adminnode.class.php');

class AdminHistory{

	private $session, $nodes;
	
	private static $instance = null;

	/**
	 *
	 * @return AdminHistory 
	 */
	static function getInstance(){
		if(!self::$instance){
			self::$instance = new self(); 
		}		
		return self::$instance;
	}
	
	private function __construct(){

		$this->session = new Session('admin');

		if(!$this->session->exists('history')){
			$this->session->set('history', array());
		}

		$this->nodes = $this->session->get('history');

	}
	
	function nodeExists($node_id){
		return isset($this->nodes[$node_id]);
	}
	
	function addNode(AdminNode $node){
		
		$node_id = $node->getId();
		
		if(isset($this->nodes[$node_id])){
			return $node_id;
		}

		$this->nodes[$node_id] = $node->toArray();
		$this->session->set('history', $this->nodes);

		return $node_id;
		
	}
	
	function getNode($node_id){
		if(isset($this->nodes[$node_id])){
			$node = $this->nodes[$node_id];
			return new AdminNode($node['path'], $node['title'], $node['parent'], $node['args'], $node['component']);
		}
		return false;
	}

	function getBranch($node_id){
		$list = array();
		do{
			$node = $this->getNode($node_id);
			$list[$node_id] = $node;
		}
		while($node_id = $node->getParent());
		return array_reverse($list);
	}
	
	function getRootNode(){
		foreach($this->nodes as $node){
			if($node['parent'] == ''){
				return new AdminNode($node['path'], $node['title'], $node['parent'], $node['args'], $node['component']);
			}
		}
		return false;
	}
	
	function getParentNode(AdminNode $node){
		$parent = $node->getParent();
		return $this->getNode($parent);
	}
	
	function getNodeByDepth($node_id, $depth){
		
		$list = $this->getBranch($node_id);
		
		if($depth > count($list)-1){
			return false;
		}
		
		$i=0;	
		foreach($list as $id => $node){
			if($depth == $i){
				break;
			}
			$i++;
		}
		
		return $node;
		
	}

	function isProcess(){
		return $this->getParam('next') !== false;
	}

}
