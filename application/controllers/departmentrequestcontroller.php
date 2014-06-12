<?php
class DepartmentRequestController extends BaseController {
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
		$search =$this->getSession('search');
		if (empty($search)) {
			return 'request_for_dvc = 1';
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		return 'request_for_dvc = 1 AND (request_title LIKE '.$search.' OR request_body LIKE '.$search.' OR request_reference LIKE '.$search.')';
	}
	
	private function orderClause() {
		$order_by = $this->getSession('order_by');
		$order_direction = strtoupper($this->getSession('order_direction'));
		if (empty($order_by)) {
			return 'request_id DESC';
		} else if (!empty($order_direction) && ($order_direction == 'ASC' || $order_direction == 'DESC')){
			$order_by = $order_by.' '.$order_direction;
		}
		
		return $order_by;
	}
	
	public function index_getAction() {
		$this->checkRole(Roles::LIST_DEPARTMENT_REQUESTS);
		
		$request = new request();
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'request INNER JOIN '.$p.'user ON '.$p.'request.request_fk_user_id = '.$p.'user.user_id ';
		$this->template->request_data = $request->getRows($t,'*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$this->template->load('hod_request.html');
	}
	
	public function index_postAction() {
		$this->template->load('hod_request.html');
	}
	
	public function read_getAction() {
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
			$this->template->load('ajax/blog/readdepartmentrequest.html');
		} catch(Exception $err) {
			echo 'Invalid Asset-ID specified.'.$err->getMessage();
			return;
		}
		$this->template->load('ajax/blog/readdepartmentrequest.html');
	}
	
	public function compose_getAction() {
		$this->template->count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_for_dvc = 1');
		
		$category = new category();
		$this->template->category_data = $category::select('asset_category','*');
		$this->template->load('dept_request_add.html');
	}
	
	public function compose_with_id_getAction() {
		try {
			if (isset($_GET['id']) && is_numeric($_GET['id'])) {
				$request = new request($_GET['id']);
				$this->template->model = $request->request_model;
				$this->template->cat_id = $request->request_fk_asset_cat_id;
			}
			
			$this->template->count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_for_dvc = 1');
			
			$category = new category();
			$this->template->category_data = $category::select('asset_category','*');
			
			$p = Registry::getInstance()->config['database']['db_prefix'];
			$t = $p.'request INNER JOIN '.$p.'user ON '.$p.'request.request_fk_user_id = '.$p.'user.user_id INNER JOIN '.$p.'department  ON '.$p.'request.request_fk_dept_id='.$p.'department.department_id';
			$this->template->list_data = db::select($t,'*','request_fk_allocation_id = 0 AND request_dvc_approve= 1 ');
			$this->template->count = db::getRowCount($t,'request_fk_allocation_id = 0 AND request_dvc_approve = 1');
			
			$this->setSuccessMessage('The request has been sent to the Procurement department successfully.');
			$this->template->load('hod_approved_requests.html');
		} catch (Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please Enter the request info manually.');
			$this->template->load('hod_approved_requests.html');
		}
	}
	
	public function send_postAction() {
		$request = new request();
		$request->request_reference = 'REF/'.(10000 + rand(5, 1034422));
		$request->request_fk_user_id = Auth::getLoggedInUserId();
		$request->request_fk_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
		$request->request_fk_asset_cat_id = $_POST['category'];
		$request->request_model = $_POST['model'];
		$request->request_title = $_POST['title'];
		$request->request_body = $_POST['body'];
		$request->request_date= $_POST['date'];
		$request->request_for_dvc = 1;
	
		$request->save();
		
		$this->setSuccessMessage('The request has been sent successfully.');
		$this->redirect('departmentrequest');
	}
	public function help_getAction() {
		
	}
	
	public function approve_request_postAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$request = new request($_GET['id']);
			
			
			if ($_POST['status'] == 'yes') {
				if ($request->request_for_hod == 1) {
					$request->request_hod_approve = 1;
					$request->request_hod_remarks = $_POST['remarks'];
				} else if ($request->request_for_dvc == 1) {
					$request->request_dvc_approve = 1;
					$request->request_dvc_remarks = $_POST['remarks'];
				}
				$request->save();
				
				$log = new log();
				$log->log_date = Registry::getDate();
				$log->log_by_user_id = Auth::getLoggedInUserId();
				$log->log_to_user_id = $_POST['user_id'];
				$name = Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername');
				$log->log_description = Notifications::approve_request($name, Auth::getLoggedInUserData('user_role'),$request->request_reference, $request->request_model);
				$log->save();
			} else if ($_POST['status'] == 'no') {
				if ($request->request_for_hod == 1) {
					$request->request_hod_approve = -1;
					$request->request_hod_remarks = $_POST['remarks'];
				} else if ($request->request_for_dvc == 1) {
					$request->request_dvc_approve = -1;
					$request->request_dvc_remarks = $_POST['remarks'];
				}
				$request->save();
				
				$log = new log();
				$log->log_date = Registry::getDate();
				$log->log_by_user_id = Auth::getLoggedInUserId();
				$log->log_to_user_id = $_POST['user_id'];
				$name = Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername');
				$log->log_description = Notifications::reject_request($name, Auth::getLoggedInUserData('user_role'),$request->request_reference, $request->request_model);
				$log->save();
			}
			if ($_POST['status'] == 'yes'){
				header('location: '.$this->baseUrl.'requestassign/dept?id='.$_GET['id']);
				exit;
			}
			$this->setSuccessMessage('You have approved the request successfully.');
			header('location: '.$this->baseUrl.'depatmentrequest');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function set_read_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$request = new request($_GET['id']);
			$request->request_read = 1;
			$request->save();
		
		} catch(Exception $err) {
			return;
		}
	}
	
	public function place_order_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$request = new request($_GET['id']);
			$user_id = Auth::getLoggedInUserId();
			$dept_id = $request->request_fk_dept_id;
			$cat_id = $request->request_fk_asset_cat_id;
			$description = $request->request_model.' Title: '.$request->request_title.' Message: '.$request->request_body;
			
			try {
				
				$order = new order('order_fk_request_id = '.$request->request_id);
				$this->setInfoMessage('An order has already been placed for that request.');
				
			} catch(Exception $err) {
				$order = new order();
				$order->order_fk_request_id = $_GET['id'];
				$order->order_fk_dept_id = $dept_id;
				$order->order_fk_user_id = $user_id;
				$order->order_fk_asset_category_id = $cat_id;
				$order->order_description = $description;
				$order->order_date = Registry::getDate();
				$order->save();
				
				$this->setSuccessMessage('The order has been placed successfully.');
				$this->redirect('orders');
			}
		
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again later');
			echo $err;
		}
	}
	
}





