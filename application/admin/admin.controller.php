<?php

require_once('classes/admin.class.php');

class AdminController extends Controller{

	function admin_setup(){

		if($this->layout){
			$this->layout = $this->load->layout('admin');
		}
		
		$default_utility = '/'.DEFAULT_MODULE.'/admin/'.DEFAULT_MODULE.'admin';
		if(!file_exists(APPD_APPLICATION . '/'.DEFAULT_MODULE.'/admin/'.DEFAULT_MODULE.'admin.utility.php')){
			$default_utility = '/admin/admin/adminadmin';
		}
		
		$util = $this->load->utility($default_utility);
		$path = $util ? DEFAULT_MODULE.'/'.DEFAULT_MODULE:'admin/index';
		
		$default_config = array(
			'roles' => array(
				'admin' => array(
					'home' => $path,
					'modules' => '*'
				)
			),
			'time_limit' => 500,
			'memory_limit' => '128M'
		);
		
		$this->config = array_merge($default_config, $this->config);
		
		$roles = array_keys($this->config['roles']);
		
		$this->plugin->Auth->addArea('admin-area', $roles);
		
		foreach($roles as $role){
			$this->plugin->Auth->addRole($role, array('member-area', 'admin-area', 'open-area'));
		}

		$this->plugin->Auth->authorize('admin-area',
			array('admin', 'node', 'node_process', 'error'),
			array(&$this, '_goLogin')
		);

		if(isset($this->config['language']) && $this->config['language'] != Lang::getCurrent()){
			$this->helper->redirect->to(UrlHelper::current($this->config['language']));
		}
		
		if(!ini_get('safe_mode') && is_callable('set_time_limit') && is_callable('ini_set')){
			@set_time_limit($this->config['time_limit']);
			@ini_set("memory_limit",$this->config['memory_limit']);
		}

	}

	function login(){

		$this->layout = $this->load->layout('auth');

		$login = $this->view->set('login', $this->load->form('/auth/login'));
		$login->setAction("/auth/login_process");

		$login->setCharset('utf-8');
		$login->setErrorSummary($this->lang->get('/auth/auth')->invalid_user);

		$login->next->setValue('/admin');
		$login->module->setValue('admin');

		$login->disableClientValidation();
		
		$this->view->addHelper('form_helper', 'form');

	}

	function admin(){
		$current_role = $this->plugin->Auth->get('user_role');
		if(!isset($this->config['roles'][$current_role]['home'])){
			trigger_error("Role '$current_role' doesn't have a home action.", E_USER_ERROR);
		}
		$node = new AdminNode($this->config['roles'][$current_role]['home'], $this->lang->home, '');
		$builder = new AdminUrlBuilder();		
		$this->helper->redirect->to('admin/'.$builder->getUrl($node, true));
	}

	function node(){

		$admin = new Admin($this->context);

		if(isset($this->args['operation'])){
			
			$this->layout = '';			
			
			$next = $admin->components->process($this->args['component'], $this->args['operation']);
			
			if(!$next){
				exit;
			}
			else if($next[0] != '/'){
				$this->helper->redirect->to('/admin/'.$next);
			}
			else{
				$this->helper->redirect->to($next);
			}
		}
				
		$this->layout->setTitle($this->lang->admin .' > '.$admin->node->getTitle());
		$this->layout->addSettings($this->context);
		$this->layout->setMenuEntries($admin->menu->getEntries());
		$this->view->set('current_node_id', $admin->node->getId());
		$this->view->set('components', $admin->components);

	}

	function node_process(){
		$builder = new AdminUrlBuilder();
		$node = $builder->getNode($this->context->getArguments());
		$this->view->setRedirect($builder->getUrl($node, true));
	}

	function error(){
		if($this->layout){
			$this->layout->setTitle($this->lang->admin);
			$this->layout->addSettings($this->context);
		}
	}

	function ping_json(){}

	function _goLogin(){
		$this->helper->redirect->to('/admin/login');
	}

}
