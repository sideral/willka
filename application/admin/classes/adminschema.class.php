<?php

class AdminSchema{

	private $tables = array();

	/**
	 *
	 * @param string $name
	 * @param array $columns
	 * @param array $keys
	 * @param array $data
	 * @return AdminTable 
	 */
	function addTable($name, array $columns){
		$this->tables[$name] = new AdminTable($this, $name, $columns);
		return $this->tables[$name];
	}

	function removeTable($name){
		unset($this->tables[$name]);
	}

	function getTable($name){
		if(!isset($this->tables[$name])){
			trigger_error("Table '$name' was not defined", E_USER_ERROR);
		}
		return $this->tables[$name];
	}

	/**
	 * Gets an array with the tables that have a reference to the given table.
	 * @param string $parent The parent table name.
	 * @return array 
	 */
	function getChildren($parent){

		$children = array();
		foreach($this->tables as $table){

			$keys = $table->getKeys();
			
			foreach($keys as $column => $key){
				if($key[0] == $parent){
					$children[] = array('table' => $table, 'key' => $key, 'column' => $column);
				}
			}
		}

		return $children;
	}

}