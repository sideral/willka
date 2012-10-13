<?php

class AdminShell extends Shell{
	
	function dbschema(){
		
		if(!is_writable(APPD_TMP)){
			$this->view->write('ERROR: tmp directory is not writable.');
			return;
		}
		
		if(!isset($this->args['m'])){
			$this->view->write('ERROR: Parameter -m (module name) is required');
			return;
		}
		
		$driver = 'default';
		if(isset($this->args['d'])){
			$driver = $this->args['d'];
		}
		
		$tables = array();
		
		foreach($this->args as $i=> $table){
			if(is_numeric($i)){
				$this->db->Phaxsi->loadDriver($driver);
				$columns = $this->db->Phaxsi->execute('SHOW COLUMNS FROM `'.$table.'`')->fetchAllRows();
				
				foreach($columns as &$column){
					$type = substr($column['Type'], 0, (int)strpos($column['Type'], '('));
					if(!$type){
						$type = $column['Type'];
					}
					if(substr($type, -3) == 'int'){
						$type = 'int';
					}
					if($column['Key'] == 'PRI'){
						$type = 'primary';
					}
					$column['Type'] = $type;
				}
				$tables[$table] = $columns;
				
			}
		}
		
		$contents = (string)$this->load->block('schema', array('module' => $this->args['m'], 'tables' => $tables));
		
		$filename = APPD_TMP.DS.$this->args['m'].'config.utility.php';
		file_put_contents($filename, $contents);
		
		$filename = str_replace(APPD, '', $filename);
		
		$this->view->write('The schema of '.count($tables). ' tables was successfully generated and stored on the file '.$filename);
		
	}
	
}
