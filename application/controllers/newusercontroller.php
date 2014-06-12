<?php
class NewUSerController extends BaseController {
	public $requireLogin = false;
	
	public function _beforeAction() {
	}
	
	public function _afterAction() {
		
	}
	
	public function index_getAction() {
		$this->template->load('newuser.html');
	}
	
	public function index_postAction() {
		$user = null;
		try {
			$user = new user('user_employment_number="'.$_POST['employment_number'].'"');
		} catch (Exception $err) {
			$this->setSession('error_msg', 'Sorry, your details do not match any on the system.');
			$this->redirect('newuser');
		}
		try{ 
			$user->user_email = $_POST['email'];
			$user->user_username = $_POST['username'];
			$user->user_id_number = $_POST['id_number'];
			$user->user_password= sha1($_POST['password']);
			$user->user_phone_number= $_POST['phone_number'];
			$user->user_office= $_POST['office'];
			$user->save();
			
			$this->setSuccessMessage('Your information has been saved successfully.');
			$this->template->load('index.html');
		} catch (Exception $err) {
			$this->setErrorMessage('Sorry, your details do not match any on the system.');
			$this->redirect('newuser');
		}
	}
	
	public function help_getAction() {
		
	}
	
}





