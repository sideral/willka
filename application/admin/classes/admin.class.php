<?php

require_once('adminschema.class.php');
require_once('admincomponent.class.php');
require_once('adminconfigutility.class.php');
require_once('admintable.class.php');
require_once('adminutility.class.php');
require_once('adminmenu.class.php');
require_once('adminurlbuilder.class.php');
require_once('admincomponentlist.class.php');

class Admin{

	private $context, $modules, $node, $schema, $menu, $components, $history, $url;

	function __construct(Context $context){

		$this->context = $context;
		
		$history = $this->history = AdminHistory::getInstance();
		$url = $this->url = new AdminUrlBuilder();
		$node = $this->node = $url->getNode($context->getArguments());
		$schema = $this->schema = new AdminSchema();
		 
		$menu = $this->menu = new AdminMenu($this);
		$load = new Loader($context);
		
		$components = $this->components = new AdminComponentList($load, $this);
		
		$modules = array();
		$dirs = scandir(APPD_APPLICATION);
		
		foreach($dirs as $dir){
			
			if($dir[0] != '.'){
				$file = APPD_APPLICATION .DS.$dir.DS.'admin'.DS.$dir.'admin.utility.php';
				if(file_exists($file)){
					$utility = $load->utility("/$dir/admin/{$dir}admin");
					$utility->initialize($this, $components);
					$modules[$dir] = $utility;
				}
				$file_config = APPD_APPLICATION .DS.$dir.DS.'admin'.DS.$dir.'config.utility.php';
				if(file_exists($file_config)){
					$utility_config = $load->utility("/$dir/admin/{$dir}config");
					$utility_config->configureSchema($schema);
					$utility_config->configureMenu($menu);
				}
			}
		}

		$this->modules = $modules;
		
		$current_module = $this->node->getModule();
		$module = false;
		if(isset($this->modules[$current_module])){
			$module = $this->modules[$current_module];
		}
		
		if(!$module){
			return false;
		}
		
		$module->execute();

	}

	function __get($name){
		if(isset($this->$name)){
			return $this->$name;
		}
		trigger_error("Property '$name' does not exist.", E_USER_ERROR);
	}
	
	function deriveUrl($path, $title, $params = array(), $component = ''){
		return $this->url->getChildUrl($this->node, $path, $title, $params, $component);
	}
	
	function deriveOperationUrl($component, $operation, $args = array()){
		return $this->url->getOperationUrl($this->node, $component, $operation, $args);
	}
	
	function getCurrentUrl(){
		return $this->url->getUrl($this->node);		
	}
	
}
