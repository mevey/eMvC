<?php
class MemoController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('memo');
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
		$search = pdo_db::getInstance()->quote($this->getSession('search'));
		if (empty($search)) {
			return 'memo_to_fk_user_id = '.Auth::getLoggedInUSerId();
		}
		return 'memo_to_fk_user_id = '.Auth::getLoggedInUSerId().' OR (memo_data_title LIKE '.$search.' OR memo_data_body LIKE '.$search.')';
	}
	
	private function orderClause() {
		$order_by = $this->getSession('order_by');
		$order_direction = strtoupper($this->getSession('order_direction'));
		if (empty($order_by)) {
			return 'memo_id DESC';
		} else if (!empty($order_direction) && ($order_direction == 'ASC' || $order_direction == 'DESC')){
			$order_by = $order_by.' '.$order_direction;
		}
		
		return $order_by;
	}
	
	public function index_getAction() {
		$memo = new memo();
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		
		$t = $p.'memo as A INNER JOIN '.$p.'memo_data AS B ON A.memo_fk_memo_data_id = B.memo_data_id INNER JOIN '.$p.'user as D ON B.memo_data_from_fk_user_id = D.user_id';
		$this->template->memo_data = $memo->getRows($t,'*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$this->template->load('memo_in.html');
	}
	
	public function index_postAction() {
		$this->template->load('memo_in.html');
	}
	
	public function compose_getAction() {
		$this->template->dept_data = db::select('department','*');
		
		$this->template->load('memo_add.html');
	}
	
	public function compose_postAction() {
		$memo_data = new memo_data();
		
		$memo_data->memo_data_title = $_POST['title'];
		$memo_data->memo_data_body = $_POST['elm1'];
		$memo_data->memo_data_date = Registry::getDate();
		$memo_data->memo_data_from_fk_user_id = Auth::getLoggedInUserId();
		$id = $memo_data->save();
		
		if ($_POST['user'] == -1) {
			$users = db::select('user','user_id','user_fk_dept_id = '.$_POST['dept']);
			foreach($users as $user){
				if ($user['user_id'] == Auth::getLoggedInUserId()) continue;
				$memo = new memo();
				$memo->memo_to_fk_user_id = $user['user_id'];
				$memo->memo_fk_memo_data_id = $id;
				$memo->memo_read = 0;
				$memo->save();
			}
		} else {
			$memo = new memo();
			$memo->memo_to_fk_user_id = $_POST['user'];
			$memo->memo_fk_memo_data_id = $id;
			$memo->memo_read = 0;
			$memo->save();
		}
		
		$this->setSuccessMessage('Your message has been sent successfully.');
		$this->redirect('outgoingmemo');
	}
	
	public function read_memo_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$memo = new memo($_GET['id']);
			
			$memo_data = new memo_data($memo->memo_fk_memo_data_id);
			
			$this->template->memo_data_content = new memo_data($memo_data->memo_data_id);
			$this->template->user_data = new user($memo_data->memo_data_from_fk_user_id);
			
			$memo->memo_read = 1;
			$memo->save();
			
			$this->template->load('ajax/blog/readmemo.html');
		}  catch(Exception $err) {
			//$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			//echo $err;
			return;
		} 
	}
	
	public function read_memo_postAction() {
		$this->read_memo_getAction();
	}
	
	public function get_dept_users_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$this->template->dept_data = db::select('department', 'department_id = '.$_GET['id']);
			$this->template->user_data = db::select('user', '*');
			$this->template->load('memo_add.html');
		}  catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		} 
	}
	
	public function news_getAction(){
		$this->template->news_data = db::select('news', '*','','news_id DESC');
		$this->template->load('news.html');
	}
	
	public function publish_getAction(){
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$news = new news($_GET['id']);
			if ($news->news_publish == 0){
				$news->news_publish = 1;
			} else {
				$news->news_publish = 0;
			}
			$news->save();
			$this->news_getAction();
		}  catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		} 
	}
	
	public function news_add_postAction() {
		$news = new news();
		$news->news_title = $_POST['title'];
		$news->news_body = $_POST['elm1'];
		$news->news_date = Registry::getDate();
		$news->news_publish = 1;
		$news->save();
		$this->setSuccessMessage('The news item has been posted successfully.');
		$this->news_getAction();
		
	}
	
	public function get_users_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$this->template->user_data = db::select('user','*','user_fk_dept_id ='.$_GET['id']);
			
			$this->template->load('ajax/blog/memousers.html');
		} catch (Exception $err) {
			echo 'Sorry, some error occurred. Please try again later';
		}
		exit;
	}
}


