<?php

class LayoutUtility extends AdminComponent{

	private $rows = array();
	private $current_row;

	function initialize(){
		$this->createRow();
	}

	function addColumn(){
		$this->current_row[] = func_get_args();
	}

	function createRow(){
		if($this->current_row){
			$this->rows[] = $this->current_row;
		}
		$this->current_row = array();
	}

	function process(){
		
	}

	function getValues(){
		$this->createRow();
		return array('rows' => $this->rows);
	}

	function getSubComponents(){
		$this->createRow();
		$sub = array();
		foreach($this->rows as $row){
			foreach($row as $columns){
				foreach($columns as $column){
					$sub[] = $column;
				}				
			}
		}
		
		return $sub;		
	}

}
