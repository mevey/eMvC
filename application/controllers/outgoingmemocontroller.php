<?php
class OutgoingMemoController extends BaseController {
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
			return 'memo_data_from_fk_user_id = '.Auth::getLoggedInUserId();
		}
		return 'memo_data_from_fk_user_id = '.Auth::getLoggedInUserId().' OR memo_data_title LIKE '.$search.' OR memo_data_body LIKE '.$search.'';
	}
	
	private function orderClause() {
		$order_by = $this->getSession('order_by');
		$order_direction = strtoupper($this->getSession('order_direction'));
		if (empty($order_by)) {
			return 'memo_data_id DESC';
		} else if (!empty($order_direction) && ($order_direction == 'ASC' || $order_direction == 'DESC')){
			$order_by = $order_by.' '.$order_direction;
		}
		
		return $order_by;
	}
	
	public function index_getAction() {
		$memo_data = new memo_data();
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'memo_data INNER JOIN '.$p.'department ON memo_data_fk_dept_id = department_id';
		
		$this->template->memo_data = $memo_data->getRows($t,'*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$this->template->load('memo_out.html');
	}
	
	public function index_postAction() {
		$this->template->index_getAction();
	}
	
	public function read_out_memo_postAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			
			$memo_data = new memo_data($_GET['id']);
			
			$this->template->memo_data_content = new memo_data($memo_data->memo_data_id);
			$this->template->user_data = new user($memo_data->memo_data_from_fk_user_id);
			
			$this->template->load('ajax/blog/readmemo.html');
		}  catch(Exception $err) {
			//$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			echo $err;
			return;
		} 
	}
}







