<?php

require_once('adminhistory.class.php');

class AdminUrlBuilder{
	
	private $history;
	
	function __construct(){
		$this->history = AdminHistory::getInstance();
	}
	
	function getNode(array $args){
		
		$node = false;
		
		if(isset($args[0])){
			$node = $this->history->getNode($args[0]);
		}
		elseif(isset($args['node'])){
			
			$node_string = @unserialize($args['node']);
			
			if(!is_array($node_string)){
				trigger_error('Node could not be unserialized.', E_USER_ERROR);
			}
			
			$valid = isset($node_string['path']) && isset($node_string['parent']) && isset($node_string['title'])&& isset($node_string['component']) && isset($node_string['args']);
			
			if(!$valid){
				trigger_error('Node is not valid.', E_USER_ERROR);
			}
			
			$node = new AdminNode($node_string['path'], $node_string['title'], $node_string['parent'], $node_string['args'], $node_string['component']);
			$this->history->addNode($node);
			
		}
		
		return $node;
	}

	function getUrl(AdminNode $node, $force_id = false){
		if($force_id){
			$node_id = $this->history->addNode($node);
		}
		else{
			$node_id = $node->getId();
		}

		if($this->history->nodeExists($node_id)){
			return "node/$node_id";
		}
		return 'node_process?node='.urlencode(serialize($node->toArray()));
	}
	
	function getOperationUrl(AdminNode $node, $component, $operation, $args = array()){
		$args_string = http_build_query($args);
		return "node/{$node->getId()}?component=".urlencode($component). '&operation='.urlencode($operation).'&'.$args_string;
	}

	function getRootChildUrl($path, $title, $args = array(), $component = '', $force_id = false){
		$root_node = $this->history->getRootNode();
		return $this->getChildUrl($root_node, $path, $title, $args, $component, $force_id);
	}
	
	function getChildUrl(AdminNode $node, $path, $title, $args = array(), $component = '', $force_id = false){
		$new_node = $node->createChildNode($path, $title, $args, $component);
		return $this->getUrl($new_node, $force_id);
	}
	
	function getParentUrl(AdminNode $node, $force_id = false){
		$parent_node = $node->getParentNode();
		return $this->getUrl($parent_node, $force_id);
	}
	
}