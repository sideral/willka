<?php

	require_once('list.utility.php');

	class GalleryUtility extends ListUtility{

		function initialize(){

			$this->helper->filter->defaults($this->args, array(
				'title' => $this->node->getTitle(),
				'operations' => array(),
				'dataset' => null,
				'row_operation' => 'edit',
				'table' => '',
				'icon_base' => '',
				'messages' => array(),
				'row_operation' => 'edit',
				'filter' => array(),
				'pagination' => array(),
				'base' => '',
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

		}

		function getValues(){
			
			$base = $this->args['base'];
			
			if(!$base){
				$images = $this->schema->getTable($this->args['table'])->getColumnsByType('image');
				if($images){
					$base = $this->args['table'].'/'.current($images)->getName();
				}
			}
			
			$values = parent::getValues();
			$values['image_base'] = $base;
			return $values;

		}
	
		
	}


?>
