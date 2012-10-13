<?php

class AdminAdminUtility extends AdminUtility{

	private $user = null;

	function module_setup(){}

	function index(){}

	function settings(){

		$form = $this->components->add('form',	array(
			'title' => $this->lang->change_password, 
			'back' => true,
			'messages' => array('edit' => $this->lang->settings_msg['pass_change'])
		));

		$this->user = $user = $form->addTable('user',	array(
			'username' => array('type' => 'hidden'),
			'role' => array('type' => 'none'),
			'email' => array('type' => 'none'),
			'password' => array('type' => 'password'),
			'activation_key' => array('type' => 'none'),
			'enabled' => array('type' => 'none')
		));
		
		$user->password->setLabel($this->lang->new_password);
		$user->password->setFilter(array(&$this, '_filterSaltedHash'));
		$user->password->setValidator(array('required' => true, 'min_length' => 6), array('min_length' => $this->lang->password_too_short));
		$user->password->setAttribute('autocomplete', 'off');
		
		$obj = $form->getFormObject();

		$confirm = $obj->add('password', 'confirm_password');
		$confirm->setLabel($this->lang->retype_password);
		$confirm->setFilter(array(&$this,'_filterSaltedHash'));
		$confirm->setValidator(
				array('comparisons' => array($user->password->getValue() == $confirm->getValue())),
				array('comparisons/0' => $this->lang->password_no_match)
		);

		$old = $obj->add('password', 'old_password');
		$old->setLabel($this->lang->old_password);
		$old->setFilter(array(&$this,'_filterSaltedHash'));
		$old->setValidator(
			array('required' => true,'callback' => array(&$this, '_validatePassword')),
			array('callback' => $this->lang->password_invalid)
		);

		$values = $this->db->from('user')->where('user_id', $this->plugin->Auth->get('user_id'))->fetchRow();

		$form->setValues($user, $values);

	}

	function _filterSaltedHash($value){
		return $this->plugin->Auth->getPasswordHash($value, $this->user->username->getValue());
	}

	function _validatePassword($value){
		$value = $this->_filterSaltedHash($value);
		$user_id = $this->db->Auth->getUserId($this->plugin->Auth->get('username'), $value);
		return $user_id == $this->plugin->Auth->get('user_id');
	}

}
