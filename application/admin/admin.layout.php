<?php

class AdminLayout extends Layout {

	function admin(){

		$this->setJsLibrary('http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js');

		$this->addStyle('jquery-ui/css/ui-lightness/jquery-ui-1.8.4.custom.css');
		$this->addScript('jquery-ui/js/jquery-ui-1.8.4.custom.min.js');

		$this->addStyle('admin.css');
		$this->addScript('admin.js');

		$this->addScript('/widgets/tiny_mce/tiny_mce.js');
		$this->addScript('jquery.tablednd_0_5.js');

	}

	function auth(){
		$this->addStyle('admin.css');
		$this->addScript('admin.js');
		$this->charset = 'utf-8';
	}

	function addSettings($context){
		$url = new AdminUrlBuilder();
		$this->view->set('settings', $url->getRootChildUrl('admin/settings', $this->lang->settings));
	}
	
	function setMenuEntries($entries){
		$this->view->set('menu_entries', $entries);
	}

}
