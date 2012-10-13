<?php

require_once('list.utility.php');

class TableUtility extends ListUtility{

	protected $row_id = 0;

	function initialize(){

		$this->helper->filter->defaults($this->args,
			array(
				'title' => $this->node->getTitle(),
				'operations' => array(),
				'extra_operations' => array(),
				'dataset' => null,
				'row_operation' => 'edit',
				'table' => '',
				'icon_base' => '',
				'filter' => array(),
				'pagination' => array(),
				'messages' => array(),
				'params' => array(),
				'columns' => array(),
				'title_row' => '',
				'column_sort' => array(),
  			    'custom_scripts' => array(),
				'custom_styles' => array(),
				'column_indexes' => array()
			)
		);

		$this->operations = $this->args['operations'];
		$this->extra_operations = $this->args['extra_operations'];

	}
	
	protected function getDisplayRows(){

		if(is_null($this->args['dataset'])){
			return array();
		}

		if(!is_array($this->args['dataset'])){
			$this->paginateDataset();
			$this->sortDataset();
			$rows = $this->args['dataset']->fetchAllRows();
			$this->putFoundRows();
		}
		else{
			$rows = $this->args['dataset'];
		}

		$this->args['params'] = array_merge($this->node->getArguments(), $this->args['params']);

		if(!$this->args['columns']){
			trigger_error('Required argument "columns" not specified.', E_USER_ERROR);
			return array();
		}

		if(!$this->args['title_row']){
			$column_names = array_keys($this->args['columns']);
			$this->args['title_row'] = $column_names[0];
		}

		$primary = $this->schema->getTable($this->args['table'])->getPrimary();
		$this->row_id = $row_id = $primary[0];

		return $this->setupOperations($rows, $row_id, $this->args['title_row']);

	}


	function getValues(){
		$values = parent::getValues();
		$values['columns'] = $this->args['columns'];
		$values['title_row'] = $this->args['title_row'];
		$values['id_row'] = $this->row_id;
		$values['header_links'] = $this->getHeaderLinks();
		$values['current_column_sort'] = $this->getCurrentColumnSort();
		return $values;
	}
	
	protected function getHeaderLinks(){
		
		if(!$this->args['column_sort']){
			return false;
		}

		$links = array();
		
		$node = clone $this->node;
		$dir = $node->arg($this->name.'/sort_direction') ? $node->arg($this->name.'/sort_direction'): $this->args['column_sort']['default'][0];
		$column = $node->arg($this->name.'/sort_column') ? $node->arg($this->name.'/sort_column'): $this->args['column_sort']['default'][1];
		
		$node->removeArgument($this->name.'/pagination_page');
		
		foreach($this->args['columns'] as $name => $label){
			
			if($column == $name)
				$node->setArgument($this->name.'/sort_direction', $dir == 'asc' ? 'desc':'asc');
			else
				$node->setArgument($this->name.'/sort_direction', 'asc');
			
			$node->setArgument($this->name.'/sort_column', $name);

			$links[$name] = '/admin/'.$this->admin->url->getUrl($node);
			
		}
		
		return $links;
		 		
	}
	
	protected function sortDataset(){
		
		if($this->args['column_sort']){
			
			$column = $this->node->arg($this->name.'/sort_column');
			$dir = $this->node->arg($this->name.'/sort_direction');
			
			if(!isset($this->args['column_sort']['default'])){
				return;
			}
			
			if(!$column || !$dir){
				$column = $this->args['column_sort']['default'][0];
				$dir = $this->args['column_sort']['default'][1];
			}
			
			$table = $this->args['table'];
			if(isset($this->args['column_sort']['options'][$column])){
				$table = $this->args['column_sort']['options'][$column][0];
				$column = $this->args['column_sort']['options'][$column][1];					
			}
						
			$this->args['dataset']->from($table)->orderby($column, $dir);
			
		}
		
	}
	
	protected function getCurrentColumnSort(){
		
		if(!$this->args['column_sort']){
			return array();
		}
		
		$node = clone $this->node;
		$dir = $node->arg($this->name.'/sort_direction') ? $node->arg($this->name.'/sort_direction'): $this->args['column_sort']['default'][0];
		$column = $node->arg($this->name.'/sort_column') ? $node->arg($this->name.'/sort_column'): $this->args['column_sort']['default'][1];
		
		return array($column, $dir);	
		
	}

}