<?php

class AdminFormInput{
		
	private $input = null;
	private $real_name = '';
	private $reference = null;
	private $deletable_reference = null;
	private $multirow = false;
	private $multicolumn = false;
	private $filedef = array();
	private $imagedef = array();

	function __construct($input, $real_name) {
		$this->input = $input;
		$this->real_name = $real_name;
		$this->multirow = !$input->isScalar();
	}
	
	function __call($function, $args){
		return call_user_func_array(array($this->input, $function), $args);
	}
	
	function getRealName(){
		return $this->real_name;
	}
	
	function setNote($note){
		$this->input->setData('note', $note);
	}
	
	function getInput(){
		return $this->input;
	}
	
	function setReference(AdminFormInput $ref, $deletable = false){
		$this->reference = $ref;
		if($deletable){
			$this->deletable_reference = $ref;
		}
	}
	
	function getReference(){
		return $this->reference;
	}
	
	function setDeletableReference($ref){
		$this->deletable_reference = $ref;
	}
	
	function getDeletableReference(){
		return $this->deletable_reference;
	}
	
	function setMultiRow($multirow){
		$this->multirow = (bool)$multirow;
	}
	
	function isMultiRow(){
		return $this->multirow;
	}
	
	function setMultiColumn($multicolumn){
		$this->multicolumn = $multicolumn;
	}
	
	function isMultiColumn(){
		return $this->multicolumn;
	}
	
	function setupFile($base_dir, $filename = '{name}.{ext}', $value = null){
		
		if(!$value){
			$value = $filename;
		}
		
		$this->filedef = array(
			'filename' => $filename,
			'base_dir' => $base_dir,
			'value' => $value
		);
		
		$path_helper = new PathHelper();
		$path = $path_helper->join(array($base_dir, $filename), '/');
		$this->setSavingTarget($path, $value);
		
	}
	
	function setupImage($base_dir, $thumbs, $keep_original_image = true, $admin_dir = null){
		
		if(!$this->filedef){
			trigger_error('You must run first setupFile before calling setupImage.', E_USER_ERROR);
			return false;
		}
		
		if(!$admin_dir){
			$target = $this->getTarget();
			$table = preg_replace('/^t[0-9]+_/', '', $target[0]);
			$admin_dir = $table.'/'.$target[1];
		}
		
		$this->imagedef = array(
			'base_dir' => $base_dir,
			'admin_dir' => $admin_dir,
			'thumbs' => $thumbs,
			'keep_original_image' => $keep_original_image
		);
		
		$this->setupThumbs($this->filedef, $this->imagedef);
		
	}
	
	function getFileConfiguration(){
		return $this->filedef;
	}
	
	function getImageConfiguration(){
		return $this->imagedef;
	}

}