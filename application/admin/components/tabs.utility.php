<?php

class TabsUtility extends AdminComponent{

	private $links = array();

	function initialize(){

		$this->helper->filter->defaults($this->args,
			array('links' => array())
		);

		$links = array();

		foreach($this->args['links'] as $index => $link){

			$params = isset($link['params']) ? $link['params']: $this->node->getArguments();

			$attributes = isset($link['attributes'])? $link['attributes'] : array();
			if(isset($attributes['class'])){
				$attributes['class'] .= ' tab';
			}
			else{
				$attributes['class'] = 'tab';
			}

			if($link['path'] == $this->node->getPath()){
				$attributes['class'] .= ' selected';
			}

			$node = clone $this->node;
			$node->setPath($link['path']);
			$node->setTitle(isset($link['title'])? $link['title'] : $link['label']);
			
			foreach($params as $key => $value){
				$node->setArgument($key, $value);
			}
			
			$url = $this->admin->url->getUrl($node);

			$links[] = array('label' => $link['label'],
							 'url' => $url,
							 'path' => $link['path'],
							 'attributes' => $attributes);
		}

		$this->links = $links;

	}

	function process(){
		$args = $this->env->getArguments();
		$link = $this->links[$args['panel_index']];
		call_user_func($link['path'], $args);
		return false;
	}

	function getValues(){
		return array('links' => $this->links);
	}


}
