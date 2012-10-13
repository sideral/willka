<?php

	class AdminBlock extends Block{

		function component(){

			$this->helper->filter->defaults($this->args,
				array('template' => '', 'values' => array())
			);

			if(!$this->args['template']){
				trigger_error('Block template is needed for component', E_USER_ERROR);
			}
			
			$this->view->setTemplate($this->args['template']);

			$this->view->setArray($this->args['values']);
			
			$this->view->addHelper('form_helper', 'form');

		}

		function breadcrumb(){

			$this->helper->filter->defaults($this->args, array('current_node_id' => ''));

			$history = AdminHistory::getInstance();
			
			$list =  $history->getBranch($this->args['current_node_id']);

			$links = array();			
			foreach($list as $id => $node){
				$links[] = array('text' => $node->getTitle(), 'url' => '/admin/node/'.$id);
			}
			
			$this->view->addHelper('text');
			$this->view->set('links', $links);
			
		}
		
		function menu(){
			$this->helper->filter->defaults($this->args,
				array('entries' => array())
			);			
			$this->view->setArray($this->args);			
		}

		function message(){

			$this->helper->filter->defaults($this->args, array('messages' => array(), 'component' => '', 'status' => array()));

			if($this->args['status']){
				$status = $this->args['status'];
			}
			else{
				$status = Session::getFlash('admin_message');
			}

			if($status){

				if($status[2] != $this->args['component']){
					$status = '';
				}
				else{
					$success = $status[1] ? 0 : 1;

					if(isset($status[3])){
						$text = $status[3];
					}
					else if(isset($this->args['messages'][$status[0]][$success])){
						$text = $this->args['messages'][$status[0]][$success];
					}
					else if(isset($this->lang->list['messages'][$status[0]][$success])){
						$text = $this->lang->list['messages'][$status[0]][$success];
					}
					else{
						$text = '';
					}

					if($text){
						$status = array($text, $status[1] ? 'success': 'failure');
					}
				}
			}		

			$this->view->set('message', $status);

		}
		
		function schema(){
			
			$this->helper->filter->defaults($this->args, array(
				'module'=> '',
				'tables' => array()
			));
			
			$this->view->setArray($this->args);
			
		}

	}
