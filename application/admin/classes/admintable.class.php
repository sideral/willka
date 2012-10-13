<?php

require_once('admintablecolumn.class.php');

class AdminTable{

	private $name, $keys;
	private $columns = array();
	private $primary = array();
	/**
	 * @var AdminSchema 
	 */
	private $schema = null;
	
	const ON_DELETE_NONE = 0;
	const ON_DELETE_CASCADE = 1;
	const ON_DELETE_RESTRICT = 2;
	const ON_DELETE_SET_NULL = 3;
	const ON_DELETE_CASCADE_REVERSED = 4;

	function __construct(AdminSchema $schema, $name, array $column_types){

		$this->schema = $schema;
		$this->name = $name;

		$columns = array();

		foreach($column_types as $c_name => $info){
			
			$type = $info[0];
			$column = new AdminTableColumn($c_name, $type);
			
			if(isset($info[1])){
				$column->setLabel($info[1]);
			}
			
			$columns[$c_name] = $column;

			if($type == 'primary'){
				$this->primary[] = $c_name;
			}

		}

		$this->columns = $columns;

		if(!$this->primary){
			foreach($this->columns as $column){
				$this->primary[] = $column->getName();
			}
		}

	}

	public function getName(){
		return $this->name;
	}

	public function getPrimary(){
		return $this->primary;
	}
	
	function getColumnsByType($type){

		$type = (array)$type;

		$columns = array();

		foreach($this->columns as $column){
			if(in_array($column->getType(), $type)){
				$columns[$column->getName()] = $column;
			}
		}

		return $columns;

	}

	function getRow($conditions){

		$db = new TableReader($this->name);

		foreach($this->primary as $column){
			if(!isset($conditions[$column])){
				//trigger_error('Argument does not contain required values', E_USER_WARNING);
				return false;
			}
			$db->where($column, $conditions[$column]);
		}

		return $db->fetchRow();

	}

	function getAllRows($conditions){
		$db = new TableReader($this->name);
		foreach($conditions as $column => $value){
			$db->where($column, $value);
		}
		return $db->fetchAllRows();
	}

	function insertRow($values){

		$ds = new TableWriter($this->name);
		$id = $ds->insert($values);

		if($id){
			return array($this->primary[0] => $id);
		}
		else{
			return $id;
		}

	}

	function updateRow($values){

		$db = new TableWriter($this->name);

		$primary_vals = array();

		foreach($this->primary as $column){
			if(!isset($values[$column])){
				trigger_error('Argument does not contain required values', E_USER_WARNING);
				return false;
			}
			$db->where($column, $values[$column]);
			$primary_vals[$column] = $values[$column];
		}

		$success = $db->update($values);

		if(!$success){
			return false;
		}

		return $primary_vals;

	}

	function deleteRowAndFiles($values, $force_primary = true, $deletable_folders =  array()){

		if($force_primary){
			$conditions = array();
			foreach($this->primary as $column){
				if(!isset($values[$column])){
					trigger_error('Argument does not contain required values', E_USER_WARNING);
					return false;
				}
				$conditions[$column] = $values[$column];
			}
		}
		else{
			$conditions = $values;
		}

		$files_to_delete = array();
		$success = $this->deleteRows($conditions, $files_to_delete);

		if($success){
			foreach($files_to_delete as $file){
				if(!is_dir($file) && file_exists($file)){
					@unlink($file);
				}
			}
		}

		return $success;

	}

	protected function deleteRows($conditions, &$files_to_delete = array()){

		if(!$conditions){
			return true;
		}

		$rows = $this->getAllRows($conditions);

		$files_to_delete = $this->getFilesToDelete($rows, $files_to_delete);

		/**
		 * Creates an array with all the the primary_key => value pairs of all the rows that have to be deleted.
		 */
		$row_conditions = array();
		foreach($rows as $row){
			$values = array();
			foreach($this->primary as $primary){
				$values[$primary] = $row[$primary];
			}
			$row_conditions[] = $values;
		}

		$children = $this->schema->getChildren($this->name);

		foreach($children as $child){

			if(!isset($child['key'][2]) || is_null($child['key'][2])){
				
			}
			elseif($child['key'][2] == self::ON_DELETE_RESTRICT){
				$reader = new TableReader($child['table']->getName());

				foreach($row_conditions as $row_condition){
					foreach($row_condition as $column => $value){
						if($column == $child['key'][1]){
							$reader->where($column, $value);
						}
					}
				}

				if($reader->count()){
					return false;
				}

			}
			elseif($child['key'][2] == self::ON_DELETE_CASCADE){

				foreach($row_conditions as $row_condition){
					$child_conditions = array();
					foreach($row_condition as $column => $value){
						if($column == $child['key'][1]){
							$child_conditions[$column] = $value;
						}
					}

					if(!$child['table']->deleteRows($child_conditions, $files_to_delete)){
						return false;
					}
				}

			}
			elseif($child['key'][2] == self::ON_DELETE_SET_NULL){
				//Not implemented yet
			}

		}

		foreach($this->getKeys() as $column => $key){

			if($key[2] == AdminTable::ON_DELETE_CASCADE_REVERSED){
				
				$table = $this->schema->getTable($key[0]);

				foreach($rows as $row){
					if(!$table->deleteRows(array($key[1] => $row[$key[1]]), $files_to_delete)){
						return false;
					}
				}				

			}

		}

		$ds = new TableWriter($this->name);
		foreach($conditions as $column => $value){
			$ds->where($column, $value);
		}

		return $ds->delete();

	}

	protected function getFilesToDelete($rows, $delete_files = array()){

		$file_columns = $this->getColumnsByType(array('filename', 'image'));

		foreach($rows as $row){
			foreach($file_columns as $column){
				
				$target_dirs = $column->getTargetDirs();
				
				foreach($target_dirs as $dir){
					$delete_files[] = PathHelper::parse($dir.DS.$row[$column->getName()]);
				}
				
				$delete_files[] = PathHelper::parse(PathHelper::join(array('{public}/'.DEFAULT_MODULE.'/admin', $this->name.'/'.$column->getName()))) . DS . $row[$column->getName()];
				$delete_files[] = PathHelper::parse(PathHelper::join(array('{public}/'.DEFAULT_MODULE.'/admin', $this->name.'/'.$column->getName().'/small'))) . DS . $row[$column->getName()];
				
			}
		}
		
		return $delete_files;

	}

	public function getKeys(){
		$keys = array();
		foreach($this->columns as $column){
			$key = $column->getForeignKey();
			if($key){
				$keys[] = $key;
			}			
		}
		return $keys;
	}

	function __get($c_name){
		return $this->getColumn($c_name);
	}
	
	public function getColumn($name){
		if(isset($this->columns[$name])){
			return $this->columns[$name];
		}
		return false;
	}

	public function getAllColumns(){
		return $this->columns;
	}


}
