<?php
class EmployeeRequestController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('employeerequest');
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
			//recieves requests from users in his/her the department
			if(!Registry::checkRole(Roles::LIST_INHOUSE_REQUESTS)) {
				return 'request_fk_user_id = '.Auth::getLoggedInUserId();
			}
			return 'request_for_hod = 1 AND request_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id');
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		if(!Registry::checkRole(Roles::LIST_INHOUSE_REQUESTS)) {
			return 'request_fk_user_id = '.Auth::getLoggedInUserId().' AND (request_title LIKE '.$search.' OR request_body LIKE '.$search.' OR request_reference LIKE '.$search.')';
		}
		return '(request_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id').' AND request_for_hod = 1) AND (request_title LIKE '.$search.' OR request_body LIKE '.$search.' OR request_reference LIKE '.$search.')';
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
		$request = new request();
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'request INNER JOIN '.$p.'user ON '.$p.'request.request_fk_user_id = '.$p.'user.user_id ';
		$this->template->request_data = $request->getRows($t,'*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		if (Registry::checkRole(Roles::LIST_INHOUSE_REQUESTS)) {
			$this->template->load('user_request.html');
		} else {
			$this->template->load('user_outgoing_request.html');
		}
	}
	
	public function index_postAction() {
		$this->template->load('user_request.html');
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
			$this->template->load('ajax/blog/reademployeerequest.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
		$this->template->load('ajax/blog/reademployeerequest.html');
	}
	
	public function compose_getAction() {
		if (Registry::checkRole(Roles::ASSIGN_ASSET_TO_DEPARTMENT)) {
			$this->template->count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_for_dvc = 1');
		} else if (Registry::checkRole(Roles::ASSIGN_ASSET_TO_USER)) {
			$this->template->count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_for_hod = 1 AND request_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id'));
		}
		$category = new category();
		$this->template->category_data = $category::select('asset_category','*');
		$this->template->load('user_request_add.html');
	}
	
	public function compose_with_id_getAction() {
		try {
			if (isset($_GET['id']) && is_numeric($_GET['id'])) {
				$request = new request($_GET['id']);
				$this->template->model = $request->request_model;
				$this->template->cat_id = $request->request_fk_asset_cat_id;
			}
			if (Registry::checkRole(Roles::ASSIGN_ASSET_TO_DEPARTMENT)) {
				$this->template->count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_for_dvc = 1');
			} else if (Registry::checkRole(Roles::ASSIGN_ASSET_TO_USER)) {
				$this->template->count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_for_hod = 1 AND request_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id'));
			}
			$category = new category();
			$this->template->category_data = $category::select('asset_category','*');
			$this->template->load('user_request_add.html');
		} catch (Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please Enter the request info manually.');
			$this->template->load('user_request_add.html');
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
		if (Registry::checkRole(Roles::SEND_DVC_REQUESTS)) {
			$request->request_for_dvc = 1;
		} else {
			$request->request_for_hod = 1;
		}
		$request->save();
		
		$this->setSuccessMessage('The request has been sent successfully.');
		$this->redirect('employeerequest');
	}

	public function send_getAction() {
		$this-> send_postAction();
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
				header('location: '.$this->baseUrl.'requestassign?id='.$_GET['id']);
				exit;
			}
			$this->setSuccessMessage('You have approved the request successfully.');
			header('location: '.$this->baseUrl.'employeerequest');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
		//header('location: '.$this->baseUrl.'employeerequest');
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
		}
	}
}







