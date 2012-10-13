<?php

	class AdminLang extends Lang{

		public $admin = 'Content Manager';
		public $content = "Content";
		public $home = 'Home';

		public $error = 'An unknown error has occurred!';
		
		public $settings = 'Settings';
		public $logout = "Logout";
		public $visit = 'View Site';

		public $change_password = "Change Password";
		public $old_password = 'Current Password';
		public $new_password = 'New Password';
		public $retype_password = 'Retype Password';
		public $password_change_notice = "Change the password below for the user";
		public $password_invalid = 'Password is not correct';
		public $password_too_short = 'Password must have at least 6 characters';
		public $password_no_match = 'Password does not match';

		public $settings_msg = array('pass_change' => array('You password has been changed succesfully.',
															'There was a problem changing your password.'));

		public $select = '---- Select ----';

		public $list = array(
			'title' => 'Item List',
			'empty' => "There are no items in the list",
			'operations' => array(
				'add'	=> 'Add',
				'edit'	=> 'Edit',
				'delete'=> 'Delete',
				'explore' => 'Visit',
				'order' => 'Order'
			),
			'messages' => array(
				 'add'		=> array("The item has been added", 'There was an error adding the item'),
				 'edit'		=> array("The item has been updated", 'There was an error updating the item'),
				 'delete'	=> array("The item has been deleted", 'There was an error deleting the item')
			),
			'confirm' => array("Are you sure you want to delete", "and all the related information?")
		);

		public $gallery = array(
			'title' => 'Gallery Pictures',
			'empty' => "There are no pictures in the gallery",
			'operations' => array(
				'add'	=> 'Add',
				'edit'	=> 'Edit',
				'delete'=> 'Delete',
				'explore' => 'Visit'
			),
			'messages' => array(
				 'add'		=> array('success' => "The picture has been added",
									 'failure' => 'There was an error adding the picture'),
				 'edit'		=> array('success' => "The picture has been updated",
									 'failure' => 'There was an error updating the picture'),
				 'delete'	=> array('success' => "The picture has been deleted",
									 'failure' => 'There was an error deleting the picture')
			)
		);

		public $viewer = array(
			'boolean' => array('No', 'Yes')
		);

		public $table = array(
			'title' => 'Item List',
			'empty' => "No items found.",
			'operations' => array(
				'add'	=> 'Add',
				'edit'	=> 'Edit',
				'delete'=> 'Delete',
				'explore' => 'Visit',
				'order' => 'Order'
			),
			'messages' => array(
				 'add'		=> array("The item has been added", 'There was an error adding the item'),
				 'edit'		=> array("The item has been updated", 'There was an error updating the item'),
				 'delete'	=> array("The item has been deleted", 'There was an error deleting the item')
			),
			'confirm' => array("Are you sure you want to delete ", "and all the related information?")
		);
		
		public $summary = array(
			'all' => 'See All'
		);

	}
