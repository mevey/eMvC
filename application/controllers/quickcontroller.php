<?php
class QuickController extends BaseController {
	
	public function _beforeAction() {
		$department = new department();
		$this->template->dept_data = $department::select('department','*');
		$asset_category = new asset_category();
		$this->template->asset_category_data = $asset_category::select('asset_category','*');
		$group = new group();
		$this->template->user_category_data = $group::select('group','*');
	}
	
	public function _afterAction() {}
	
	public function index_getAction() {}
	
	public function index_postAction() {}
		
	public function help_getAction() {}
	
	public function _404Handler() {
		echo 'Sorry, could not load that resource.';
	}
	
	protected function checkRole($roleName) {
		if (!Registry::checkRole($roleName)) {
			echo '<font style="color:red">Access Denied. Permission required for '.$roleName.'</font>';
			exit;
		}
		
	}
//---------------------------------------------------------------------
//	ASSET
//---------------------------------------------------------------------
	public function edit_asset_getAction() {
			$this->edit_asset_postAction();
	}

	public function edit_dept_asset_getAction() {
			$this->edit_dept_asset_postAction();
	}

	public function edit_asset_postAction() {
		$this->checkRole(Roles::EDIT_ASSET);
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$asset = new asset($_GET['id']);
			
			$this->template->media = db::select('asset_media','*','asset_media_fk_asset_id = '.$_GET['id']);
			$this->template->data = $asset;
			
			$asset_category = new asset_category($asset->asset_fk_cat_id);
			$this->template->category_data = $asset_category;
			
			$this->template->history = db::select('log','*','log_to_asset_id='.$_GET['id'],'log_id DESC');
			
			$this->template->load('ajax/blog/quickedit.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function edit_dept_asset_postAction() {
		$this->checkRole(Roles::EDIT_DEPARTMENT_ASSET);
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$asset = new asset($_GET['id']);
			
			$this->template->media = db::select('asset_media','*','asset_media_fk_asset_id = '.$_GET['id']);
			
			$this->template->data = $asset;
			$this->template->history = db::select('log','*','log_to_asset_id='.$_GET['id'],'log_id DESC');
			$this->template->load('ajax/blog/editdeptasset.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function view_asset_getAction() {
		$this->view_asset_postAction();
	}
	
	public function view_asset_postAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			
			$asset = new asset($_GET['id']);
			
			$this->template->media = db::select('asset_media','*','asset_media_fk_asset_id = '.$_GET['id']);
			
			$asset_category = new asset_category($asset->asset_fk_cat_id);
			$this->template->category_name = $asset_category->asset_category_name;
			
			$this->template->data = $asset;
			$this->template->history = db::select('log','*','log_to_asset_id='.$_GET['id'],'log_id DESC');
			
			$this->template->load('ajax/blog/viewdeptasset.html');
			
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function update_asset_postAction() {
		try {
			if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
				throw new exception();
			}
			
			$asset = new asset($_POST['id']);
			$asset->asset_tag_id = $_POST['tag_id'];
			$asset->asset_model = $_POST['model'];
			$asset->asset_fk_cat_id = $_POST['category'];
			$asset->asset_cost = $_POST['cost'];
			$asset->asset_supply_date = $_POST['date'];
			$asset->asset_description = $_POST['description'];
			$asset->asset_supplier = $_POST['supplier'];
			
			$asset_category = new asset_category('asset_category_id = '.$_POST['category']);
			$cat_meta = $asset_category->asset_category_meta_fields;
			
			$cat_attribs = json_decode($cat_meta, true);
			foreach($cat_attribs as $key => $attrib) {
				$post_key = 'attrib_'.strtolower(str_replace(' ','_',$attrib['attrib_name']));
				$attrib['attrib_value'] = empty($_POST[$post_key]) ? '' : $_POST[$post_key];
				$cat_attribs[$key] = $attrib;
			}
			$asset->asset_meta_field_data = json_encode($cat_attribs);
			
			$asset->save();
			
			if (!isset($_FILES['asset_img']) || empty($_FILES['asset_img']) ||  $_FILES['asset_img']['size'] == 0) {
				
			} else {
				$f = str_replace(' ', '', microtime());
				$allowedExts = array('jpg', 'jpeg', 'gif', 'png');
				$extension = end(explode('.', $_FILES['asset_img']['name']));
				if ((($_FILES['asset_img']['type'] == 'image/gif')
				|| ($_FILES['asset_img']['type'] == 'image/jpeg')
				|| ($_FILES['asset_img']['type'] == 'image/png')
				|| ($_FILES['asset_img']['type'] == 'image/pjpeg'))
				&& ($_FILES['asset_img']['size'] < 2000000)
				&& in_array($extension, $allowedExts)) {
					if ($_FILES['asset_img']['error'] > 0) {
						echo 'Error: ' . $_FILES['asset_img']['error'] . '<br>';
						exit;
					} else {
						move_uploaded_file($_FILES['asset_img']['tmp_name'],
						SITE_PATH.'application/uploads/asset_images/' . $f.'.'.$extension);
					}
				} else {
					$this->setErrorMessage('You have entered an invalid image file.');
					$this->redirect('/asset');
					return;
				}
			}
			if (isset($_POST['imagesarr'])) {
				$arr = array();
				$arr = explode(',',$_POST['imagesarr']);
				foreach ($arr as $a){
					if($a == '') continue;
					try {
					$asset_media = new asset_media($a);
					$asset_media->deleteMe();
					} catch(Exception $err) {
						
					} 
				}
			}
			$asset = new asset('asset_tag_id ="'.$_POST['tag_id'].'"');
			$newAssetData = $asset->getProperties();
			
			$asset_media = new asset_media();
			$asset_media->asset_media_fk_asset_id = $newAssetData['asset_id'];
			if (isset($_FILES)) {
				$asset_media->asset_media_url_path = 'application/uploads/asset_images/'.$f.'.'.$extension ;;
			}
			$asset_media->save();
			
			$this->setSuccessMessage('The asset has been updated successfully.');
			$this->redirect('asset');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function update_dept_asset_postAction() {
		try {
			if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
				throw new exception();
			}
			
			$asset = new asset($_POST['id']);
			$asset->asset_tag_id = $_POST['tag_id'];
			$asset->asset_model = $_POST['model'];
			$asset->asset_fk_cat_id = $_POST['category'];
			$asset->asset_cost = $_POST['cost'];
			$asset->asset_supply_date = $_POST['date'];
			$asset->save();
			
			$f = str_replace(' ', '', microtime());
			$allowedExts = array('jpg', 'jpeg', 'gif', 'png');
			$extension = end(explode('.', $_FILES['asset_img']['name']));
			if ((($_FILES['asset_img']['type'] == 'image/gif')
			|| ($_FILES['asset_img']['type'] == 'image/jpeg')
			|| ($_FILES['asset_img']['type'] == 'image/png')
			|| ($_FILES['asset_img']['type'] == 'image/pjpeg'))
			&& ($_FILES['asset_img']['size'] < 2000000)
			&& in_array($extension, $allowedExts)) {
				if ($_FILES['asset_img']['error'] > 0) {
					echo 'Error: ' . $_FILES['asset_img']['error'] . '<br>';
					exit;
				} else {
					move_uploaded_file($_FILES['asset_img']['tmp_name'],
					SITE_PATH.'application/uploads/asset_images/' . $f.'.'.$extension);
				}
			} else {
				echo 'Invalid file';
				exit;
			}
			
			if (isset($_POST['imagesarr'])) {
				$arr = array();
				$arr = explode(',',$_POST['imagesarr']);
				foreach ($arr as $a){
					if($a == '') continue;
					try {
					$asset_media = new asset_media($a);
					$asset_media->deleteMe();
					} catch(Exception $err) {
						
					} 
				}
			}
			
			$asset = new asset('asset_tag_id ="'.$_POST['tag_id'].'"');
			$newAssetData = $asset->getProperties();
			
			$asset_media = new asset_media();
			$asset_media->asset_media_fk_asset_id = $newAssetData['asset_id'];
			if (isset($_FILES)) {
				$asset_media->asset_media_url_path = 'application/uploads/asset_images/'.$f.'.'.$extension ;
			}
			
			$asset_media->save();
			
			$this->setSuccessMessage('The asset has been updated successfully.');
			$this->redirect('departmentasset');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
		
	public function delete_asset_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			
			$asset = new asset($_GET['id']);
			$asset_category = new asset_category($asset->asset_fk_cat_id);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
			$log->log_description = Notifications::delete_asset($asset->asset_tag_id,
			$asset->asset_model,$asset_category->asset_category_name);
			$log->save();
			
			$asset->deleteMe();
			
			$this->setSuccessMessage('The asset has been deleted successfully.');
			$this->redirect('asset');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function delete_dept_asset_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			
			$asset = new asset($_GET['id']);
			$asset_category = new asset_category($asset->asset_fk_cat_id);
			$department = new department(Auth::getLoggedInUserData('user_fk_dept_id'));
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
			$log->log_description = Notifications::delete_dept_asset($asset->asset_tag_id,
			$asset->asset_model,$asset_category->asset_category_name, $department->department_name);
			$log->save();
			
			$asset->deleteMe();
			
			$this->setSuccessMessage('The asset has been deleted successfully.');
			$this->redirect('departmentasset');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function dispose_asset_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			
			$asset = new asset($_GET['id']);
			$asset->asset_state = -1;
			
			$asset_category = new asset_category($asset->asset_fk_cat_id);
			$department = new department(Auth::getLoggedInUserData('user_fk_dept_id'));
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
			$log->log_description = Notifications::dispose_asset($asset->asset_tag_id,
			$asset->asset_model, Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername'));
			$log->save();
			
			$asset->save();
			
			$this->setSuccessMessage('The asset has been set for disposal successfully.');
			$this->redirect('asset');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function dispose_dept_asset_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			
			$asset = new asset($_GET['id']);
			$asset->asset_state = -1;
			
			$asset_category = new asset_category($asset->asset_fk_cat_id);
			$department = new department(Auth::getLoggedInUserData('user_fk_dept_id'));
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
			$log->log_description = Notifications::dispose_asset($asset->asset_tag_id,
			$asset->asset_model, Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername'));
			$log->save();
			
			$asset->save();
			
			$this->setSuccessMessage('The asset has set for disposal successfully.');
			$this->redirect('departmentasset');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	


//---------------------------------------------------------------------
//	USER
//---------------------------------------------------------------------
	public function edit_user_getAction() {
		$this->edit_user_postAction();
	}
	
	public function edit_user_postAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$user = new user($_GET['id']);
			$this->template->data = $user;
			$this->template->load('ajax/blog/useredit.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function update_user_postAction() {
		try {
			if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
				throw new exception();
			}
			
			$user = new user($_POST['id']);
			$user->user_surname = $_POST['surname'];
			$user->user_othername = $_POST['othername'];
			$user->user_employment_number = $_POST['emp_number'];
			$user->user_employment_date = $_POST['employment_date'];
			$user->user_role = $_POST['role'];
			$user->user_office = $_POST['office'];
			$user->user_fk_dept_id = $_POST['dept'];
			$user->user_fk_cat_id = $_POST['category'];
			$user->save();
			
			$this->setSuccessMessage('The user\'s information has been updated successfuly.');
			$this->redirect('user');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function delete_user_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			
			$user = new user($_GET['id']);
			$user->deleteMe();
			
			$this->setSuccessMessage('The user has been deleted successfully.');
			$this->redirect('user');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
//---------------------------------------------------------------------
//	ASSET CATEGORY
//---------------------------------------------------------------------
	public function edit_asset_category_getAction() {
		$this->edit_asset_category_postAction();
	}
	
	public function edit_asset_category_postAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$asset_category = new asset_category($_GET['id']);
			$this->template->data = $asset_category;
			
			$this->template->load('ajax/blog/catedit.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function update_asset_category_postAction() {
		try {
			if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
				throw new exception();
			}
			$attribLimit = (int) $_POST['attrib_count'];
		
			$attribArray = array();
			for ($i = 1; $i <= $attribLimit; $i++) {
				if (!isset($_POST['attrib_name_'.$i])) continue;
				
				$attrib_config = explode(',', $_POST['attrib_config_'.$i]);
				
				
				$attribArray[] = array(
						'attrib_name' => $_POST['attrib_name_'.$i],
						'attrib_type' => $_POST['attrib_type_'.$i],
						'attrib_required' => (!empty($_POST['attrib_required_'.$i])) ? 1 : 0,
						'attrib_config' => explode(',', $_POST['attrib_config_'.$i])
						);
			}
			
			$attrib_array_data = json_encode($attribArray);
			
			//print_r($attrib_array_data);exit;
			
			$asset_category = new asset_category($_POST['id']);
			$asset_category->asset_category_name = $_POST['name'];
			$asset_category->asset_category_description = $_POST['description'];
			$asset_category->asset_category_depreciation_rate = $_POST['dep_rate'];
			$asset_category->asset_category_meta_fields = $attrib_array_data;
			$asset_category->asset_category_retire_age = $_POST['retire'];

			$asset_category->save();
			
			$this->setSuccessMessage('The asset category information has been updated successfully.');
			$this->redirect('category');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function delete_asset_category_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			
			$asset_category = new asset_category($_GET['id']);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_dept_id = 5;
			$log->log_description = Notifications::delete_asset_category($asset_category->asset_category_name);
			$log->save();
			
			$asset_category->deleteMe();
			
			$this->setSuccessMessage('The asset category has been deleted successfully.');
			$this->redirect('category');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
//------------------------------------------------------------------
//REQUESTS
//------------------------------------------------------------------
	public function read_employee_request_postAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$request = new request($_GET['id']);
			
			$this->template->request_data = $request;
			$user = new user('user_id = '.$this->template->request_data->request_fk_user_id);
			$this->template->user_data = $user;
			$asset_category = new asset_category('asset_category_id = '.$this->template->request_data->request_fk_asset_cat_id);
			$this->template->cat_data = $asset_category;
			
			$request->request_read = 1;
			$request->save();
			
			$this->template->load('ajax/blog/reademployeerequest.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function read_department_request_postAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$request = new request($_GET['id']);
			
			$this->template->request_data = $request;
			$user = new user('user_id = '.$this->template->request_data->request_fk_user_id);
			$this->template->user_data = $user;
			$asset_category = new asset_category('asset_category_id = '.$this->template->request_data->request_fk_asset_cat_id);
			$this->template->cat_data = $asset_category;
			
			$request->request_read = 1;
			$request->save();
			
			$this->template->load('ajax/blog/readdepartmentrequest.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			$this->template->load('departmentrequest');
		}
	}
	
	public function read_outgoing_request_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$request = new request($_GET['id']);
			$this->template->request_data = $request;
			$user = new user('user_id = '.$this->template->request_data->request_fk_user_id);
			$this->template->user_data = $user;
			$asset_category = new asset_category('asset_category_id = '.$this->template->request_data->request_fk_asset_cat_id);
			$this->template->cat_data = $asset_category;
			$this->template->load('ajax/blog/readoutgoingrequest.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function read_outgoing_request_postAction() {
		$this->read_outgoing_request_getAction();
	}
	
	public function read_request_postAction() {
		$this->read_request_getAction();
	}
	
	public function update_request_postAction() {
		try {
			if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
				throw new exception();
			}
			
			$request = new request($_POST['id']);
			$request->request_hod_approve = '1';
			$request->request_hod_remarks = $_POST['remarks'];

			$request->save();
			
			$this->setSuccessMessage('Your remarks have been posted successfully.');
			$this->redirect('request');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
//-----------------------------------------------------------------------
//Groups
//----------------------------------------------------------------------

	public function edit_group_postAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$group= new group($_GET['id']);
			$this->template->data = $group;
			$this->template->load('ajax/blog/groupedit.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}

	public function update_group_postAction() {
		try {
			if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
				throw new exception();
			}
			
			$group = new group($_POST['id']);
			$group->group_name = $_POST['name'];;

			$group->save();
			
			$this->setSuccessMessage('The group information has been updated successfully.');
			$this->redirect('group');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
//----------------------------------------------------------------------
//MAINTENANCE
//----------------------------------------------------------------------
	public function view_repair_problem_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}

			$p = Registry::getInstance()->config['database']['db_prefix'];
			$t = $p.'maintenance as A INNER JOIN '.$p.'asset as B ON A.maintenance_fk_asset_id = B.asset_id INNER JOIN '.$p.'user AS C ON A.maintenance_fk_user_id = C.user_id INNER JOIN '.$p.'department AS D ON A.maintenance_fk_dept_id = D.department_id INNER JOIN '.$p.'asset_category as E ON A.maintenance_fk_cat_id= E.asset_category_id';
			
			$dt = db::select($t,'*','maintenance_id='.$_GET['id'],'','','');
			$this->template->maintenance_data =$dt;
			
			$this->template->past_repairs = array();
			if (!empty($dt[0]['maintenance_fk_asset_id'])) {
				$a = $dt[0]['maintenance_fk_asset_id'];
				$this->template->past_repairs = db::select($t,'*','maintenance_fk_asset_id='.$a.' AND maintenance_id <>'.$_GET['id'],'','','');
			}
			
			$this->template->load('ajax/blog/viewmaintenance.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function view_repair_problem_postAction() {
		$this->view_repair_problem_getAction();
	}
	
	public function view_maintenance_report_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			
			$p = Registry::getInstance()->config['database']['db_prefix'];
			$t = $p.'maintenance as A INNER JOIN '.$p.'asset as B ON A.maintenance_fk_asset_id = B.asset_id INNER JOIN '.$p.'user AS C ON A.maintenance_fk_user_id = C.user_id INNER JOIN '.$p.'department AS D ON A.maintenance_fk_dept_id = D.department_id INNER JOIN '.$p.'asset_category as E ON A.maintenance_fk_cat_id= E.asset_category_id';
			
			$dt = db::select($t,'*','maintenance_id='.$_GET['id'],'','','');
			
			$this->template->maintenance_data = $dt;
			
			$this->template->past_repairs = array();
			if (!empty($dt[0]['maintenance_fk_asset_id'])) {
				$a = $dt[0]['maintenance_fk_asset_id'];
				$this->template->past_repairs = db::select($t,'*','maintenance_fk_asset_id='.$a,'','','');
			}
			
			$this->template->load('ajax/blog/viewreport.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
		
	}
	public function view_maintenance_report_postAction() {
		$this->view_maintenance_report_getAction();
	}
}





