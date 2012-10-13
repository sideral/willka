<?php

class MenuUtility extends AdminComponent{

	private $entries = array();

	function initialize(){
		$this->helper->filter->defaults($this->args, 
				  array('title' => $this->lang->content,
						'entries' => array(),
						'messages' => array())
		);

		$entries = array();
		foreach($this->args['entries'] as $entry){
			if(!is_callable($entry[1])){
				$link = $this->admin->deriveUrl($entry[1], isset($entry[3]) ? $entry[3] :  $entry[0], isset($entry[2]) ? $entry[2] : array(), $this->name);
			}
			else{
				$link = $this->admin->deriveOperationUrl($this->name, 'callback', array('entry_index' => count($entries)));
			}

			$entries[] = array($entry[0], $link, $entry);
		}

		$this->entries = $entries;

	}

	function getValues(){
		return array('entries' => $this->entries,
					 'title' => $this->args['title'],
					 'messages' => $this->args['messages']);
	}

	function callback(){
		$args = $this->admin->context->getArguments();
		$entry = $this->entries[$args['entry_index']];
		return call_user_func($entry[2][1], $entry[2][2]);
	}

}
