<?php

class PanelUtility extends AdminComponent{

	private $links = array();

	function initialize(){

		$this->helper->filter->defaults($this->args,
			array('links' => array(), 'title' => '')
		);

		$links = array();

		foreach($this->args['links'] as $index => $link){

			$external = isset($link['external'])? $link['external'] : false;

			$params = isset($link['params']) ? $link['params']: array();

			if(!is_callable($link['path'])){
				if(!$external){
					$title = isset($link['title'])? $link['title'] : $link['label'];
					$url = $this->admin->deriveUrl($link['path'], $title, $params, $this->name);
				}
				else{
					$url = "/".$link['path'].'?'. http_build_query($params);
				}
			}
			else{
				$params['panel_index'] = $index;
				$url = $this->admin->deriveOperationUrl($this->name, 'callback', $params);
			}

			$attributes = isset($link['attributes'])? $link['attributes'] : array();
			if(isset($attributes['class'])){
				$attributes['class'] .= ' action-button';
			}
			else{
				$attributes['class'] = 'action-button';
			}

			$links[] = array('label' => $link['label'],
							 'url' => $url,
							 'path' => $link['path'],
							 'external' => $external,
							 'attributes' => $attributes,
							 'icon'	=> isset($link['icon'])? $link['icon'] : 'list');
		}

		$this->links = $links;

	}

	function callback(){
		$args = $this->admin->context->getArguments();
		$link = $this->links[$args['panel_index']];
		call_user_func($link['path'], $args);
		return false;
	}

	function getValues(){
		return array('links' => $this->links, 'title' => $this->args['title']);
	}


}
