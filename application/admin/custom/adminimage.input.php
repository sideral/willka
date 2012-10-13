<?php

require_once(APPD_APPLICATION.DS.'widgets/custom/editableimage.input.php');

class AdminImageInput extends EditableImageInput{
	
	function setupThumbs($filedef, $imagedef){
		
		$path_helper = new PathHelper();
		
		foreach($imagedef['thumbs'] as &$thumb){
			$thumb[1] = $path_helper->join(array($imagedef['base_dir'],$thumb[1],$filedef['filename']), '/');
		}
		
		if($imagedef['admin_dir'] !== false){

			#For the list
			$path_1 = $path_helper->join(array('{public}/'.DEFAULT_MODULE.'/admin', $imagedef['admin_dir'], 'small' , $filedef['value']), '/');
			$admin_thumb_1 = array('crop',$path_1, 30, 30);
			$imagedef['thumbs'][] = $admin_thumb_1;

			#For the gallery
			$path_2 = $path_helper->join(array('{public}/'.DEFAULT_MODULE.'/admin', $imagedef['admin_dir'], $filedef['value']), '/');
			$admin_thumb_2 = array('filled', $path_2, 160);
			$imagedef['thumbs'][] = $admin_thumb_2;
			
		}
		
		$thumbs = $imagedef['thumbs'];
		
		if($thumbs){
			$last = $thumbs[count($thumbs)-1];
			$parts = explode('/', $last[1]);
			$dir = implode('/',array_slice($parts, 3, count($parts)-4));
			$this->setBasePath(UrlHelper::resource('/'.DEFAULT_MODULE.'/admin/'.$dir));
		}
		
		$this->setThumbsTarget($thumbs);
		
	}

}
