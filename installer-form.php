<?php defined('INSTALLER_INDEX') or header("location: ."); ?>
<!DOCTYPE html>
<html>
 <head>
  <title>Michael Orji's Apps Installer</title>
  <!--<link rel="stylesheet" type="text/css" href="styles/bootstrap.min.css" />-->
  <style type="text/css">
     form div{ margin-bottom: 20px; }
	 form div label{ float: left; width: 250px; }
     form input[type="text"], form input[type="password"]{ width: 450px; height: 20px;}
  </style>
 </head>
 <body>
    <div><?php echo $status_message; ?></div>
	<form method="post" action="">
		<div><input type="text"     name="db_server"  value="<?php echo $db_server; ?>"    placeholder="Database Server"/></div>
		<div><input type="text"     name="db_user"    value="<?php echo $db_user;   ?>"    placeholder="Database User"/></div>
		<div><input type="text"     name="db_pass"    value="<?php echo $db_pass;   ?>"    placeholder="Database User Password"/></div>
		<div><input type="text"     name="db_name"    value="<?php echo $db_name;   ?>"    placeholder="Database Name"/></div>
		<div><input type="text"     name="tables_prefix" value="<?php echo $tables_prefix; ?>" placeholder="Tables Prefix"/></div>

		<?php 
		/** 
		* Load the checkboxes representing the plugins to install, variable passed initially from 'index.php', i.e before form submission
		*/
		echo $available_plugins_str; 
		?>

		<div><input type="text" name="installation_complete_url" value="" placeholder="URL to redirect on installation completion" /></div>
		<div><input type="submit" value="Run Installer"/></div>
	</form>
	<!--
	<script type="text/javascript" src="scripts/jquery.js"></script>
	<script type="text/javascript" src="scripts/jquery-ui.js"></script>
	<script type="text/javascript" src="scripts/bootstrap.min.js"></script>
	-->
</body>
</html>