<?php
class MyProfileController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('myprofile');
		$this->template->user_id = Auth::getLoggedInUserId();
	}
	
	public function _afterAction() {
		
	}
	
	public function index_getAction() {
		if (Auth::isLoggedIn()){
			try{
				$id = Auth::getLoggedInUserId();
				$p = Registry::getInstance()->config['database']['db_prefix'];
				
				$user = new user($id);
				$this->template->user_data = $user->getProperties();
				
				$group = new group($user->user_fk_cat_id);
				$this->template->group_data = $group->getProperties();
				
				$department = new department($user->user_fk_dept_id);
				$this->template->dept_data = $department->getProperties();
				
				$this->template->load('myprofile.html');
				
				} catch(Exception $err) {
					$this->setErrorMessage('Sorry, something went wrong. Please try again.');
					return;
				}
			} else {
				$this->template->load('index.html');
			}
	}
	
	public function index_postAction() {
		$this->template->load('myprofile.html');
	}
	
	public function edit_profile_getAction() {
		try{
			$id = Auth::getLoggedInUserId();
			$p = Registry::getInstance()->config['database']['db_prefix'];
			
			$user = new user($id);
			$this->template->user_data = $user->getProperties();
			
			$group = new group($user->user_fk_cat_id);
			$this->template->group_data = $group->getProperties();
			
			$department = new department($user->user_fk_dept_id);
			$this->template->dept_data = $department->getProperties();
			
			$this->template->load('myprofile_edit.html');
			
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function update_postAction() {
		try{
			$id = Auth::getLoggedInUserId();
			$user = new user($id);
			$user->user_surname = $_POST['surname'];
			$user->user_othername = $_POST['othername'];
			$user->user_email = $_POST['email'];
			$user->user_username = $_POST['username'];
			$user->user_id_number = $_POST['id_number'];
			$user->user_phone_number= $_POST['phone_number'];
			$user->user_office= $_POST['office'];
			$user->user_role= $_POST['role'];
			$user->save();
			
			if (isset($_FILES['user_img']) && !empty($_FILES['user_img'])) {
				//print_r($_FILES['user_img']);exit;
				$f = str_replace(' ', '', microtime());
				$allowedExts = array('jpg', 'jpeg', 'gif', 'png');
				$extension = end(explode('.', $_FILES['user_img']['name']));
				if ((($_FILES['user_img']['type'] == 'image/gif')
				|| ($_FILES['user_img']['type'] == 'image/jpeg')
				|| ($_FILES['user_img']['type'] == 'image/png')
				|| ($_FILES['user_img']['type'] == 'image/pjpeg'))
				&& ($_FILES['user_img']['size'] < 2000000)
				&& in_array($extension, $allowedExts)) {
					if ($_FILES['user_img']['error'] > 0) {
						echo 'Error: ' . $_FILES['user_img']['error'] . '<br>';
						exit;
					} else {
						move_uploaded_file($_FILES['user_img']['tmp_name'],
						SITE_PATH.'application/uploads/user_images/' . $f.'.'.$extension);
						try {
							$user_media = new user_media($id);
							$user_media->user_media_url_path = 'application/uploads/user_images/'.$f.'.'.$extension ;
							$user_media->save();
						} catch(Exception $err) {
							$user_media = new user_media();
							$user_media->user_media_fk_user_id = $id;
							$user_media->user_media_url_path = 'application/uploads/user_images/'.$f.'.'.$extension ;
							$user_media->save();
						}
					}
					
				}
			}
			
			$this->setSuccessMessage('Your profile has been updated successfully.');
			$this->redirect('myprofile');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			$this->redirect('myprofile');
		}
	}
	
	public function update_getAction() {
		try{
			$id = Auth::getLoggedInUserId();
			$user = new user($id);
			$this->template->user_data = $user->getProperties();
			
			$this->template->load('myprofile_edit.html');
		} catch(Exception $err) {
			$this->redirect('myprofile');
			return;
		}
	}
	
	public function log_out_getAction() {
		$user = new user(Auth::getLoggedInUserId());
		$user->user_last_login = Registry::getDate();
		$user->save();
		
		Auth::setLoggedOut();
		$this->template->load('index.html');
	}
	
	public function help_getAction() {
		
	}
	
	public function change_password_getAction() {
		$this->template->flag = 0;
		$this->template->load('change_password.html');
	}
	
	public function new_password_getAction() {
		$this->template->flag = 1;
		$this->template->load('change_password.html');
	}
	
	public function change_password_postAction() {
		$this->change_password_getAction();
	}
	
	public function get_old_password_getAction() {
		$this->change_password_getAction();
	}
	
	public function get_old_password_postAction() {
		try {
			$user = new user('user_id = '.Auth::getLoggedInUserId().' AND user_password = "'.sha1($_POST['old_password']).'"');
			$this->template->flag = 1;
			$this->setInfoMessage('You can now change your password.');
			$this->redirect('myprofile/new_password');
			//$this->template->load('change_password.html');
		} catch (Exception $err) {
			$this->template->flag = 0;
			$this->setErrorMessage('Sorry, the password entered is incorrect.');
			$this->redirect('myprofile/change_password');
		}
	}
	
	public function new_password_postAction() {
		try{
			$user = new user(Auth::getLoggedInUserId());
			$this->template->flag = 1;
			if ($_POST['new_password'] != $_POST['confirm_password']) {
				$this->setErrorMessage('The password entered does not match.');
				$this->template->load('change_password.html');
				return;
			}
			if (strlen($_POST['new_password']) < 8 ){
				$this->setErrorMessage('You password needs to be 8 or more characters');
				$this->template->load('change_password.html');
				return;
			}
			$user->user_password = sha1($_POST['confirm_password']);
			$user->save();
			
			$this->setSuccessMessage('Your password has been changed successfully.');
			$this->template->load('change_password.html');
		} catch (Exception $err) {
			$this->template->flag = 0;
			$this->setErrorMessage('Sorry, the password entered is incorrect.');
			$this->redirect('myprofile/change_password');
		}
	}
	
}







