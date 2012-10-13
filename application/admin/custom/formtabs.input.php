<?php

class FormTabsInput extends FormInput{

	private $tabs = array();

	function __construct($value = '', $name = null){
		parent::__construct('hidden', $value, $name);
		$this->setValidator(array(), array('callback' => 'Hello'));
	}

	function addTabs($tabs){
		$this->tabs = array_merge($this->tabs, $tabs);
	}

	function  __toString() {

		$help = new HtmlHelper();

		$html = $help->css('/admin/custom/formtabs.css'). $help->javascript('/admin/custom/formtabs.js');

		$html .=  '<div id="tabs">';

		foreach($this->tabs as $tab){
			$html .=  $help->absoluteLink($tab[0], '#'.$tab[1]);
		}

		$html .= '<div class="clear"></div></div>';

		$html .= $this->getClientValidationHtml();

		return $html;

	}

	function returnsValue(){
		return false;
	}

	function getClientValidationConfig(){
		return array('callback' => 'validateTabs');
	}

}
