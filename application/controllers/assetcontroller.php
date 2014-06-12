<?php
class AssetController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('asset');
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
		if (isset($_GET['statusfilter'])) {
			$this->setSession('statusfilter',$_GET['statusfilter']);
		}
	}
	
	public function _afterAction() {
		
	}
	
	private function searchClause() {
		$search = $this->getSession('search');
		$f = $this->getSession('filter');
		$d = $this->getSession('statusfilter');
		$cat_filter = $f != '' && is_numeric($f) ? ' AND asset_fk_cat_id = '.$this->getSession('filter') : '';
		$status_filter = $d != '' && is_numeric($d) ? ' AND asset_state = '.$this->getSession('statusfilter') : '';
		if (empty($search)) {
			return 'asset_state <> -1 AND asset_state <> 2 '.$cat_filter.$status_filter;
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		return ' asset_state <> -1 AND asset_state <> 2 AND (asset_model LIKE '.$search.' OR asset_tag_id LIKE '.$search.' OR asset_fk_cat_id = '.$search.')'.$cat_filter.$status_filter;
	}
	
	private function orderClause() {
		$order_by = $this->getSession('order_by');
		$order_direction = strtoupper($this->getSession('order_direction'));
		if (empty($order_by)) {
			return 'asset_id DESC';
		} else if (!empty($order_direction) && ($order_direction == 'ASC' || $order_direction == 'DESC')){
			$order_by = $order_by.' '.$order_direction;
		}
		
		return $order_by;
	}
	
	public function index_getAction() {
		$asset = new asset();
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'asset INNER JOIN '.$p.'asset_category ON '.$p.'asset.asset_fk_cat_id = '.$p.'asset_category.asset_category_id ';
		$this->template->list_data = $asset->getRows($t, '*', $this->searchClause(), $this->orderClause(), $this->getSession('items_per_page'),$this->getSession('page_number'));
		//echo db::$lastSql;exit;
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$this->template->filter = $this->getSession('filter');
		$this->template->statusfilter = $this->getSession('statusfilter');
		
		$this->template->cat_data = db::select('asset_category','*');
		$this->template->dept_data = db::select('department','*');
		
		$returned_asset = new returned_asset();
		$id = Auth::getLoggedInUserData('user_fk_dept_id');
		$t = $p.'asset INNER JOIN '.$p.'returned_asset ON '.$p.'asset.asset_id = '.$p.'returned_asset.returned_asset_asset_id';
		$this->template->count = $returned_asset->getRowCount($t,'returned_by_dept_id != 0');
		
		$this->template->load('asset.html');
	}
	
	
	
	public function add_getAction() {
		$category = new category();
		$this->template->category_data = $category::select('asset_category','*');
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$returned_asset = new returned_asset();
		$id = Auth::getLoggedInUserData('user_fk_dept_id');
		$t = $p.'asset INNER JOIN '.$p.'returned_asset ON '.$p.'asset.asset_id = '.$p.'returned_asset.returned_asset_asset_id';
		$this->template->count = $returned_asset->getRowCount($t,'returned_by_dept_id != 0');
		
		$this->template->load('asset_add.html');
	}
	
	public function bulk_getAction() {
		$category = new category();
		$this->template->category_data = $category::select('asset_category','*');
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$id = Auth::getLoggedInUserData('user_fk_dept_id');
		$t = $p.'asset INNER JOIN '.$p.'returned_asset ON '.$p.'asset.asset_id = '.$p.'returned_asset.returned_asset_asset_id';
		
		$returned_asset = new returned_asset();
		$this->template->count = $returned_asset->getRowCount($t,'returned_by_dept_id != 0');
		
		$this->template->load('asset_bulk_add.html');
	}
	
	public function add_postAction() {
		$this->redirect('/asset/add');
		$asset = new asset();
		$log = new log();
		
		$asset->asset_tag_id = $_POST['tag_id'];
		$asset->asset_model = $_POST['model'];
		$asset->asset_fk_cat_id = $_POST['category'];
		$asset->asset_cost = $_POST['cost'];
		$asset->asset_supply_date = $_POST['date'];
		$asset->asset_description = $_POST['description'];
		$asset->asset_supplier = $_POST['supplier'];
		$asset->asset_add_date = Registry::getDate();

		$asset_category = new asset_category('asset_category_id = '.$_POST['category']);
		$cat_meta = $asset_category->asset_category_meta_fields;
		
		$cat_attribs = json_decode($cat_meta, true);
		
		foreach($cat_attribs as $key => $attrib) {
			$post_key = 'attrib_'.$attrib['attrib_name'];
			$attrib['attrib_value'] = empty($_POST[$post_key]) ? '' : $_POST[$post_key];
			$cat_attribs[$key] = $attrib;
		}
		
		$asset->asset_meta_field_data = json_encode($cat_attribs);
		$id = $asset->save();
		
		for ($i = 1; $i <= $_POST['count']; $i++) {
			if (empty($_FILES['asset_img_'.$i])) continue;
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
				$this->redirect('asset/add');
				return;
			}
		}
		
		
		$log->log_date = Registry::getDate();
		$log->log_by_user_id = Auth::getLoggedInUserId();
		$log->log_to_asset_id = $id;
		$log->log_description = Notifications::add_asset($id,$_POST['tag_id'],$_POST['model'], $asset_category->asset_category_name);
		$log->log_asset_description = Notifications::asset_add_asset($asset_category->asset_category_name);
		$log->save();
		
		$this->setSuccessMessage('The asset has successfully been added');
		$this->redirect('asset');
	}
	
	public function assign_asset_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			
			$asset = new asset('asset_id='.$_GET['id']);
			$asset->asset_state = 1;
			$asset->save();
			$this->template->asset_data = $asset->getProperties();
			
			
			$this->template->media = db::select('asset_media','*','asset_media_fk_asset_id = '.$_GET['id'],'','');
			
			$this->template->dept_data = db::select('department','*','','','');
			$t = db::getRowCount('allocation','allocation_fk_asset_id='.$_GET['id']);
			if ($t == 0 ) {
				$this->template->allocation_err = 'This asset has not been assigned to any department';
			} else {
				$allocation = new allocation('allocation_fk_asset_id='.$_GET['id']);
				$department = new department('department_id='.$allocation->allocation_fk_dept_id);
				$this->template->allocation = $allocation;
				$this->template->allocated_dept = $department->department_name;
			}
			
			$asset_category = new asset_category('asset_category_id='.$this->template->asset_data['asset_fk_cat_id']);
			$this->template->category_data = $asset_category->getProperties();
		
			$this->template->load('asset_assign.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry. Something went wrong. Please try again.');
			$this->redirect('asset');
			return;
		}
		
	}
	
	public function assign_asset_postAction() {
		$allocation = new allocation();
		$allocation->allocation_fk_asset_id = $_POST['asset_id'];
		$allocation->allocation_fk_dept_id = $_POST['dept_id'];
		$allocation->allocation_date = Registry::getDate();
		$allocation->save();
		
		$department = new department($_POST['dept_id']);
		
		$asset = new asset('asset_id='.$_POST['asset_id']);
		$asset->asset_state = 1;
		$asset->save();
		
		$log = new log();
		$log->log_date = Registry::getDate();
		$log->log_by_user_id = Auth::getLoggedInUserId();
		$log->log_to_dept_id =$_POST['dept_id'];
		$log->log_to_asset_id = $_POST['asset_id'];
		$log->log_description = Notifications::assign_dept($asset->asset_model,
		$asset->asset_tag_id,$_POST['asset_id'] ,Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername'),
		Auth::getLoggedInUserData('user_role'),$department->department_name );
		$log->log_asset_description = Notifications::asset_assign_dept(Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername'),
		Auth::getLoggedInUserData('user_role'),$department->department_name );
		$log->save();
		
		$this->setSuccessMessage('You have successfully assigned the asset');
		$this->redirect('asset');
		
	}
	
	public function attrib_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$asset_category = new asset_category($_GET['id']);
			if (!empty($asset_category->asset_category_meta_fields) && $asset_category->asset_category_meta_fields != '{}') {
				$this->template->attrib_data = json_decode($asset_category->asset_category_meta_fields, true);
				$this->template->load('ajax/blog/assetaddattributes.html');
			} else {
				echo '{}';
			}
		} catch (Exception $err) {
			echo 'Sorry, some error occurred. Please try again later';
		}
		exit;
	}
	
	public function bulk_attrib_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$asset_category = new asset_category($_GET['id']);
			$this->template->attrib_data = json_decode($asset_category->asset_category_meta_fields, true);
			$this->template->load('ajax/blog/assetbulkattributes.html');

		} catch (Exception $err) {
			echo 'Sorry, some error occurred. Please try again later';
		}
		exit;
	}
	
	public function bulk_add_postAction() {
		$id = array();
		for ($i = 1; $i <= $_POST['count']; $i++) {
			$asset = new asset();
			
			$asset->asset_tag_id = $_POST['tag_id_'.$i];
			$asset->asset_model = $_POST['model_'.$i];
			$asset->asset_fk_cat_id = $_POST['category'];
			$asset->asset_cost = $_POST['cost'];
			$asset->asset_supply_date = $_POST['date'];
			$asset->asset_supplier = $_POST['supplier'];
			$asset->asset_add_date = Registry::getDate();

			$asset_category = new asset_category('asset_category_id = '.$_POST['category']);
			$cat_meta = $asset_category->asset_category_meta_fields;
			
			$cat_attribs = json_decode($cat_meta, true);
			
			foreach($cat_attribs as $key => $attrib) {
				$post_key = 'attrib_'.$attrib['attrib_name'].'_'.$i;
				$attrib['attrib_value'] = empty($_POST[$post_key]) ? '' : $_POST[$post_key];
				$cat_attribs[$key] = $attrib;
			}
			
			$asset->asset_meta_field_data = json_encode($cat_attribs);
			$id[] = $asset->save();
		}
		
		for ($i = 1; $i <= $_POST['img_count']; $i++) {
			if (empty($_FILES['asset_img_'.$i])) continue;
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
					
					foreach($id as $i) {
						$asset_media = new asset_media();
						$asset_media->asset_media_fk_asset_id = $i;
						$asset_media->asset_media_url_path = 'application/uploads/asset_images/'.$f.'.'.$extension;

						$asset_media->save();
					}
				}
			} else {
				$this->setErrorMessage('You have entered an invalid image file.');
				$this->redirect('/asset/bulk_add');
				return;
			}
		}
		
		
		$log = new log();
		$log->log_date = Registry::getDate();
		$log->log_by_user_id = Auth::getLoggedInUserId();
		$log->log_to_asset_id = $id;
		$log->log_description = Notifications::bulk_add_asset($_POST['count'], $asset_category->asset_category_name);
		$log->log_asset_description = Notifications::bulk_add_asset($_POST['count'],$asset_category->asset_category_name);
		$log->save();
		
		$this->setSuccessMessage('You have successfully added '.$_POST['count'].' assets');
		$this->redirect('asset');
	}
}




