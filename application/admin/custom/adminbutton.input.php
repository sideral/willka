<?php

class AdminButtonInput extends FormInput{ 

	protected $url = '';
	
	function __construct($value, $name = null){
		parent::__construct('button', $value, $name);
	}
	
	function setUrl($url){
		$this->url = UrlHelper::get($url);
	}
	
	function returnsValue(){
		return false;
	}
	
	function __toString() {
		$html = new HtmlHelper();
		$this->afterHTML = $html->inlineJavascript('Phaxsi.Event.addEvent(document.getElementById("'.$this->getId().'"), "click", function(){location.href="'.$this->url.'"})');
		return parent::__toString();
	}

}
