<?php
class DisposeAssetController extends BaseController {
	public function _beforeAction() {
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
	
	public function _afterAction() {
		
	}
	
	private function searchClause() {
		$search = $this->getSession('search');
		if (empty($search)) {
			return 'asset_state = -1 ';
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		return 'asset_state = -1 AND ( asset_model LIKE '.$search.' OR asset_tag_id LIKE '.$search.')';
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
		$asset = new asset();
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		
		$t = $p.'asset INNER JOIN '.$p.'asset_category ON '.$p.'asset.asset_fk_cat_id = '.$p.'asset_category.asset_category_id';
		$this->template->list_data = $asset->getRows($t,'*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$returned_asset = new returned_asset();
		$id = Auth::getLoggedInUserData('user_fk_dept_id');
		$t = $p.'asset INNER JOIN '.$p.'returned_asset ON '.$p.'asset.asset_id = '.$p.'returned_asset.returned_asset_asset_id';
		$this->template->count = $returned_asset->getRowCount($t,'returned_by_dept_id <> 0');
		
		$this->template->load('asset_dispose.html');
	}
	
	public function index_postAction() {
		$this->template->load('asset_dispose.html');
	}
	
	public function dispose_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$asset = new asset($_GET['id']);
			$asset->asset_state = 2	;
			$asset->save();
			
			$this->setSuccessMessage('The asset has been disposed successfully.');
			
			$this->redirect('disposeasset');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			
			$this->redirect('disposeasset');
			return;
		}
	}
	
	public function reinstate_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$asset = new asset($_GET['id']);
			$asset->asset_state = 0;
			$asset->save();
			
			$this->setSuccessMessage('The asset has been reinstated successfully.');
			
			$this->redirect('disposeasset');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			
			$this->redirect('disposeasset');
			return;
		}
	}
}







