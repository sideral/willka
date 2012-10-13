<?php

class AdminComponentList{
	
	protected $admin, $load, $components = array();
	
	function __construct($load, $admin){
		$this->load = $load;
		$this->admin = $admin;
	}
	
	function create($type, $args = array(), $name = null){

		if(!$name){
			$name = count($this->components);
		}

		$path = $this->admin->node->getPath(true);
		$name = $path[0].'/'.$path[1].'/'.$type . '/'. $name;

		$parts = explode('/', $type, 2);
		
		if(count($parts) == 2){
			$path = '/'.$parts[0].'/custom/'.$parts[1];
		}
		else{
			$path ='/admin/components/'.$type;
		}

		$component = $this->load->utility($path);
		$component->setup($name, $args, $this->admin);
		$component->initialize();

		return $component;

	}
	
	
	/**
	 *
	 * @param string $type
	 * @param array $args
	 * @param string $name
	 * @return AdminComponent 
	 */
	function add($type, $args = array(), $name = null){
		$component = $this->create($type, $args, $name);
		$this->components[$component->getName()] = $component;
		return $component;
	}
	
	function get($name){
		if(isset($this->components[$name])){
			return $this->components[$name];
		}
		return false;
	}
	
	function process($current_component, $operation){

		$result = null;

		foreach($this->components as $component){
			if($component->getName() == $current_component){
				$result = $component->$operation();
				break;
			}

			$subs = $component->getSubComponents();
			
			foreach($subs as $sub){
				if($sub->getName() == $current_component){
					$result = $sub->$operation();
					break;
				}
			}
		}
		
		if(isset($result['message'])){
			Session::setFlash('admin_message', $result['message']);
		}
		
		return $result['next'];

	}
	
	
	final function __toString(){

		$html = '';
		foreach($this->components as $component){
			$html .= $component->getHtml();
		}

		return $html;

	}
	
}