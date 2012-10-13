<?php

class ViewerUtility extends AdminComponent{

	protected $rows = array();

	function initialize(){
		$this->helper->filter->defaults($this->args, array(
			'block' => '',
			'title' => $this->node->getTitle(),
			'operations' => array(),
			'messages' => array(),
			'filter' => array(),
			'custom_scripts' => array(),
			'custom_styles' => array()
		));
	}

	function addSection($dataset, $title =  '', $new_column = false, $attributes = array()){
		if(!$new_column || !$this->rows){
			$this->rows[] = array(array('dataset' => $dataset, 'title' => $title, 'attributes' => $attributes));
		}
		else{
			$this->rows[count($this->rows)-1][] = array('dataset' => $dataset, 'title' => $title, 'attributes' => $attributes);
		}		
	}

	function createDataset($data, $attributes = array()){

		$this->helper->filter->defaults($attributes, array(
			'header' => array(),
			'orientation'  => 'registry',
			'list'		=> false,
			'attributes' => array(),
			'input_single' => true,
			'input_orientation' => 'horizontal',
			'class' => '',
			'disabled' => array(),
			'table' => array(),
			'enabled' => array(),
			'disable_keys' => true,
			'disable_primary' => false,
			'date_format' => 'd/m/Y G:i',
			'replace_boolean' => true,
			'links' => array()
		));

		if(is_array($data)){
			if($attributes['input_single'] && !$attributes['list']){
				if($attributes['input_orientation'] == 'vertical'){
					foreach($data as $key => &$value){
						$value = array($value);
					}
				}
				else{
					$data = array($data);
				}
			}

			if(!$attributes['list']){
				if($attributes['orientation'] == 'vertical' && $attributes['input_orientation'] == 'horizontal'){
					$vertical = array();
					foreach($data as $item){
						foreach($item as $key => $value){
							if(!isset($multi[$key])) {
								$vertical[$key] = array();
							}
							$vertical[$key][] = $value;
						}
					}
					$data = $vertical;
				}
				elseif(($attributes['orientation'] == 'horizontal' || $attributes['orientation'] == 'registry')
						&& $attributes['input_orientation'] == 'vertical'){
					$horizontal = array();
					foreach($data as $key => $values){
						$row = array();
						foreach($values as $index => $value){
							if(!isset($horizontal[$index])){
								$horizontal[$index] = array();
							}
							$horizontal[$index][$key] = $value;
						}
					}
					$data = $horizontal;
				}
			}
			
		}

		if(is_object($data)){
			if($attributes['orientation'] == 'vertical'){
				$data = $data->fetchTransposedRows();
			}
			else{
				$data = $data->fetchAllRows();
			}
		}

		$dates = array();
		$booleans = array();
		if($attributes['table']){

			if(is_string($attributes['table'])){
				$attributes['table'] = array($attributes['table']);
			}

			foreach($attributes['table'] as $table_name){

				$table = $this->schema->getTable($table_name);

				if($attributes['disable_keys']){
					$keys = $table->getColumnsByType('key');
					foreach($keys as $column){
						if(!in_array($column->name, $attributes['enabled'])
								&& ($attributes['disable_primary'] || !$column->is_primary)){
							$attributes['disabled'][] = $column->name;
						}
					}
				}

				if($attributes['date_format']){
					$keys = $table->getColumnsByType(array('timestamp', 'date', 'datetime'));
					foreach($keys as $column){
						$dates[$column->name] = true;
					}
				}

				if($attributes['replace_boolean']){
					$bools = $table->getColumnsByType(array('bool', 'boolean'));
					foreach($bools as $column){
						$booleans[$column->name] = true;
					}
				}

				$columns = $table->getAllColumns();

				foreach($columns as $column){
					if(!isset($attributes['header'][$column->name]) && $column->label){
						$attributes['header'][$column->name] = $column->label;
					}
				}
				
			}


		}

		if($attributes['disabled'] || $dates || $booleans || $attributes['links']){
			
			if($attributes['orientation'] == 'vertical'){
				$filtered = array();
				foreach($data as $header => $column){
					if(!in_array($header, $attributes['disabled'])){
						$filtered[$header] = $column;
						if(isset($dates[$header])){
							foreach($filtered[$header] as &$val){
								$val = date($attributes['date_format'], strtotime($val));
							}
						}
						if(isset($booleans[$header])){
							foreach($filtered[$header] as &$val){
								$val = $this->lang->viewer['boolean'][$val];
							}
						}
					}
				}
				$data = $filtered;
			}
			else{
				$filtered = array();
				foreach($data as $row){
					$copied = array();
					foreach($row as $header => $column){
						if(!in_array($header, $attributes['disabled'])){
							$copied[$header] = $column;
							if(isset($dates[$header])){
								$copied[$header] = date($attributes['date_format'], strtotime($copied[$header]));
							}
							if(isset($booleans[$header])){
								$copied[$header] = $this->lang->viewer['boolean'][$copied[$header]];
							}
						}
					}
					$filtered[] = $copied;
				}
				$data = $filtered;
			}
		}
		
		foreach($data as &$item){
			if(is_array($item)){
				foreach($item as &$subitem){
					if(is_array($subitem)){
						if(!isset($subitem[0])){
							$subitem = $this->createDataset($subitem);
						}
						else{
							$subitem = $this->createDataset($subitem, array('orientation' => 'horizontal', 'input_single' => false));
						}
					}
				}
			}
		}

		$dataset = new stdClass();
		$dataset->data = $data;
		$dataset->header = (array)$attributes['header'];
		$dataset->orientation = $attributes['orientation'];
		$dataset->list = $attributes['list'];
		$dataset->css_class = $attributes['class'];
		$dataset->disabled = $attributes['disabled'];
		$dataset->links = $attributes['links'];
		
		return $dataset;

	}

	function getValues(){

		if(isset($this->args['operations']['edit'])){
			$operation = $this->args['operations']['edit'];
			if(!isset($operation[2])){
				$params = $this->node->getArguments();
			}
			else{
				$params = $operation[2];
			}
			$title = isset($operation[1]) ? $operation[1] : 'Edit';
			$this->args['operations']['edit']['url'] =  $this->admin->deriveUrl($operation[0], $title, $params, $this->name);
		}

		return array(
			'title' => $this->args['title'],
			'block' => $this->args['block'],
			'filter' => $this->getFilter(),
			'rows'	=> $this->rows,
			'messages' => $this->args['messages'],
			'operations' => $this->args['operations'],
			'custom_scripts' => $this->args['custom_scripts'],
			'custom_styles' => $this->args['custom_styles']
		);
	}

	function process(){

	}
	
	protected function getFilter(){

		$inputs = array();

		if(!$this->args['filter']){
			return $inputs;
		}

		$filter_list = $this->args['filter'];

		if(!isset($filter_list[0])){
			$filter_list = array($filter_list);
		}

		foreach($filter_list as $index => $filter){

			if(!isset($filter['id'])){
				trigger_error("Attribute 'id' is required for filter", E_USER_WARNING);
				continue;
			}

			if(!isset($filter['type'])){
				$filter['type'] = 'dropdown';
			}

			$node = clone $this->node;	

			$filter_value = $this->node->arg($filter['id']);
			
			if($filter['type'] == 'dropdown'){

				if(!isset($filter['dataset'])){
					trigger_error("Attribute 'dataset' is required for filter of type 'dropdown'", E_USER_WARNING);
					continue;
				}

				if($filter_value !== false){
					$selected_value = $this->admin->url->getUrl($node);
					$node->removeArgument($filter['id']);
					$empty_value = $this->admin->url->getUrl($node, true);
				}
				else{
					//Current Url
					$empty_value = $selected_value = $this->admin->url->getUrl($this->node);
				}

				$input = $this->load->form()->add('dropdown', 'filter['.$index.']', $selected_value);
				$name = isset($filter['name']) ?'- '. $filter['name'] . ' -': $this->lang->select;
				$input->add($empty_value, $name);

				$dataset = $filter['dataset'];
				if(!is_array($dataset)){
					$dataset = $dataset->fetchKeyValue();
				}

				$source = array();
				foreach($dataset as $id => $value){
					$obj[$filter['id']] = $id;
					if($id != $filter_value){
						$key = $this->admin->url->getUrl($node);
					}
					else{
						$key = $selected_value;
					}
					$source[$key] = $value;
				}

				$input->setDataSource($source);
				
			}
			elseif($filter['type'] == 'text'){
				$initial_value = $filter_value ? $filter_value : '';
				$input = $this->load->form()->add('text', 'filter['.$index.']', $initial_value);
				$input->setAttribute('data-default', isset($filter['name'])? $filter['name'] : 'Buscar');
				$input->setAttribute('data-url', $this->admin->deriveOperationUrl($this->name, 'filter'));
			}
			else{
				trigger_error("Unsupported filter input type '{$filter['type']}'", E_USER_WARNING);
				continue;
			}

			$input->setAttribute('class', 'entity-filter');
			$inputs[] = $input;

		}

		return $inputs;

	}


}