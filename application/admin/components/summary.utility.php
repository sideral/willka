<?php

class SummaryUtility extends AdminComponent{

	private $entries = array();

	function initialize(){
		
		$this->helper->filter->defaultsRecursive($this->args, array(
			'title' => $this->lang->content,
			'dataset' => array(),
			'link' => array(
				'text' => $this->lang->summary['all'],
				'path' => '',
				'title' => '',
				'args' => array()
			)
		));
		
		$dataset = $this->args['dataset'];
		if(is_object($dataset)){
			$dataset = $dataset->fetchAllRowsNum();
		}

		$this->entries = $dataset;

	}

	function getValues(){
		return array(
			'entries' => $this->entries,
			'title' => $this->args['title'],
			'all_url' => $this->admin->deriveUrl($this->args['link']['path'], $this->args['link']['title']? $this->args['link']['title'] :$this->args['title'], $this->args['link']['args'], $this->name),
			'all_text' => $this->args['link']['text']
		);
	}

}
