<?php
class UserOutgoingRequestController extends BaseController {
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
			if (Auth::getLoggedInUserData('user_fk_cat_id') == 4){
				return 'request_fk_user_id = '.Auth::getLoggedInUserId().' OR request_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id');
			}
			return 'request_fk_user_id = '.Auth::getLoggedInUserId();
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		if (Auth::getLoggedInUserData('user_fk_cat_id') == 4){
			return '(request_fk_user_id = '.Auth::getLoggedInUserId().' OR request_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id').') AND (request_for_hod = 1 AND (request_title LIKE '.$search.' OR request_body LIKE '.$search.'))';
		}
		return 'request_fk_user_id = '.Auth::getLoggedInUserId().' AND (request_for_hod = 1 AND (request_title LIKE '.$search.' OR request_body LIKE '.$search.'))';
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
	
	public function index_getAction() {
		if (Registry::checkRole(Roles::ASSIGN_ASSET_TO_DEPARTMENT)) {
			$this->template->count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_for_dvc = 1');
		} else if (Registry::checkRole(Roles::ASSIGN_ASSET_TO_USER)) {
			$this->template->count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_for_hod = 1 AND request_fk_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id'));
		}
		$request = new request();
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'request INNER JOIN '.$p.'user ON '.$p.'request.request_fk_user_id = '.$p.'user.user_id ';
		$this->template->request_data = $request->getRows($t,'*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		$this->template->load('user_outgoing_request.html');
	}
	
	public function index_postAction() {
		$this->template->load('user_outgoing_request.html');
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
			$this->template->load('ajax/blog/readrequest.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
		$this->template->load('ajax/blog/readrequest.html');
	}
	
	public function compose_getAction() {
		$category = new category();
		$this->template->category_data = $category::select('asset_category','*');
		$this->template->load('request_add.html');
	}
	
	public function send_postAction() {
		$request = new request();
		$request->request_fk_asset_cat_id = $_POST['category'];
		$request->request_model = $_POST['model'];
		$request->request_title = $_POST['title'];
		$request->request_body = $_POST['body'];
		$request->request_date= $_POST['date'];
		$request->save();
		
		$this->setSuccessMessage('The request has been sent successfully.');
		$this->redirect('user_outgoing_request');
		
	}
	
	public function send_getAction() {
		$this-> send_postAction();
	}
	
	public function help_getAction() {
		
	}
	
}





