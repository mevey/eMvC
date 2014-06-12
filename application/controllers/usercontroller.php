<?php
class UserController extends BaseController {
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
		if (isset($_GET['deptfilter'])) {
			$this->setSession('deptfilter',$_GET['deptfilter']);
		}
		if (isset($_GET['groupfilter'])) {
			$this->setSession('groupfilter',$_GET['groupfilter']);
		}
	}
	
	public function _afterAction() {
		
	}
	
	private function searchClause() {
		$search = $this->getSession('search');
		$d = $this->getSession('deptfilter');
		$g = $this->getSession('groupfilter');
		$group_filter = $g != '' && is_numeric($g) ? ' AND user_fk_cat_id = '.$this->getSession('groupfilter') : '';
		$dept_filter = $d != '' && is_numeric($d) ? ' AND user_fk_dept_id = '.$this->getSession('deptfilter') : '';
		if (Registry::checkRole(Roles::LIST_USERS)) {
			if (empty($search)) {
				return '1=1 '.$group_filter.' '.$dept_filter;
			}
			$search = pdo_db::getInstance()->quote('%'.$search.'%');
			return 'user_surname LIKE '.$search.' OR user_othername LIKE '.$search.' OR user_role LIKE '.$search.' OR user_employment_number LIKE '.$search.' '.$group_filter.' '.$dept_filter;
		} else  if (Registry::checkRole(Roles::LIST_DEPARTMENT_USERS)){
			if (empty($search)) {
				$dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
				return 'user_fk_dept_id = '.$dept_id.' '.$group_filter;
			}
			$search = pdo_db::getInstance()->quote('%'.$search.'%');
			return 'user_fk_dept_id = 1 AND (user_surname LIKE '.$search.' OR user_othername LIKE '.$search.' OR user_role LIKE '.$search.' OR user_employment_number LIKE '.$search.')'.$group_filter;
		}
	}
	
	private function orderClause() {
		$order_by = $this->getSession('order_by');
		$order_direction = strtoupper($this->getSession('order_direction'));
		if (empty($order_by)) {
			return 'user_id DESC';
		} else if (!empty($order_direction) && ($order_direction == 'ASC' || $order_direction == 'DESC')){
			$order_by = $order_by.' '.$order_direction;
		}
		
		return $order_by;
	}
	
	public function index_getAction() {
		$user = new user();
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'user INNER JOIN '.$p.'department ON '.$p.'user.user_fk_dept_id = '.$p.'department.department_id INNER JOIN '.$p.'group ON '.$p.'user.user_fk_cat_id='.$p.'group.group_id';
		$this->template->list_data = $user->getRows($t,'*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		$this->template->groupfilter = $this->getSession('groupfilter');
		$this->template->deptfilter = $this->getSession('deptfilter');
		
		$this->template->dept_data = db::select('department', '*');
		$this->template->group_data = db::select('group', '*');
		$this->template->load('user.html');
	}
	
	public function add_getAction() {
		$this->template->category_data = db::select('group','*');
		$this->template->dept_data = db::select('department','*');
		
		$this->template->load('user_add.html');
	}
	
	public function add_postAction() {
		$user = new user();
		
		$user->user_surname = $_POST['surname'];
		$user->user_othername = $_POST['othername'];
		$user->user_employment_number = $_POST['emp_number'];
		$user->user_employment_date = $_POST['employment_date'];
		$user->user_role = $_POST['role'];
		$user->user_fk_dept_id = $_POST['dept'];
		$user->user_fk_cat_id = $_POST['category'];
		
		$user->save();
		
		$this->setSuccessMessage('The user has been added successfully.');
		$this->redirect('user');
	}
	
	public function group_getAction() {
		$group = new group();
		$this->template->list_data = $group->getRows('group','*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		$this->template->load('user_group.html');
	}
	
	public function group_postAction() {
		$this->group_getAction();
	}
	
	public function view_user_role_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$p = Registry::getInstance()->config['database']['db_prefix'];
			$this->template->isSuperAdmin = Registry::checkRole('*');
			
			$user = new user($_GET['id']);
			$this->template->user_data = $user->getProperties();
			
			$group = new group($user->user_fk_cat_id);
			$this->template->group_data = $group->getProperties();
			
			
			$this->template->all_roles = db::select('role','*','','','');
			
			// Get groups-roles for this user's group
			$t1 = $p.'group_role INNER JOIN '.$p.'role ON '.$p.'role.role_id = '.$p.'group_role.role_id';
			$this->template->group_roles = db::select($t1,'*','group_id='.$user->user_fk_cat_id,'','');
			
			//Get granted user-specific roles
			$t = $p.'role INNER JOIN '.$p.'user_role ON '.$p.'role.role_id = '.$p.'user_role.role_id';
			$this->template->denied_user_roles = db::select($t,'*','user_id='.$_GET['id'].' and user_role_grant = 0','','');
			
			//Get granted user-specific roles
			$t = $p.'role INNER JOIN '.$p.'user_role ON '.$p.'role.role_id = '.$p.'user_role.role_id';
			$this->template->user_roles = db::select($t,'*','user_id='.$_GET['id'].' and user_role_grant = 1','','');
			
			$this->template->load('user_role.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function add_user_role_postAction() {
		$user_role = new user_role();
		
		$user_role->user_id = $_POST['user_id'];
		$user_role->role_id = $_POST['role_id'];
		$user_role->user_role_grant = 1;
		
		$user_role->save();
		
		$role = new role($_POST['role_id']);
		$user = new user($_POST['user_id']);
		
		$log = new log();
		$log->log_date = Registry::getDate();
		$log->log_by_user_id = Auth::getLoggedInUserId();
		$log->log_to_user_id = $_POST['user_id'];
		$log->log_description = Notifications::add_user_role($role->role_alias,Auth::getLoggedInUserId(),$user->user_role, $role->role_description);
		$log->save();
		
		$this->setSuccessMessage('The role has been successfully assigned to the user.');
		header('location: '.$this->baseUrl.'user/view_user_role?id='.$_POST['user_id']);
	}
	
	public function grant_group_role_getAction() {
		try{
			if ((!isset($_GET['id']) || !is_numeric($_GET['id'])) || (!isset($_GET['user_id']) || !is_numeric($_GET['user_id']))){
				throw new exception();
			}
			$user_role = new user_role('role_id ='.$_GET['id'].' AND user_id='.$_GET['user_id']);
			
			$user = new user($_GET['user_id']);
			$group = new group($user->user_fk_cat_id);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_group_id = $group->group_id;
			$log_description = Notifications::add_user_role($group->group_name, $role->role_alias,Auth::getLoggedInUserId(),$user->user_role, $role->role_description);
			$log->save();
			
			$user_role->deleteMe();
			
			$this->setSuccessMessage('The group role has been successfully granted.');
			$this->redirect('user/view_user_role?id='.$_GET['user_id']);
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function deny_group_role_getAction() {
		try{
			if ((!isset($_GET['id']) || !is_numeric($_GET['id'])) || (!isset($_GET['user_id']) || !is_numeric($_GET['user_id']))){
				throw new exception();
			}
			$user_role = new user_role();
			$user_role->user_id = $_GET['user_id'];
			$user_role->role_id = $_GET['id'];
			$user_role->user_role_grant = 0;
			
			$user_role->save();
			$user = new user($_GET['user_id']);
			$group = new group($user->user_fk_cat_id);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_group_id = $_GET['user_id'];
			$log_description = Notifications::revoke_group_role($group->group_name, $role->role_alias,Auth::getLoggedInUserId(),$user->user_role, $role->role_description);
			$log->save();
			
			$this->setSuccessMessage('The group role has been successfully denied.');
			$this->redirect('user/view_user_role?id='.$_GET['user_id']);
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function delete_user_role_getAction() {
		try{
			if ((!isset($_GET['id']) || !is_numeric($_GET['id'])) || (!isset($_GET['user_id']) || !is_numeric($_GET['user_id']))){
				throw new exception();
			}
			$user_role = new user_role($_GET['id']);
			$role = new role($user_role->role_id);
			$user = new user($user_role->user_id);
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_user_id = $user->user_id;
			$log_description = Notifications::revoke_user_role($role->role_alias,Auth::getLoggedInUserId(),$user->user_role, $role->role_description);
			$log->save();
			
			$user_role->deleteMe();
			
			$this->setSuccessMessage('The user role has been successfully denied.');
			$this->redirect('user/view_user_role?id='.$_GET['user_id']);
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
}



