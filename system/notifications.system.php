<?php
class Notifications {
	
	public static function assign_user($model, $tag_id, $asset_id, $user_by,$user_by_post) {
		return 'You have been assigned a '.$model
		.' : Tag '.$tag_id.' by '.$user_by.', '.$user_by_post.'.';
	}
	public static function asset_assign_user($user_by,$user_by_post, $user_to) {
		return $user_to.' assigned this asset by '.$user_by.', '.$user_by_post.'.';
	}
	
	public static function revoke_user($model, $tag_id, $asset_id, $user_by,$user_by_post ,$user_to) {
		return 'You have been de-allocated an asset. Model: '.$model.' : Tag '.$tag_id.' by '.$user_by.', '.$user_by_post.'.';
	}
	
	public static function asset_revoke_user($user_by,$user_by_post ,$user_to) {
		return $user_to.' de-allocated this asset by '.$user_by.', '.$user_by_post.'.';
	}
	
	public static function assign_dept($model, $tag_id, $asset_id, $user_by, $user_by_post,$dept) {
		return 'Your department has been assigned a '.$model
		.' : Tag '.$tag_id.' by '.$user_by.', '.$user_by_post.'.';
	}
	
	public static function asset_assign_dept($user_by, $user_by_post,$dept) {
		return $dept.' department primarily assigned this asset by '.$user_by.', '.$user_by_post.'.';
	}
	
	public static function revoke_dept($model, $tag_id, $asset_id, $user_by,$user_by_post ,$user_to) {
		return 'Your department has been de-allocated an asset. Model: '.$model.' : Tag   '.$tag_id.' by '.$user_by.', '.$user_by_post.'.';
	}
	
	public static function asset_revoke_dept($user_by,$user_by_post ,$user_to) {
		return $dept.' department has been de-allocated this by '.$user_by.', '.$user_by_post.'.';
	}
	
	public static function add_user($user_name, $user_dept) {
		return 'A new user'.$user_name.' has been added to '.$user_dept.' department.';
	}
	
	public static function delete_user($user_name, $user_dept) {
		return 'The user'.$user_name.' has been removed from the  '.$user_dept.' department.';
	}
	
	public static function add_asset($asset_id,$tag_id, $model, $category) {
		return 'An asset, tag  :'.$tag_id.', Model:'.$model.'  has been added to the '.$category.' category.';
	}
	
	public static function asset_add_asset($category) {
		return 'Added to the '.$category.' category.';
	}
	
	public static function delete_asset($tag_id, $model, $category) {
		return 'An asset, tag  :'.$tag_id.', Model:'.$model.' has been deleted from the '.$category.' category.';
	}
	
	public static function add_dept_asset($asset_id,$tag_id, $model, $category, $dept_name) {
		return 'An asset, tag  :'.$tag_id.', Model:'.$model.' has been added to the '.$category.' category in the '.$dept_name.' department.';
	}
	
	public static function asset_add_dept_asset($category, $dept_name) {
		return 'Added to the '.$category.' category in the '.$dept_name.' department.';
	}
	
	public static function delete_dept_asset($tag_id, $model, $category, $dept_name) {
		return 'An asset, tag  :'.$tag_id.', Model:'.$model.' has been deleted from the '.$category.' category in the '.$dept_name.' department.';
	}
	
	public static function dispose_asset($tag_id, $model, $user_by) {
		return 'An asset, tag  :'.$tag_id.', Model:'.$model.' has been disposed.';
	}
	
	public static function asset_dispose_asset($tag_id, $model, $user_by) {
		return 'Disposed by '.$user_by;
	}
	
	public static function add_asset_category($name, $dep_rate) {
		return 'A new asset category: '.$name.' has been created with a depreciation rate of '.$dep_rate.'% p.a.';
	}
	
	public static function delete_asset_category($name) {
		return 'The asset category: '.$name.' has been deleted.';
	}
	
	public static function schedule_maintenance($model, $tag_id, $user_by, $dept, $id) {
		return 'An asset, Model: '.$model.' Tag  : '.$tag_id.' has been scheduled for maintenance by '.$user_by.' from the '.$dept.' department.'.
		'';
	}
	
	public static function repair_report($model, $tag_id, $user_by, $id) {
		return 'The asset, Model: '.$model.' Tag  : '.$tag_id.', has been repaired. ';
	}
	
	public static function asset_repair_report($user_by, $problem, $repair) {
		return 'Repaired by '.$user_by.'.<br/>Problem: '.$problem.'.<br/> Repair report: '.$repair;
	}
	
	public function add_group($name, $user_by) {
		return 'A new group, '.$name.', has been <b>added</b> by '.$user_by;
	}
	
	public function delete_group($name, $user_by) {
		return 'The group, '.$name.', has been <b>deleted</b> by '.$user_by;
	}
	
	
	public function add_group_role($name, $role, $user_by, $user_post, $role_description ) {
		return 'Your group, '.$name.', has been <b>granted</b> the role '.$role.' by '.$user_by.','.$user_post.'. <br/>Role description: '.$role_description;
	}
	
	public function revoke_group_role($name, $role, $user_by, $user_post, $role_description ) {
		return 'Your group, '.$name.', has had the role '.$role.' <b>revoked</b> by '.$user_by.','.$user_post.'.<br/> Role description: '.$role_description;
	}
	
	public function add_user_role($role, $user_by, $user_post, $role_description ) {
		return 'You have been <b>granted</b> the role '.$role.' by '.$user_by.','.$user_post.'.<br/> Role description: '.$role_description;
	}
	
	public function revoke_user_role($role, $user_by, $user_post, $role_description ) {
		return 'You have had the role '.$role.' <b>revoked</b> by '.$user_by.','.$user_post.'.<br/> Role description: '.$role_description;
	}
	
	public function approve_request($user_by, $user_post, $reference, $model) {
		return 'Your request for a '.$model.' , reference number '.$reference.' has been <b>approved</b> by '.$user_by.', '.$user_post;
	}
	
	public function reject_request($user_by, $user_post, $reference, $model) {
		return 'Your request for a '.$model.' , reference number '.$reference.' has been <b>rejected</b> by '.$user_by.', '.$user_post;
	}
	
	public function return_user_asset($tag_id, $model, $name) {
		return 'The asset, Tag Id: '.$tag_id.' Model: '.$model.' has been returned by '.$name;
	}
	
	public function approve_return($tag_id, $model) {
		return 'Your request to return asset tag id, <b>'.$tag_id.'</b> Model <b>'.$model.'</b> has been approved.';
	}
	public function decline_return($tag_id, $model) {
		return 'Your request to return asset tag id, <b>'.$tag_id.'</b> Model <b>'.$model.'</b> has been declined.';
	}
	
	public function asset_approve_return($name) {
		return 'Returned by '.$name;
	}
	
	public function bulk_add_asset($count, $category) {
		return $count.' assets have been added to the '.$category.' category';
	} 
}
