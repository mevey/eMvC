<?php
class GroupController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('user');
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
		return 'group_name LIKE '.$search.'';
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
		$group = new group();
		
		$this->template->list_data = $group->getRows('group','*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		if (!empty($_GET['group_role_id'])) {
			$this->template->toggle_group_role = $_GET['group_role_id'];
		}
		
		$this->template->load('user_group.html');
	}
	
	public function view_role_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$group= new group($_GET['id']);
			$this->template->data = $group;
			$this->template->all_roles = db::select('role','*','','','');
			$p = Registry::getInstance()->config['database']['db_prefix'];
			$t = $p.'role INNER JOIN '.$p.'group_role ON '.$p.'role.role_id = '.$p.'group_role.role_id';
			$this->template->role_data = db::select($t,'*','group_id='.$_GET['id'],'','');
			$this->template->load('group_role.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function add_role_postAction() {
		try{
			if ((!isset($_POST['group_id']) || !is_numeric($_POST['group_id'])) || (!isset($_POST['group_id']) || !is_numeric($_POST['role_id']))) {
				throw new exception();
			}
			$dt = db::getRowCount('group_role','role_id='.$_POST['role_id'].' AND group_id ='.$_POST['group_id']);
			if ($dt == 0) {
				$group_role = new group_role();
				$group_role->role_id = $_POST['role_id'];
				$group_role->group_id = $_POST['group_id'];
				$group_role->save();
				
				$role = new role('role_id ='. $_POST['role_id']);
				$group = new group('group_id ='. $_POST['group_id']);
				
				$name = Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername');
				$post = Auth::getLoggedInUserData('user_role');
				
				$log = new log();
				$log->log_date = Registry::getDate();
				$log->log_by_user_id = Auth::getLoggedInUserId();
				$log->log_to_group_id = $_POST['group_id'];
				$log->log_description = Notifications::add_group_role($group->group_name,$role->role_alias,$name,$post,$role->role_description);
				$log->save();
			}
			
			$this->setSuccessMessage('The group role has successfully been added.');
			$this->redirect('group/view_role?id='.$_POST['group_id']);
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again');
			$this->template->load('user_group.html');
			return;
		}
	}
	
	public function delete_group_role_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$group_role = new group_role($_GET['id']);
			$group_id = $group_role->group_id;
			$role_id = $group_role->role_id;
			
			$name = Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername');
			$post = Auth::getLoggedInUserData('user_role');
			
			$group = new group('group_id = '.$group_id);
			$role = new role('role_id = '.$role_id);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_group_id = $group_id;
			$log->log_description = Notifications::revoke_group_role($group->group_name,$role->role_alias,$name,$post,$role->role_description);
			$log->save();
			
			$group_role->deleteMe();
			
			$this->setSuccessMessage('The group has been successfully deleted.');
			$this->redirect('group/view_role?id='.$group_id);
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			$this->template->load('user_group.html');
			return;
		}
	}
	
	public function add_group_getAction() {
		$this->template->load('user_group_add.html');
	}
	
	public function add_group_postAction() {
		$group = new group();
		$group->group_name = $_POST['name'];
		$group->group_description = $_POST['description'];
		$group->save();
		
		$log = new log();
		$log->log_date = Registry::getDate();
		$log->log_by_user_id = Auth::getLoggedInUserId();
		$log->log_to_dept_id = 5;
		$log->log_description = Notifications::add_group($_POST['name'], Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_other_name'));
		$log->save();
		
		$this->setSuccessMessage('The group has been added successfully.');
		$this->redirect('group');
	}
	
	public function delete_group_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$group = new group($_GET['id']);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_dept_id = 5;
			$log->log_description = Notifications::delete_group($group->group_name, Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_other_name'));
			$log->save();
			
			$group->deleteMe();
			$this->setSuccessMessage('The group has been deleted successfully');
			$this->redirect('group');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, an error occurred. Please try again.');
			$this->redirect('group');
			return;
		}
	}
}




