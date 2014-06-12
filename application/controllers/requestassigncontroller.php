<?php
class RequestAssignController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('departmentrequest');
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
	
	public function index_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$request = new request($_GET['id']);
			
			$department = new department($request->request_fk_dept_id);
			
			$asset_category = new asset_category($request->request_fk_asset_cat_id);
			
			$user = new user($request->request_fk_user_id);
			
			$asset = new asset();
			
			$p = Registry::getInstance()->config['database']['db_prefix'];
			if ($request->request_for_dvc == 1) {
				$this->template->list_data = $asset->getRows('asset','*','asset_state = 0 AND asset_fk_cat_id ='.$request->request_fk_asset_cat_id,$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
			} else {
				$t = $p.'allocation INNER JOIN '.$p.'asset ON '.$p.'allocation.allocation_fk_asset_id = '.$p.'asset.asset_id';
				$this->template->list_data = $asset->getRows($t,'*','allocation_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id').' AND asset_fk_cat_id ='.$request->request_fk_asset_cat_id, $this->orderClause(), $this->getSession('items_per_page'),$this->getSession('page_number'));
			}
			$this->template->id = $_GET['id'];
			$this->template->request_data = db::select('request','*','request_id = '.$_GET['id']);
			$this->template->request_model = $request->request_model;
			$this->template->request_body = $request->request_body;
			$this->template->dept_name = $department->department_name;
			$this->template->user_name = $user->user_surname.' '.$user->user_othername;
			$this->template->user_id = $user->user_id;
			$this->template->category_name = $asset_category->asset_category_name;
			$this->template->search_data = $this->getSession('search');
			$this->template->order_direction = $this->getSession('order_direction');
			$this->template->order_by = $this->getSession('order_by');
			
			$this->template->load('request_assign.html');
			
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function index_postAction() {
	}
	
	public function dept_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$request = new request($_GET['id']);
			
			if ($request->request_fk_allocation_id > 0) {
				$this->setInfoMessage('Approval and assignment has already been done for this request.');
				$this->redirect('departmentrequest');
			} else {
				
				$department = new department($request->request_fk_dept_id);
				
				$asset_category = new asset_category($request->request_fk_asset_cat_id);
				
				$user = new user($request->request_fk_user_id);
				
				$asset = new asset();
				
				$p = Registry::getInstance()->config['database']['db_prefix'];
				if ($request->request_for_dvc == 1) {
					$this->template->list_data = $asset->getRows('asset','*','asset_state = 0 AND asset_fk_cat_id ='.$request->request_fk_asset_cat_id,$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
				} else {
					$t = $p.'allocation INNER JOIN '.$p.'asset ON '.$p.'allocation.allocation_fk_asset_id = '.$p.'asset.asset_id';
					$this->template->list_data = $asset->getRows($t,'*','allocation_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id').' AND asset_fk_cat_id ='.$request->request_fk_asset_cat_id, $this->orderClause(), $this->getSession('items_per_page'),$this->getSession('page_number'));
				}
				$this->template->request_id = $_GET['id'];
				$this->template->request_data = db::select('request','*','request_id = '.$_GET['id']);
				$this->template->count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_dvc_approve = 1');
				$this->template->request_model = $request->request_model;
				$this->template->request_body = $request->request_body;
				$this->template->dept_name = $department->department_name;
				$this->template->user_name = $user->user_surname.' '.$user->user_othername;
				$this->template->user_id = $user->user_id;
				$this->template->category_name = $asset_category->asset_category_name;
				$this->template->search_data = $this->getSession('search');
				$this->template->order_direction = $this->getSession('order_direction');
				$this->template->order_by = $this->getSession('order_by');
				
				$this->template->load('dept_request_assign.html');
			}
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function assign_getAction() {
		try{
			if ((!isset($_GET['id']) || !is_numeric($_GET['id'])) && (!isset($_GET['r']) || !is_numeric($_GET['r']))) {
				throw new exception();
			}
			$request = new request($_GET['r']);
			$allocation = new allocation();
			if ($request->request_for_hod == 1){
				$allocation->allocation_fk_user_id = $request->request_fk_user_id;
			}
			if ($request->request_for_dvc == 1){
				$allocation->allocation_fk_dept_id = $request->request_fk_dept_id;
			}
			$allocation->allocation_fk_asset_id = $_GET['id'];
			$allocation->allocation_date = Registry::getDate();
			$id = $allocation->save();
			
			$request->request_fk_allocation_id = $id;
			$request->save();
			
			$this->setSuccessMessage('The assignment has been executed successfully.');
			$this->redirect('requestassign/list');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function list_getAction() {
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'request INNER JOIN '.$p.'user ON '.$p.'request.request_fk_user_id = '.$p.'user.user_id INNER JOIN '.$p.'department  ON '.$p.'request.request_fk_dept_id='.$p.'department.department_id';
		$this->template->list_data = db::select($t,'*','request_fk_allocation_id = 0 AND request_hod_approve = 1 AND request_for_hod = 1 AND request_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id'));
		//$this->template->count = db::getRowCount($t,'request_fk_allocation_id = 0 AND request_hod_approve = 1 AND request_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id'));
		$this->template->load('approved_requests.html');
	}
	
	public function list2_getAction() {
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'request INNER JOIN '.$p.'user ON '.$p.'request.request_fk_user_id = '.$p.'user.user_id INNER JOIN '.$p.'department  ON '.$p.'request.request_fk_dept_id='.$p.'department.department_id';
		$this->template->list_data = db::select($t,'*','request_fk_allocation_id = 0 AND request_dvc_approve= 1 AND request_for_dvc = 1');
		//$this->template->count = db::getRowCount($t,'request_fk_allocation_id = 0 AND request_dvc_approve = 1');
		$this->template->load('hod_approved_requests.html');
	}
	
	public function help_getAction() {
		
	}
	
	public function assign_dept_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$request = new request($_GET['id']);
			
			$department = new department($request->request_fk_dept_id);
			
			$asset_category = new asset_category($request->request_fk_asset_cat_id);
			
			$user = new user($request->request_fk_user_id);
			
			$p = Registry::getInstance()->config['database']['db_prefix'];
			$t = $p.'request INNER JOIN '.$p.'user ON '.$p.'request.request_fk_user_id = '.$p.'user.user_id INNER JOIN '.$p.'department  ON '.$p.'request.request_fk_dept_id='.$p.'department.department_id';
		
			$this->template->count = db::getRowCount($t,'request_fk_allocation_id = 0 AND request_dvc_approve = 1');
			
			$asset = new asset();
			
			$this->template->list_data = $asset->getRows('asset','*','asset_state = 0 AND asset_fk_cat_id ='.$request->request_fk_asset_cat_id,$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
			
			$this->template->req_id = $_GET['id'];
			$this->template->request_data = db::select('request','*','request_id = '.$_GET['id']);
			$this->template->request_model = $request->request_model;
			$this->template->request_body = $request->request_body;
			$this->template->dept_name = $department->department_name;
			$this->template->user_name = $user->user_surname.' '.$user->user_othername;
			$this->template->user_id = $user->user_id;
			$this->template->category_name = $asset_category->asset_category_name;
			$this->template->search_data = $this->getSession('search');
			$this->template->order_direction = $this->getSession('order_direction');
			$this->template->order_by = $this->getSession('order_by');
			
			$this->template->load('dept_request_assign.html');
			
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function assign_to_req_dept_getAction() {
		try{
			if ((!isset($_GET['id']) || !is_numeric($_GET['id'])) && (!isset($_GET['r']) || !is_numeric($_GET['r']))) {
				throw new exception();
			}
			$request = new request($_GET['r']);
			$allocation = new allocation();
			if ($request->request_for_hod == 1){
				$allocation->allocation_fk_user_id = $request->request_fk_user_id;
			}
			if ($request->request_for_dvc == 1){
				$allocation->allocation_fk_dept_id = $request->request_fk_dept_id;
			}
			$allocation->allocation_fk_asset_id = $_GET['id'];
			$allocation->allocation_date = Registry::getDate();
			$id = $allocation->save();
			
			$request->request_fk_allocation_id = $id;
			$request->save();
			
			$this->setSuccessMessage('The assignment has been executed successfully.');
			$this->redirect('requestassign/list2');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
}







