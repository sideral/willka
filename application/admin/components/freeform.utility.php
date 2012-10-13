<?php

class FreeFormUtility extends AdminComponent{

	protected $form, $targets = array();

	function initialize(){

		$this->helper->filter->defaults($this->args,
				array('form' => '/phaxsi/form',
					  'title' => $this->env->getParam('title'),
					  'messages' => array(),
					  'back' => false));

		$this->form = $this->load->form($this->args['form']);

		if($_POST){
			$this->form->setRawValue($_POST);
		}

		$this->form->setAction('/admin/'.$this->env->getProcessUrl($this->name));

	}


	function getFormObject(){
		return $this->form;
	}

	function setValues($inputs, $values, $overwrite_post = false){
		$prefix = is_string($inputs) ? $inputs : $inputs->__prefix;
		if((!Session::getFlash($this->form->getId()) && !$_POST) || $overwrite_post){
			$prefixed_values = array();
			foreach($values as $name => $value){
				$prefixed_values[$prefix.$name] = $value;
			}
			$this->form->setValue($prefixed_values);
		}
	}

	function getValues(){

		$group = array(
			'attributes' => '',
			'legend' => '',
			'classes' => array()
		);

		return array('form' => $this->form,
					 'group' => $group,
					 'back' => (bool)$this->args['back'],
					 'messages' => $this->args['messages'],
					 'title' => $this->args['title']);

	}

	function process(){

		$this->form->validateOrRedirect();

		$success = true;

		$args = $this->env->getArguments();
		$back = 1;
		if(isset($args['back'])){
			$back = $args['back'];
		}

		$component = $back == 1 ? $this->env->getComponent() : $this->name;

		$returns = array($new? 'add' : 'edit', $success, $component);
		Session::setFlash('admin_message_form', $returns);

		if(!$new || $back == 1){
			return $this->env->getPreviousUrl($back);
		}
		else{
			return $this->env->getPreviousUrl();
		}

	}

}
