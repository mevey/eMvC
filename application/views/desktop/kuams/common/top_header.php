	<div class="topheader">
		<div class="left">
			<img src='<?php t('templatePathUrl') ?>images/logo.png' style='height:40px;float:left;'/>
			<h1 class="logo">KU.<span>AMS</span></h1>
			<span class="slogan">Integrity. Effectiveness</span>
			
			<br clear="all" />
			
		</div><!--left-->
		<?php if (Auth::isLoggedIn()) { 
			try{
				$name = Auth::getLoggedInUserName();
				$id = Auth::getLoggedInUserId();
			} catch(Exception $err) {
				$name = 'N/A';
				$id = 0;
			}
		?>
		<div class="right">
			<div class="userinfo">
			<div style="float:left;color:white;margin-bottom:0px;padding-right:2px;">Last Login:<br/> <?php echo Auth::getLoggedInUserData('user_last_login') ?></div>
				<img src="<?php echo Registry::getUserAvatarUrl($id); ?>" height="25px" alt="" />
				<span></span>
			</div><!--userinfo-->
			
			<div class="userinfodrop">
				<div class="avatar">
					<a href=""><img width="120px" src="<?php echo Registry::getUserAvatarUrl($id); ?>" alt="" /></a>
				</div><!--avatar-->
				<div class="userdata">
					<h4><?php echo $name;; ?></h4>
					<ul>
						<li><a href="<?php echo $baseUrl.'myprofile'; ?>">My Profile</a></li>
						<li><a href="<?php echo $baseUrl.'myprofile/log_out'; ?>">Sign Out</a></li>
					</ul>
				</div><!--userdata-->
			</div><!--userinfodrop-->
		</div><!--right-->
		<?php } else { ?>
			<div class="right">
				<a href="<?php echo $baseUrl; ?>login"><button class="stdbtn">Login</button></a>
			</div>
		<?php } ?>
	</div><!--topheader-->
