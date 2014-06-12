<?php
class CategoryController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('cat');
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
	}
	
	public function _afterAction() {
		
	}
	
	private function searchClause() {
		$search =$this->getSession('search');
		if (empty($search)) {
			return '';
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		return 'asset_category_name LIKE '.$search.' OR asset_category_depreciation_rate LIKE '.$search.'';
	}
	
	private function orderClause() {
		$order_by = $this->getSession('order_by');
		$order_direction = strtoupper($this->getSession('order_direction'));
		if (empty($order_by)) {
			return 'asset_category_id DESC';
		} else if (!empty($order_direction) && ($order_direction == 'ASC' || $order_direction == 'DESC')){
			$order_by = $order_by.' '.$order_direction;
		}
		
		return $order_by;
	}
	
	public function index_getAction() {
		$asset_category = new asset_category();
		$this->template->list_data = $asset_category->getRows('asset_category','*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		
		$cat_data = $this->template->list_data;
		$count_arr = array();
		foreach ($cat_data['data'] as $cat) {
			$c = db::getRowCount('asset','asset_fk_cat_id = '.$cat['asset_category_id'].' AND asset_state <> 2 ');
			$count_arr[$cat['asset_category_id']] = $c;
		}
		$this->template->asset_count = $count_arr;
		
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		$this->template->load('category.html');
	}
	
	public function index_postAction() {
		$this->template->load('category.html');
	}
	
	public function add_getAction() {
		$this->template->load('category_add.html');
	}
	
	public function add_postAction() {
		$attribLimit = (int) $_POST['attrib_count'];
		
		$attribArray = array();
		for ($i = 1; $i <= $attribLimit; $i++) {
			if (!isset($_POST['attrib_name_'.$i])) continue;
			
			$attrib_config = explode(',', $_POST['attrib_config_'.$i]);
			
			$attribName = strtolower($_POST['attrib_name_'.$i]);
			$attribName = str_replace(' ','_',$attribName);
			
			$attribArray[] = array(
					'attrib_name' => $attribName,
					'attrib_type' => $_POST['attrib_type_'.$i],
					'attrib_required' => (!empty($_POST['attrib_required_'.$i])) ? 1 : 0,
					'attrib_config' => explode(',', $_POST['attrib_config_'.$i])
					);
		}
		
		$attrib_array_data = json_encode($attribArray);
		
		$asset_category = new asset_category();
		
		$asset_category->asset_category_name = $_POST['name'];
		$asset_category->asset_category_description = $_POST['description'];
		$asset_category->asset_category_depreciation_rate = $_POST['dep_rate'];
		$asset_category->asset_category_meta_fields = $attrib_array_data;
		$asset_category->asset_category_retire_age = $_POST['retire'];
		
		$asset_category->save();
		
		$log = new log();
		$log->log_date = Registry::getDate();
		$log->log_by_user_id = Auth::getLoggedInUserId();
		$log->log_to_dept_id = 5;
		$log->log_description = Notifications::add_asset_category($_POST['name'], $_POST['dep_rate']);
		$log->save();
		
		$this->setSuccessMessage('The asset category has successfully been added.');
		
		
		
		$this->redirect('category');
	}
	
	public function add_another_postAction() {
		$asset_category = new asset_category();
		
		$asset_category->asset_category_name = $_POST['name'];
		$asset_category->asset_category_description = $_POST['description'];
		$asset_category->asset_category_depreciation_rate = $_POST['dep_rate'];
		
		$asset_category->save();
		
		$this->setSuccessMessage('The asset categories have successfully been added.');
		$this->redirect('category/add');
	}
	
	public function delete_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$asset_category = new asset_category($_GET['id']);
			$asset_category->deleteMe();
			
			$this->setSuccessMessage('The asset category has been deleted successfully.');
			$this->redirect('category');
			exit;
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry. Something went wrong. Please try again.');
			$this->redirect('category');
			exit;
		}
	}
	
}





