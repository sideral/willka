<?php

require_once('adminmenuentry.class.php');

class AdminMenu{
		
	private $entries = array();
	private $admin;
	
	function __construct(Admin $admin){
		$this->admin = $admin;
	}
	
	function addEntry(AdminMenuEntry $entry, $subentries = array(), $offset = -1){
		
		if($offset == -1){
			$offset = count($this->entries);
		}

		$history = AdminHistory::getInstance();
		$current_node = $this->admin->node;
		$node = $history->getNodeByDepth($current_node->getId(), 1);
		
		$selected = false;
		
		$arr_sub = array();
				
		foreach($subentries as $subentry){
			$sub = $subentry->toArray();
			$sub[2] = $node && $subentry->getPath() == $node->getPath() && $subentry->getArguments() == $node->getArguments();
			$selected = $selected || $sub[2];
			$arr_sub[] = $sub;
		}
		
		$entry_arr = $entry->toArray();
		$entry_arr[2] = false;
		
		if($subentries){
			$entry_arr[2] = $selected;
		}
		else if($node){
			$entry_arr[2] = $entry->getPath() == $node->getPath();
		}
		
		
		$insert = array(
			'entry' => $entry_arr,
			'subentries' => $arr_sub
		);
		
		if(isset($this->entries[$offset])){
			array_splice($this->entries, $offset, 0, array($insert));		
		}
		else{
			$this->entries[$offset] = $insert;
		}
		
		ksort($this->entries);
		
	}

	function getEntries(){
		return $this->entries ;
	}

}

