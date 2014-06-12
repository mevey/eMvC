<?php if (Auth::isLoggedIn()) { ?>
	<div class="header">
		<ul class="headermenu">
			<?php $selected_item = $this->menuItems; ?>
			<li <?php echo $selected_item['dash'] ?> ><a href="<?php t('baseUrl') ?>dashboard"><span class="icon icon-flatscreen"></span>Dashboard</a></li>
			
			<?php if (Registry::checkRole(Roles::LIST_ASSETS)) { ?>
				<li <?php echo $selected_item['asset'] ?> ><a href="<?php t('baseUrl')?>asset"><span class="icon icon-message"></span>Assets</a></li>
			<?php } ?>
			
			<?php if (Registry::checkRole(Roles::LIST_DEPARTMENT_ASSETS)) {?>
				<li <?php echo $selected_item['dept_asset'] ?> ><a href="<?php t('baseUrl')?>departmentasset"><span class="icon icon-message"></span>Department Assets</a></li>
			<?php } ?>
			
			<?php if (Registry::checkRole(Roles::LIST_CATEGORIES)) {?>
				<li <?php echo $selected_item['cat'] ?> ><a href="<?php t('baseUrl')?>category"><span class="icon icon-chart"></span>Categories</a></li>
			<?php } ?>
			
			<li <?php echo $selected_item['memo'] ?> ><a href="<?php t('baseUrl')?>memo"><span class="icon icon-speech"></span>Memos 
			<?php if (Auth::isLoggedIn() && isset($unreadMemos)) {
				echo ' ('.$unreadMemos.')';
			} 
			?>
			</a></a></li>
		
			<li <?php echo $selected_item['employeerequest'] ?> ><a href="<?php t('baseUrl')?>employeerequest"><span class="icon icon-message"></span>Requests
			<?php if (Auth::isLoggedIn() && Registry::checkRole(Roles::LIST_INHOUSE_REQUESTS) && isset($unreadForHodRequests)) { echo ' ('.$unreadForHodRequests.')'; } ?>
			</a></li>
			
			<?php if (Registry::checkRole(Roles::LIST_DEPARTMENT_REQUESTS)) {?>
			<li <?php echo $selected_item['departmentrequest'] ?> ><a href="<?php t('baseUrl')?>departmentrequest"><span class="icon icon-message"></span> Department Requests
			<?php if (Auth::isLoggedIn() && isset($unreadForDvcRequests)) { echo ' ('.$unreadForDvcRequests.')'; } ?>
			</a></li>
			<?php } ?>
			
			<?php if (Registry::checkRole(Roles::LIST_USERS) || Registry::checkRole(Roles::LIST_DEPARTMENT_USERS)) {?>
				<li <?php echo $selected_item['user'] ?> ><a href="<?php t('baseUrl')?>user"><span class="icon icon-chart"></span>Users</a></li>
			<?php } ?>
			
			<?php if (Registry::checkRole(Roles::LIST_MAINTENANCE_REQUESTS)) {?>
				<li <?php echo $selected_item['maintenance'] ?> ><a href="<?php t('baseUrl')?>maintenance"><span class="icon icon-pencil"></span>Maintenance</a></li>
			<?php } ?>
			
			<li <?php echo $selected_item['myasset'] ?> ><a href="<?php t('baseUrl')?>myasset"><span class="icon icon-speech"></span>My assets</a></li>
			
			<?php if (Registry::checkRole(Roles::LIST_LOGS)) {?>
			<li <?php echo $selected_item['log'] ?> ><a href="<?php t('baseUrl')?>log"><span class="icon icon-chart"></span>Logs</a></li>
			<?php } ?>
			
			
			<li <?php echo $selected_item['myprofile'] ?> ><a href="<?php echo $baseUrl.'myprofile'; ?>"><span class="icon icon-pencil"></span>My Profile</a></li>
			
			<?php if (Registry::checkRole(Roles::VIEW_REPORTS)) {?>
			<li <?php echo $selected_item['reports'] ?> ><a href="<?php echo $baseUrl.'report'; ?>"><span class="icon icon-chart"></span>Reports</a></li>
			<?php } ?>
			<?php if (Registry::checkRole(Roles::VIEW_MAP)) {?>
			<!--<li <?php echo $selected_item['map'] ?> ><a href="<?php echo $baseUrl.'map'; ?>"><span class="icon icon-chart"></span>Map</a></li>-->
			<?php } ?>
		</ul>
	</div><!--header-->
<?php } ?>

<?php if (isset($info_messages) && is_array($info_messages) && count($info_messages) > 0 ) { ?>
<div class="notibar msginfo">
	<a class="close"></a>
	<?php 
		foreach ($info_messages as $msg) {
			echo '<p>'.$msg.'</p><br/>';
		}
	?>
</div><!-- notification msginfo -->
<?php } ?>

<?php if (!empty($success_messages)) { ?>
<div class="notibar msgsuccess">
	<a class="close"></a>
	<p>
	<?php echo '<p>'.$success_messages.'</p>'; ?>
	</p>
</div><!-- notification msgsuccess -->
<?php } ?>

<?php if (!empty($alert_messages)) { ?>
<div class="notibar msgalert">
	<a class="close"></a>
	<?php echo '<p>'.$alert_messages.'</p>'; ?>
</div><!-- notification msgalert -->
<?php } ?>

<?php if (!empty($info_messages)) { ?>
<div class="notibar msginfo">
	<a class="close"></a>
	<?php echo '<p>'.$info_messages.'</p>'; ?>
</div><!-- notification msgalert -->
<?php } ?>

<?php if (!empty($error_messages)) { ?>
<div class="notibar msgerror">
	<a class="close"></a>
	<?php echo '<p>'.$error_messages.'</p>'; ?>
</div><!-- notification msgerror -->
<?php } ?>

<?php if (!empty($announcement_messages)) { ?>
<div class="notibar announcement">
	<a class="close"></a>
	<h3>Announcement</h3>
	<?php echo '<p>'.$announcement_messages.'</p>'; ?>
</div><!-- notification announcement -->
<?php } ?>
