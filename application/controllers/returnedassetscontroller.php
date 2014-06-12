<?php
class ReturnedAssetsController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('dept_asset');
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
	}
	
	private function searchClause() {
		$search = $this->getSession('search');
		if (empty($search)) {
			return '';
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		return 'asset_model LIKE '.$search.' OR asset_tag_id LIKE '.$search;
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
	
	public function _afterAction() {}
	
	public function index_getAction() {
		$this->dept_assets_getAction();
	}
	
	public function index_postAction() {
	}
	
	public function dept_assets_getAction() {
		$returned_asset = new returned_asset();
		
		$id = Auth::getLoggedInUserData('user_fk_dept_id');
		
		$department = new department('department_id ='.$id);
		$this->template->dept_name = $department->department_name;
		$this->template->dept_id = $department->department_id;
		
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'asset INNER JOIN '.$p.'returned_asset ON '.$p.'asset.asset_id = '.$p.'returned_asset.returned_asset_asset_id INNER JOIN '.$p.'asset_category ON '.$p.'asset.asset_fk_cat_id = '.$p.'asset_category.asset_category_id INNER JOIN '.$p.'user ON '.$p.'returned_asset.returned_asset_user_id = '.$p.'user.user_id';
		
		$this->template->list_data = $returned_asset->getRows($t,'*','returned_asset_dept_id = '.$id, $this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->count = $returned_asset->getRowCount($t,'returned_asset_dept_id = '.$id);
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$this->template->load('returned_dept_assets.html');
	}
	
	public function all_assets_getAction() {
		$returned_asset = new returned_asset();
		
		$id = Auth::getLoggedInUserData('user_fk_dept_id');
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'asset INNER JOIN '.$p.'returned_asset ON '.$p.'asset.asset_id = '.$p.'returned_asset.returned_asset_asset_id INNER JOIN '.$p.'department ON '.$p.'returned_asset.returned_asset_dept_id = '.$p.'department.department_id';
		
		$this->template->list_data = $returned_asset->getRows($t,'*','returned_by_dept_id != 0 ', $this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->count = $returned_asset->getRowCount($t,'returned_by_dept_id != 0');
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$this->template->load('returned_assets.html');
	}
	
	public function approve_employee_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$returned_asset = new returned_asset($_GET['id']);
			
			$asset = new asset($returned_asset->returned_asset_asset_id);
			
			$user = new user($returned_asset->returned_asset_user_id);
			
			$allocation = new allocation('allocation_fk_asset_id = '.$returned_asset->returned_asset_asset_id);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_user_id = $returned_asset->returned_asset_user_id;
			$log->log_to_asset_id = $returned_asset->returned_asset_user_id;
			$log->log_description = Notifications::approve_return($asset->asset_tag_id,
			$asset->asset_model);
			$log->log_asset_description = Notifications::asset_approve_return($user->user_surname.' '.$user->user_othername);
			
			$log->save();
			
			$allocation->allocation_fk_user_id = 0;
			$allocation->save();
			
			$returned_asset->deleteMe();
			
			$this->setSuccessMessage('The asset has been deallocated successfully.');
			$this->redirect('returnedassets');
		} catch(Exception $err) {
			echo $err;exit;
			$this->setErrorMessage('Sorry. Something went wrong. Please try again.');
			$this->redirect('returnedassets');
			return;
		}
	}
	
	public function decline_employee_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$returned_asset = new returned_asset($_GET['id']);
			
			$asset = new asset($returned_asset->returned_asset_asset_id);
			
			$user = new user($returned_asset->returned_asset_user_id);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_user_id = $returned_asset->returned_asset_user_id;
			$log->log_to_asset_id = $returned_asset->returned_asset_user_id;
			$log->log_description = Notifications::decline_return($asset->asset_tag_id,
			$asset->asset_model);
			$log->log_asset_description = Notifications::asset_approve_return($user->user_surname.' '.$user->user_othername);
			
			$log->save();
			
			$returned_asset->deleteMe();
			
			$this->setSuccessMessage('The asset has been returned to user successfully.');
			$this->redirect('returnedassets');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry. Something went wrong. Please try again.');
			$this->redirect('returnedassets');
			return;
		}
	}
	
	public function approve_dept_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$returned_asset = new returned_asset($_GET['id']);
			
			$asset = new asset($returned_asset->returned_asset_asset_id);
			
			$user = new user($returned_asset->returned_asset_user_id);
			
			$allocation = new allocation('allocation_fk_asset_id = '.$returned_asset->returned_asset_asset_id);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_user_id = $returned_asset->returned_asset_user_id;
			$log->log_to_asset_id = $returned_asset->returned_asset_user_id;
			$log->log_description = Notifications::approve_return($asset->asset_tag_id,
			$asset->asset_model);
			$log->log_asset_description = Notifications::asset_approve_return($user->user_surname.' '.$user->user_othername);
			
			$log->save();
			
			$asset->asset_state = 0;
			$asset->save();
			$allocation->deleteMe();
			$returned_asset->deleteMe();
			
			$this->setSuccessMessage('The asset has been deallocated successfully.');
			$this->redirect('returnedassets');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry. Something went wrong. Please try again.');
			$this->redirect('returnedassets');
			return;
		}
	}
	
	public function decline_dept_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$returned_asset = new returned_asset($_GET['id']);
			
			$asset = new asset($returned_asset->returned_asset_asset_id);
			
			$user = new user($returned_asset->returned_asset_user_id);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_user_id = $returned_asset->returned_asset_user_id;
			$log->log_to_asset_id = $returned_asset->returned_asset_user_id;
			$log->log_description = Notifications::decline_return($asset->asset_tag_id,
			$asset->asset_model);
			$log->log_asset_description = Notifications::asset_approve_return($user->user_surname.' '.$user->user_othername);
			
			$log->save();
			
			$returned_asset->deleteMe();
			
			$this->setSuccessMessage('The asset has been returned to user successfully.');
			$this->redirect('returnedassets');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry. Something went wrong. Please try again.');
			$this->redirect('returnedassets');
			return;
		}
	}
	
}







