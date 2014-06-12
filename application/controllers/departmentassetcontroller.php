<?php
class DepartmentAssetController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('dept_asset');
		
		if (isset($_GET['page_number'])) {
			$this->setSession('page_number',$_GET['page_number']);
		}
		if (isset($_GET['items_per_page'])) {
			$this->setSession('items_per_page',$_GET['items_per_page']);
		}
		if (isset($_GET['search'])) {
			$this->setSession('search',$_GET['search']);
		}
		if (isset($_GET['order_by'])) {
			if ($this->getSession('order_by') == $_GET['order_by']) {
				if ( $this->getSession('order_direction') == 'ASC') {
					$this->setSession('order_direction','DESC');
				} else {
					$this->setSession('order_direction','ASC');
				}
			}
			$this->setSession('order_by',$_GET['order_by']);
		}
		if (isset($_GET['order_direction'])) {
			$this->setSession('order_direction',$_GET['order_direction']);
		}
		if (isset($_GET['filter'])) {
			$this->setSession('filter',$_GET['filter']);
		}
	}
	
	private function searchClause() {
		$search = $this->getSession('search');
		$f = $this->getSession('filter');
		$cat_filter = $f != '' && is_numeric($f) ? ' AND asset_fk_cat_id = '.$this->getSession('filter') : '';
		if (empty($search)) {
			return 'asset_state <> -1 AND asset_state <> 2 '.$cat_filter;
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		return ' asset_state <> -1 AND asset_state <> 2 AND (asset_model LIKE '.$search.' OR asset_tag_id LIKE '.$search.' OR asset_fk_cat_id = '.$search.')'.$cat_filter;
	}
	
	private function orderClause() {
		$order_by = $this->getSession('order_by');
		$order_direction = strtoupper($this->getSession('order_direction'));
		if (empty($order_by)) {
			return '';
		} else if (!empty($order_direction) && ($order_direction == 'ASC' || $order_direction == 'DESC')){
			$order_by = $order_by.' '.$order_direction;
		}
		
		return $order_by;
	}
	
	public function _afterAction() {
		
	}
	
	public function index_getAction() {
		$user_dept = Auth::getLoggedInUserData('user_fk_dept_id');
		
		$department = new department('department_id ='.$user_dept);
		$this->template->dept_name = $department->department_name;
		$this->template->dept_id = $department->department_id;
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'asset INNER JOIN '.$p.'allocation ON '.$p.'asset.asset_id = '.$p.'allocation.allocation_fk_asset_id';
		try {
			$allocation = new allocation('allocation_fk_dept_id='. $user_dept);
			$this->template->list_data = $allocation->getRows($t,'*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		} catch (Exception $err) {
			$this->template->allocation_err = 'Your department has not been assigned any assets';
		}
		
		$this->template->cat_data = db::select('asset_category','*');
		
		$returned_asset = new returned_asset();
		$id = Auth::getLoggedInUserData('user_fk_dept_id');
		$t = $p.'asset INNER JOIN '.$p.'returned_asset ON '.$p.'asset.asset_id = '.$p.'returned_asset.returned_asset_asset_id';
		$this->template->count = $returned_asset->getRowCount($t,'returned_asset_dept_id = '.$id);
		
		$this->template->category_data = db::select('asset_category','*','','','');
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		$this->template->filter = $this->getSession('filter');
		
		$this->template->load('department_asset.html');
	}
	
	public function index_postAction() {
		$this->template->load('department_asset.html');
	}
	
	public function add_asset_getAction() {
		$user_dept = Auth::getLoggedInUserData('user_fk_dept_id');
		$department = new department('department_id='.$user_dept);
		$this->template->dept_data = $department->getProperties();
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$returned_asset = new returned_asset();
		$id = Auth::getLoggedInUserData('user_fk_dept_id');
		$t = $p.'asset INNER JOIN '.$p.'returned_asset ON '.$p.'asset.asset_id = '.$p.'returned_asset.returned_asset_asset_id';
		$this->template->count = $returned_asset->getRowCount($t,'returned_asset_dept_id = '.$id);
		
		$category = new category();
		$this->template->category_data = $category::select('asset_category','*');
		
		$this->template->load('department_asset_add.html');
	}
	
	public function add_asset_postAction() {
		$asset = new asset();
		
		for($i = 1; $i <= $_POST['count']; $i++) {
			if (empty($_FILES['asset_img_'.$i]['name'])) continue;
			$f = str_replace(' ', '', microtime());
			$allowedExts = array('jpg', 'jpeg', 'gif', 'png');
			$extension = end(explode('.', $_FILES['asset_img_'.$i]['name']));
			if ((($_FILES['asset_img_'.$i]['type'] == 'image/gif')
			|| ($_FILES['asset_img_'.$i]['type'] == 'image/jpeg')
			|| ($_FILES['asset_img_'.$i]['type'] == 'image/png')
			|| ($_FILES['asset_img_'.$i]['type'] == 'image/pjpeg'))
			&& ($_FILES['asset_img_'.$i]['size'] < 2000000)
			&& in_array($extension, $allowedExts)) {
				if ($_FILES['asset_img_'.$i]['error'] > 0) {
					echo 'Error: ' . $_FILES['asset_img_'.$i]['error'] . '<br>';
					exit;
				} else {
					move_uploaded_file($_FILES['asset_img_'.$i]['tmp_name'],
					SITE_PATH.'application/uploads/asset_images/' . $f.'.'.$extension);
					
					$asset_media = new asset_media();
					$asset_media->asset_media_fk_asset_id = $id;
					$asset_media->asset_media_url_path = 'application/uploads/asset_images/'.$f.'.'.$extension;

					$asset_media->save();
				}
			} else {
				$this->setErrorMessage('You have entered an invalid image file.');
				$this->redirect('/asset/add');
				return;
			}
		}
		
		$asset->asset_tag_id = $_POST['tag_id'];
		$asset->asset_model = $_POST['model'];
		$asset->asset_fk_cat_id = $_POST['category'];
		$asset->asset_cost = $_POST['cost'];
		$asset->asset_supply_date = $_POST['date'];
		$asset->asset_add_date = Registry::getDate();
		$asset->asset_description = $_POST['description'];
		$asset->asset_supplier= $_POST['supplier'];
		
		$asset_category = new asset_category('asset_category_id = '.$_POST['category']);
		$cat_meta = $asset_category->asset_category_meta_fields;
		
		$cat_attribs = json_decode($cat_meta, true);
		
		foreach($cat_attribs as $key => $attrib) {
			$post_key = 'attrib_'.$attrib['attrib_name'];
			$attrib['attrib_value'] = empty($_POST[$post_key]) ? '' : $_POST[$post_key];
			$cat_attribs[$key] = $attrib;
		}
		
		$asset->asset_meta_field_data = json_encode($cat_attribs);
		
		$asset->save();
		
		$asset_category = new asset_category('asset_category_id = '.$_POST['category']);
		$department = new department('department_id = '.Auth::getLoggedInUserData('user_fk_dept_id'));
		
		$asset = new asset('asset_tag_id ="'.$_POST['tag_id'].'"');
		$newAssetData = $asset->getProperties();
		
		$asset_media = new asset_media();
		$asset_media->asset_media_fk_asset_id = $newAssetData['asset_id'];
		$asset_media->asset_media_url_path = 'application/uploads/asset_images/'.$f.'.'.$extension ;;
		
		$asset_media->save();
		
		$allocation = new allocation();
		$allocation->allocation_fk_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
		$allocation->allocation_fk_asset_id = $newAssetData['asset_id'];
		
		$allocation->save();
		
		$log = new log();
		$log->log_date = Registry::getDate();
		$log->log_by_user_id = Auth::getLoggedInUserId();
		$log->log_to_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
		$log->log_description = Notifications::add_dept_asset($newAssetData['asset_id'],$newAssetData['asset_tag_id'],
		$newAssetData['asset_model'],$asset_category->asset_category_name, $department->department_name);
		$log->log_asset_description = Notifications::asset_add_dept_asset($asset_category->asset_category_name, $department->department_name);
		$log->save();
		
		$this->setSuccessMessage('The asset has successfully been added.');
		$this->redirect('departmentasset');
	}
	
	public function assign_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$user_dept = Auth::getLoggedInUserData('user_fk_dept_id');
			$asset = new asset($_GET['id']);
			
			$this->template->asset_data = $asset->getProperties();
			
			$department = new department('department_id='.$user_dept);
			$this->template->dept_data = $department->getProperties();
			
			$this->template->media = db::select('asset_media','*','asset_media_fk_asset_id='.$_GET['id']);
		
			$allocation = new allocation('allocation_fk_asset_id='.$_GET['id']);
			$this->template->allocation_data = $allocation->getProperties();
			
			if ($this->template->allocation_data['allocation_fk_user_id'] == 0){
				$this->template->allocated_user_data  = 'Asset not allocated to anyone';
			} else { 
				$user = new user('user_id='.$this->template->allocation_data['allocation_fk_user_id']);
				$this->template->allocated_user_data = $user->getProperties();
				$user_media = new user_media('user_media_fk_user_id='.$this->template->allocation_data['allocation_fk_user_id']);
				$this->template->user_img = $user_media->user_media_url_path;
			}
			
			
			$returned_asset = new returned_asset();
			$id = Auth::getLoggedInUserData('user_fk_dept_id');
			$p = Registry::getInstance()->config['database']['db_prefix'];
			$t = $p.'asset INNER JOIN '.$p.'returned_asset ON '.$p.'asset.asset_id = '.$p.'returned_asset.returned_asset_asset_id';
			$this->template->count = $returned_asset->getRowCount($t,' returned_asset_dept_id = '.$id);
			
			$this->template->users_data = db::select('user','*','user_fk_dept_id='.$user_dept,'','');
			
			$asset_category = new asset_category('asset_category_id='.$this->template->asset_data['asset_fk_cat_id']);
			$this->template->category_data = $asset_category->getProperties();
			
			$this->template->load('department_assign.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			$this->template->load('department_assign.html');
			return;
		}
		
	}
	
	public function assign_user_postAction() {
		$dateTime = Registry::getDate();
		$allocation = new allocation('allocation_fk_asset_id='.$_POST['asset_id']);
		
		$allocation->allocation_fk_user_id = $_POST['user_id'];
		$allocation->allocation_date = $dateTime;
		
		$allocation->save();
		
		$asset = new asset('asset_id = '.$_POST['asset_id']);
		
		$user = new user($_POST['user_id']);
		
		$log = new log();
		$log->log_date = Registry::getDate();
		$log->log_by_user_id = Auth::getLoggedInUserId();
		$log->log_to_user_id = $_POST['user_id'];
		$log->log_description = Notifications::assign_user($asset->asset_model,$asset->asset_tag_id, $_POST['asset_id'],
		Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername'), Auth::getLoggedInUserData('user_role'));
		$log->log_asset_description = Notifications::asset_assign_user(Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername'),
		Auth::getLoggedInUserData('user_role'),$user->user_surname.' '.$user->user_othername);
		$log->save();
		
		$this->setSuccessMessage('You have successfully assigned that asset.');
		$this->redirect('departmentasset/assign?id='.$_POST['asset_id']);
	}
	
	public function help_getAction() {
		
	}
	
	public function return_asset_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$d = db::getRowCount('allocation','allocation_fk_asset_id = '.$_GET['id'].' AND allocation_fk_user_id <> 0');
			if ($d > 0) {
				$this->setInfoMessage('This asset is currently assigned to an employee in your department. Please remove the assignement before returning the asset.');
				$this->redirect('departmentasset');
			}
			$c = db::getRowCount('returned_asset','returned_asset_asset_id = '.$_GET['id'].' AND returned_by_dept_id = 1');
			if ($c > 0) {
				$this->setInfoMessage('Sorry, You have already requested for a return of this asset. It is awaiting processing from your head of department.');
				$this->redirect('departmentasset');
			}
			$returned_asset = new returned_asset();
			$returned_asset->returned_asset_user_id = Auth::getLoggedInUserId();
			$returned_asset->returned_asset_asset_id = $_GET['id'];
			$returned_asset->returned_asset_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
			$returned_asset->returned_by_dept_id = 1;
			$returned_asset->save();
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
			$log->log_to_asset_id = $_GET['id'];
			$name = Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername');
			$log->log_description = Notifications::return_user_asset($asset->asset_model, $asset->asset_tag_id, $name);
			$log->save();
			
			$this->setSuccessMessage('Your request has been successfully sent. It is pending approval from the fixed assets department.');
			$this->redirect('departmentasset');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			$this->redirect('departmentasset');
		}
	}
	
}







