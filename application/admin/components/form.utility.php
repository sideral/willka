<?php

require_once('classes/form/adminformtable.class.php');

class FormUtility extends AdminComponent{

	protected $form, $groups = array(), $tables = array();

	function initialize(){

		$this->helper->filter->defaults($this->args, array(
			'title' => $this->node->getTitle(),
			'panel' => null,
			'messages' => array(),
			'back' => false,
			'on_save' => null
		));
		
		$this->initializeFieldMap();

		$this->form = $this->load->form();

		if($_POST){
			$this->form->setRawValue($_POST);
		}

		$this->form->setAction('/admin/'.$this->admin->deriveOperationUrl($this->name, 'save'));

	}
	
	/**
	 *
	 * @param string $table_name
	 * @param array $elements
	 * @param array $config
	 * @return AdminFormTable 
	 */
	function addTable($table_name, $elements = array()){

		$table = $this->schema->getTable($table_name);
		$columns = $this->getAllColumnsForm($table);

		$prefix = 't'.count($this->tables).'_';
		
		$this->tables[] = $admin_table = new AdminFormTable($table, $prefix);
		
		$path_helper = $this->load->helper('path');	

		foreach($columns as $c_name => $c_data){

			if(isset($this->form->$c_name)){
				continue;
			}

			if(isset($elements[$c_name])){
				$c_data = array_merge($c_data, $elements[$c_name]);
			}
			
			if(isset($c_data['type'])){
				$type = $c_data['type'];
			}
			else{
				trigger_error("Invalid type for column '$c_name'.", E_USER_ERROR);
			}

			if($type == 'none'){
				continue;
			}

			$default = null;
			if(isset($c_data['value'])){
				$default = $c_data['value'];
			}

			$input = $admin_table->$c_name = $this->form->add($type, $prefix.$c_name, $default);

			$input->setTarget($prefix.$table_name, $c_name);

			if(isset($c_data['label']) && !empty($c_data['label'])){
				$input->setLabel($c_data['label']);
			}
			
			if(isset($c_data['attributes'])){
				foreach($c_data['attributes'] as $name => $value){
					$input->setAttribute($name, $value);
				}
			}

			if(isset($c_data['validator'])){
				$messages = isset($c_data['validator_msg']) ?
									$c_data['validator_msg'] : array();
				$input->setValidator($c_data['validator'], $messages);
			}
			
		}

		return $admin_table;

	}
	
	function getTable($index){
		if(isset($this->tables[$index])){
			return $this->tables[$index];
		}
		return false;		
	}

	function setPanel($panel){
		$this->args['panel'] = $panel;
	}
	
	/**
	 *
	 * @return Form 
	 */
	function getFormObject(){
		return $this->form;
	}
	
	/**
	 *
	 * @param string $prefix
	 * @param string $input_name
	 * @return IFormComponent 
	 */
	function getElement($prefix, $input_name){
		$input = $prefix.$input_name;
		return $this->form->$input;
	}

	function setValues(AdminFormTable $admin_table, $values, $overwrite_post = false){
		$prefix = $admin_table->getPrefix();
		if((!Session::getFlash($this->form->getId()) && !$_POST) || $overwrite_post){
			$prefixed_values = array();
			foreach($values as $name => $value){
				$prefixed_values[$prefix.$name] = $value;
			}
			$this->form->setValue($prefixed_values);
		}
	}

	function getValues(){
		
		//No autofill for the moment
		/*if(!$this->form->receivedValues()){
			foreach($this->tables as $admin_table){
				if($admin_table->getAutoFill()){
					$table = $admin_table->getTable();
					$primary = $table->getPrimary();
					$values = $table->getRow($params);
					if($values){
						$this->setValues($admin_table, $values);
					}
				}		
			}
		}*/

		if(!$this->groups){
			$names = array();
			foreach($this->form as $name => $element){
				$names[] = $name;
			}

			$this->fieldset($names);
		}

		return array(
			'form' => $this->form,
			'panel' => $this->args['panel'],
			'groups' => $this->groups,
			'back' => (bool)$this->args['back'],
			'messages' => $this->args['messages'],
			'title' => $this->args['title']
		);

	}

	function fieldset( array $inputs, $attributes = array(), $legend = ''){

		$single_names = array();
		$classes = array();

		$names = array();
		foreach($inputs as $class => $input){
			$names[$class] = is_object($input) ? $input->getName() : $input;
		}

		foreach($names as $class => $name){
			if(!is_array($name)){
				$single_names[] = $name;
			}
			else{
				foreach($name as $n){
					$single_names[] = $n->getName();
					$classes[$n->getName()] = $class;
				}
			}
		}

		$group = array('legend' => $legend, 'names' => $single_names, 'classes' => $classes, 'attributes' => $this->load->helper('html')->formatAttributes($attributes));
		$this->groups[] = $group;

	}

	function save(){

		$this->form->validateOrRedirect();

		$first_processed_table = true;
		$first_primary_vals = array();
		$success = true;

		$this->db->Phaxsi->execute('START TRANSACTION');
		
		$path_helper = $this->load->helper('path');
		
		/**
		 * Gets a list of all columns that are referenced.
		 */

		$saved_values = array();
		$all_values = array();
		
		foreach($this->tables as $admin_table){
			
			$prefix = $admin_table->getPrefix();
			
			$ref_values = $this->filterReferenceValues($admin_table->getAllReferences(), $saved_values);
			$del_values = $this->filterReferenceValues($admin_table->getAllDeletableReferences(), $saved_values);
			
			$table = $admin_table->getTable();
			
			$new_values = $this->form->getTargetValues($prefix.$table->getName());
			$new_values = array_merge($new_values, $admin_table->getAdditionalValues(), $ref_values);
			
			if($admin_table->getFilter()){
				$new_values = call_user_func($admin_table->getFilter(), $new_values);
			}
			
			$current_values = false;
			
			$multirow_input = $admin_table->getMultiRowInput();
			
			if($multirow_input){
				$mr_name = $multirow_input->getRealName();
				$tmp_values = array();
				foreach($new_values[$mr_name] as $mr_value){
					$tmp_non_mr_values = $new_values;
					$tmp_non_mr_values[$mr_name] = $mr_value;
					$tmp_values[] = $tmp_non_mr_values;
				}
				$new_values = $tmp_values;
			}
			else{
				$current_values = $table->getRow($new_values);
				$new_values = array($new_values);
			}
			
			$multicolumn_inputs = $admin_table->getMultiColumnInputs();
			
			$tmp_values = array();
			foreach($new_values as $name => $new_value){
				$mc = false;
				foreach($multicolumn_inputs as $mc_name => $mc_input){
					if($mc_name == $name && is_array($new_value)){
						foreach($new_value as $col => $new_val){
							$tmp_values[$col] = $new_val;
						}
						$mc = true;
						break;
					}
				}
				if(!$mc){
					$tmp_values[$name] = $new_value;
				}
			}
			$new_values = $tmp_values;
			
			if(!isset($all_values[$table->getName()])){
				$all_values[$table->getName()] = array();
			}
			$all_values[$table->getName()][] = $new_values;
			
			//Update table
			if($current_values){
				
				$this->deleteFilesAndImages($admin_table, $current_values, $new_values[0]);	
				
				$primary_vals = $table->updateRow($new_values[0]);

				if($primary_vals){
					$saved_values = array_merge($saved_values, $this->getSavedValues($primary_vals, $new_values[0], $prefix));
				}
				else{
					$success = false;
					$this->db->Phaxsi->execute('ROLLBACK');
					trigger_error("A value could not be inserted", E_USER_WARNING);
					break;
				}

				if($first_processed_table){
					$is_new = false;
				}
				
				/*if($this->args['on_update']){
					$continue = call_user_func($this->args['on_update'], $admin_table, $new_values[0], $primary_vals);
					if($continue === false){
						$success = false;
						$this->db->Phaxsi->execute('ROLLBACK');
						break;
					}
				}*/

			}
			else{
				
				//Delete everything with this conditions
				if($del_values){
					$table->deleteRowAndFiles($del_values, false);
				}
				
				foreach($new_values as $new_value){
					$this->deleteOriginalImage($admin_table, $new_value);
					$primary_vals = $table->insertRow($new_value);
					if($primary_vals === false){
						$this->db->Phaxsi->execute('ROLLBACK');
						$success = false;
						break 2;
					}
				}

				if($primary_vals && $new_values ){
					$saved_values = array_merge($saved_values, $this->getSavedValues($primary_vals, $new_values[count($new_values)-1], $prefix));
				}

				if($first_processed_table){
					$first_primary_vals = $primary_vals;
					$is_new = true;
				}
				
				if($success){
					$continue = $admin_table->fireOnInsert($new_values, $primary_vals, $is_new);
					if($continue === false){
						$success = false;
						$this->db->Phaxsi->execute('ROLLBACK');
						break;
					}
				}

			}

			$first_processed_table = false;

		}
		
		if($this->args['on_save']){
			call_user_func($this->args['on_save'], $all_values);
		}
			
		$this->db->Phaxsi->execute('COMMIT');
		
		$args = $this->admin->context->getArguments();
		
		$go_back = true;
		if(isset($args['back'])){
			$go_back = (bool)$args['back'];
		}

		$component = $go_back? $this->node->getComponent() : $this->name;
		
		$message = array($is_new? 'add' : 'edit', $success, $component);		

		if(!$is_new){
			$next = $go_back ? $this->admin->url->getParentUrl($this->node) : $this->admin->getCurrentUrl();
			return array(
				'next' => $next, 
				'message' => $message
			);
		}
		else if($first_primary_vals){

			$node = clone $this->node;
			foreach($first_primary_vals as $key => $val){
				$node->setArgument($key, $val);
			}

			if(is_object($this->args['back'])){
				foreach($first_primary_vals as $key => $val){
					$this->args['back']->where($key, $val);
				}
				$node->setTitle($this->args['back']->fetchScalar());
			}
			
			$next = $go_back ? $this->admin->url->getParentUrl($this->node) : $this->admin->url->getUrl($node);
			return array(
				'next' => $next, 
				'message' => $message
			);
			
		}
		else{
			return array(
				'next' => $this->admin->url->getParentUrl($this->node), 
				'message' => $message
			);
		}

	}
	
	protected function getAllReferencedColumns(){
		$all = array();
		foreach($this->tables as $admin_table){
			foreach($admin_table->getAllReferences() as $ref){
				$all[] = $ref->getRealName();
			}
			foreach($admin_table->getAllDeletableReferences() as $ref){
				$all[] = $ref->getRealName();
			}
		}
		return array_unique($all);
	}

	protected function filterReferenceValues($refs, $values){
		$key_vals = array();
		foreach($refs as $column => $table_ref){
			$column_name = $table_ref->getName();
			if(isset($values[$column_name])){
				$key_vals[$column] = $values[$column_name];
			}
		}
		return $key_vals;
	}
	
	protected function deleteFilesAndImages($admin_table, $row, $values){
		
		$table = $admin_table->getTable();
		$prefix = $admin_table->getPrefix();
		$path_helper = $this->load->helper('path');	
		
		$file_columns = $table->getColumnsByType(array('filename', 'image'));

		foreach($file_columns as $column){
			$column_name = $column->getName();
			//If the file exists and has changed its name.
			if($row[$column_name] != '' && $row[$column_name] != $values[$column_name]){

				$saving_target = $this->form->{$prefix.$column_name}->getSavingTarget();

				$filelist = array($saving_target[0]);

				if($this->form->{$prefix.$column_name} instanceof InputFileImage){
					 $thumbs = $this->form->{$prefix.$column_name}->getThumbsTarget();
					 foreach($thumbs as $thumb){
						  $filelist[] = $thumb[1];
					 }
				}

				foreach($filelist as $path){
					 if($path){
						  $dirname = dirname($path_helper->parse($path));
						  $file = $dirname . DS . $row[$column_name];
						  @unlink($file);
					 }
				}

			}

		}

		$this->deleteOriginalImage($admin_table, $values);
		
	}
	
	protected function deleteOriginalImage($admin_table, $new_values){
		
		$table = $admin_table->getTable();
		$prefix = $admin_table->getPrefix();
		$path_helper = $this->load->helper('path');	
		
		$image_columns = $table->getColumnsByType('image');
	
		foreach($image_columns as $column){
			$column_name = $column->getName();
			$filedef = $admin_table->{$column_name}->getImageConfiguration();
			if($filedef && $filedef['keep_original_image'] == false
				&& $this->form->{$prefix.$column_name}
				&& $this->form->{$prefix.$column_name} instanceof InputFile){
				$saving_target = $this->form->{$prefix.$column_name}->getSavingTarget();
				 if($saving_target[0]){
					  $dirname = dirname($path_helper->parse($saving_target[0]));
					  $file = $dirname . DS . $new_values[$column_name];
					  @unlink($file);
				 }
			}
		}
	}
	
	protected function getSavedValues($primary_vals, $new_values, $prefix){
		$all_references = $this->getAllReferencedColumns();
		$prefixed_vals = array();
		foreach($primary_vals as $name => $val){
			$prefixed_vals[$prefix.$name] = $val;
		}
		foreach($all_references as $col){
			if(!isset($prefixed_vals[$prefix.$col]) && isset($new_values[$col])){
				$prefixed_vals[$prefix.$col] = $new_values[$col];
			}
		}
		return $prefixed_vals;
	}
	
	protected function getAllColumnsForm($table){
		
		$table_columns = $table->getAllColumns();
		
		$columns = array();
		foreach($table_columns as $name => $column){
			$col_type = $column->getType();
			$col = array();
			$col['label'] = $column->getLabel();
			$col['type'] = $this->type_field_map[$col_type][0];
			$col['original_type'] = $col_type;
			$col['validator'] = $this->type_field_map[$col_type][1];
			$col['attributes'] = $this->type_field_map[$col_type][2];
			$columns[$name] = $col;
		}
		return $columns;
	}
	
	protected function initializeFieldMap(){

		$this->type_field_map = array(
			'primary'	=> array('hidden', array(), array()),
			'key'		=> array('hidden', array(), array()), 
			'int'		=> array('text', $this->valid->integer, array('class' => 'form_input_text_quarter')), 
			'string'	=> array('text', array(), array()),
			'filename'	=> array('/widgets/editablefile', array(), array()),
			'image'		=> array('/admin/adminimage', array(), array()),
			'char'		=> array('text', array(), array()),
			'varchar'	=> array('text', array(), array()),
			'date'		=> array('/widgets/datepicker', $this->valid->european_date, array()),
			'datetime'	=> array('/widgets/datetimepicker', array(), array()),
			'enum'		=> array('dropdown', array(), array()),
			'float'		=> array('text', $this->valid->numeric, array('class' => 'form_input_text_quarter')),
			'decimal'	=> array('text', $this->valid->numeric, array('class' => 'form_input_text_quarter')),
			'double'	=> array('text', $this->valid->numeric, array('class' => 'form_input_text_quarter')),
			'boolean'	=> array('checkbox', array(), array()),
			'bool'		=> array('checkbox', array(), array()),
			'timestamp' => array('none', array(), array()),
			'text'		=> array('textarea', array(), array()),
			'html'		=> array('/widgets/tinymceeditor', array(), array())
		);
	
	}

}
