<?php

class ListUtility extends AdminComponent{

	protected $operations, $form, $extra_operations = array();

	function initialize(){
		
		$this->helper->filter->defaults($this->args,array(
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
			'custom_scripts' => array(),
			'custom_styles' => array(),
			'column_indexes' => array(
				'id' => 0,
				'title' => 1,
				'subtitle' => 2,
				'image' => 3
			)
		));

		$this->operations = $this->args['operations'];
		$this->extra_operations = $this->args['extra_operations'];

	}

	function getValues(){

		return array(
			'datasource' => $this->getDisplayRows(),
			'title' => $this->args['title'],
			'operations' => $this->operations,
			'row_operation' => $this->args['row_operation'],
			'extra_operations' => $this->extra_operations,
			'messages' => $this->args['messages'],
			'icon_base' => $this->args['icon_base'],
			'filter' => $this->createFilter(),
			'pagination' => $this->getPagination(),
			'id' => HtmlHelper::generateId(),
			'custom_scripts' => $this->args['custom_scripts'],
			'custom_styles' => $this->args['custom_styles'],
			'column_indexes' => $this->args['column_indexes']
		);

	}
	
	function setDataset($dataset){
		$this->args['dataset'] = $dataset;
	}
	
	function getDataset(){
		return $this->args['dataset'];
	}
	
	function setOperations(array $operations){
		$this->operations = $operations;
	}
	
	function getOperations(){
		return $this->operations;
	}
	
	function setTitle($title){
		$this->args['title'] = $title;
	}
	
	function getTitle(){
		return $this->args['title'];
	}
	
	function addFilter($type, $id, $text = null, $dataset = null, $autofilter = true){
		
		if(!in_array($type, array('dropdown', 'text'))){
			trigger_error('Unsuported filter type',E_USER_ERROR);
		}
		
		$target = null;
		if(is_array($id)){
			$target = $id;
			$id = $id[0].'-'.$id[1];
		}
		
		$this->args['filter'][] = array(
			'type' => $type, 
			'id' => $id,
			'dataset' => $dataset,
			'name' => $text
		);
		
		if($autofilter && $target && $this->node->arg($id) !== false){
			
			if(!is_array($this->args['dataset'])){
				
				if($type == 'dropdown'){
					$this->args['dataset'] = $this->args['dataset']->from($target[0])->where($target[1], $this->node->arg($id));
				}
				else{
					$text = strtr($this->node->arg($id), array('%' => '\%', '_' => '\_'));
					$this->args['dataset'] = $this->args['dataset']->from($target[0])->where($target[1], '%'.$text.'%', 'LIKE');
				}
			}
		}
		
		return $id;
		
	}
	
	function removeFilter($id){
		
		$new_filter = array();
		foreach($this->args['filter'] as &$filter){
			if($filter['id'] != $id){
				$new_filter[] = $filter;
			}
		}
		
		$this->args['filter']= $new_filter;
		
	}

	protected function getDisplayRows(){

		if(is_null($this->args['dataset'])){
			return array();
		}

		if(!is_array($this->args['dataset'])){
			$this->paginateDataset();
			$rows = $this->args['dataset']->fetchAllRowsNum();
			$this->putFoundRows();
		}
		else{
			$rows = $this->args['dataset'];
		}

		$this->args['params'] = array_merge($this->node->getArguments(), $this->args['params']);

		$indexes = $this->args['column_indexes'];
		
		return $this->setupOperations($rows, $indexes['id'], $indexes['title']);

	}

	protected function paginateDataset(){
		if(isset($this->args['pagination']['auto']) && $this->args['pagination']['auto']){

			if(!isset($this->args['pagination']['items_per_page'])){
				$items_per_page = $this->args['pagination']['items_per_page'] = 30;
			}
			else{
				$items_per_page = $this->args['pagination']['items_per_page'];
			}
			$page = $this->node->arg($this->name.'/pagination_page');
			if(!$page){
				$page = 1;
			}
			$offset = $items_per_page*($page-1);

			$this->args['pagination']['current_page'] = $page;
			$this->args['dataset']->option('SQL_CALC_FOUND_ROWS')->limit($items_per_page, $offset);
		}
	}

	protected function putFoundRows(){
		if(isset($this->args['pagination']['auto']) && $this->args['pagination']['auto']){
			$this->args['pagination']['item_count'] = $this->db->Phaxsi->execute('SELECT FOUND_ROWS()')->fetchScalar();
		}
	}

	protected function setupOperations($rows, $id_key, $title_key){

		$params = $this->args['params'];

		$primary = $this->schema->getTable($this->args['table'])->getPrimary();
		$row_id = $primary[0];
		
		$text_helper = $this->load->helper('text');

		foreach($rows as &$row){

			$params[$row_id] = $row[$id_key];

			foreach($this->operations as $op_name => $operation){
				
				if($operation){
					$title = isset($operation[1]) ? $operation[1] : $text_helper->cut($row[$title_key], 8);
					if($op_name == 'add' || $op_name == 'edit' || ($op_name == 'explore' && !is_callable($operation))){
						$row['_url_'.$op_name] = $this->admin->deriveUrl($operation[0], $title, $params, $this->name);
					}
					else{
						$row['_url_'.$op_name] = $this->admin->deriveOperationUrl($this->name, $op_name, array($row_id => $row[$id_key]));
					}		
				}

			}

			foreach($this->extra_operations as $op_name => $operation){
				$row['_url_'.$op_name] = $this->admin->deriveOperationUrl($this->name, 'callback', array($row_id => $row[$id_key]));
			}

		}

		if(isset($this->operations['add'])){
			$this->operations['add']['url'] = $this->admin->deriveUrl($this->operations['add'][0], $this->operations['add'][1], $this->args['params'], $this->name);
		}

		return $rows;

	}

	protected function createFilter(){

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
			$node->removeArgument($this->name.'/pagination_page');

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
					$node->setArgument($filter['id'], $id);
					if($id !== $filter_value){
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

	protected function getPagination(){
		$node = clone $this->node;
		$node->setArgument($this->name.'/pagination_page', '###');
		$this->args['pagination']['link_url'] = '/admin/'.$this->admin->url->getUrl($node);
		$this->args['pagination']['url_page_marker'] = urlencode('###');
		return $this->load->block('/widgets/pagination', $this->args['pagination']);
	}

	function delete(){
		
		$args = $this->admin->context->getArguments();
		
		if($this->operations['delete']){
			$delete_paths = array();
			if(is_array($this->operations['delete'])){
				$delete_paths = $this->operations['delete'];
			}

			$table = $this->schema->getTable($this->args['table']);

			$this->db->Phaxsi->execute('START TRANSACTION');
			$success = $table->deleteRowAndFiles($args, true, $delete_paths);

			if($success){
				$this->db->Phaxsi->execute('COMMIT');
			}
			else{
				$this->db->Phaxsi->execute('ROLLBACK');
			}

		}
		else{
			$success = false;
		}
		
		return array(
			'next' => $this->admin->getCurrentUrl(), 
			'message' => array('delete', $success, $this->name)
		);
		
	}
	
	function order(){
		
		$args = $this->admin->context->getArguments();
		
		if(!isset($this->args['operations']['order']) || !$this->args['operations']['order']){
			return false;
		}

		$table_ids = $this->schema->getTable($this->args['table'])->getPrimary();
		$primary = $table_ids[0];

		if(!isset($args[$primary])){
			return false;
		}
		
		if(isset($_POST['prev'])){
			$prev_order = $this->db->from($this->args['table'])
							->where($primary, $_POST['prev'])
							->select($this->args['operations']['order'])
							->fetchScalar();
		}
		else if(isset($_POST['first'])){
			$prev_order = $this->db->from($this->args['table'])
							->where($primary, $_POST['first'])
							->select($this->args['operations']['order'])
							->fetchScalar() -0.5;
		}
		else if (isset($_POST['next'])){
			$prev_order = $this->db->from($this->args['table'])
							->where($primary, $_POST['next'])
							->select($this->args['operations']['order'])
							->fetchScalar() -0.5;
		}
		else{
			$prev_order = 0;
		}
		
		if(isset($_POST['next'])){
			$next_order = $this->db->from($this->args['table'])
							->where($primary, $_POST['next'])
							->select($this->args['operations']['order'])
							->fetchScalar();
		}
		else if(isset($_POST['last'])){
			$next_order = $this->db->from($this->args['table'])
							->where($primary, $_POST['last'])
							->select($this->args['operations']['order'])
							->fetchScalar() +0.5;
		}
		else if (isset($_POST['prev'])){
			$next_order = $this->db->from($this->args['table'])
							->where($primary, $_POST['prev'])
							->select($this->args['operations']['order'])
							->fetchScalar()+0.5;
		}
		else{
			$next_order = $this->db->from($this->args['table'])
									->select(array('max', $primary))
									->fetchScalar()+0.5;
		}
		
		if(function_exists('bcdiv')){
			$new_order = bcdiv(bcadd($prev_order, $next_order, 15), 2, 15);
		}
		else{
			$new_order =  ($prev_order+ $next_order)/2;
		}

		$this->db->into($this->args['table'])
					->where($primary, $args[$primary])
					->update(array($this->args['operations']['order'] => $new_order));

		return array('next' => false);
		
	}
	
	function explore(){
		
		$args = $this->admin->context->getArguments();
		
		if(isset($this->operations['explore'])){
			call_user_func($this->operations['explore'], $args);
		}
		
		return array('next' => $this->admin->getCurrentUrl());
		
	}
	
	function filter(){
		
		$args = $this->admin->context->getArguments();
		
		if(!$this->args['filter']){
			return false;
		}

		$filter_list = $this->args['filter'];
		if(!isset($filter_list[0])){
			$filter_list = array($filter_list);
		}

		$node = clone $this->node;

		$added = false;
		foreach($filter_list as $index => $filter){
			if(isset($args['filter'][$index]) && isset($filter['id'])){
				if($args['filter'][$index]){
					$node->setArgument($filter['id'], $args['filter'][$index]);
					$added = true;
				}
				else{
					$node->removeArgument($filter['id']);
				}
			}
		}

		if($added){
			$node->removeArgument($this->name.'/pagination_page');
		}

		$url = $this->admin->url->getUrl($node);
		print JsonHelper::encode(array('url' => $url));
		
		return array('next' => false);

		
	}
	
	function callback(){
	
		$args = $this->admin->context->getArguments();

		if(isset($this->extra_operations[$args['operation']])){
			$success = call_user_func($this->extra_operations[$args['operation']][0], $args);
			Session::setFlash('admin_message_form', array($args['operation'], $success, $this->name));
		}

		return array('next' => $this->url->getCurrentUrl());

	}

}