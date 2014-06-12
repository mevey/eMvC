<html>
	<head>
		<title><?php echo $this->reg->config['template']['mobile_name'] ?></title>
	</head>
<body>
	<p><?php echo $message ?></p><br/>
	<p> :)</p><br/>
	<p> yeih</p><br/>
	<p><img src="<?php echo $templatePathUrl.'/site_images/me.jpg'; ?>" /></p>
	
	<form method="post" action="">
		<input name="studentName" />
		<input type="submit" value="Submit" />
	</form>
	
	<?php echo $footer ?>
</body>
</html>
