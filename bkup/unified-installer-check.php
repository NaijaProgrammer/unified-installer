<?php
$unified_installer_directory   = dirname(dirname(dirname(__FILE__))). '/unified-installer';
$unified_installer_config_file = $unified_installer_directory. '/unified-install.config.php';
if( !file_exists($unified_installer_config_file) )
{
	if( $_SERVER['REQUEST_METHOD'] != 'POST' )
	{ 
		UrlManipulator::redirect("."); 
	}
}
else
{
	define("NL", "\r\n");
	require_once($unified_installer_config_file);
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		foreach($_POST AS $key => $value)
		{ 
			$$key = trim($value); 
		}
		$db_server        = empty($db_server)        ? 'localhost'     : $db_server;
		$db_name          = empty($db_name)          ? 'multi-manager' : $db_name;
		$db_user          = empty($db_user)          ? 'root'          : $db_user;
		$db_pass          = empty($db_pass)          ? ''              : $db_pass;
		$tables_prefix    = empty($tables_prefix)    ? 'mm_'           : $tables_prefix;
		$error_message    = '';
		if( !@mysql_connect($db_server, $db_user, $db_pass) ){
			$error_message = "Unable to connect to the database server. <br> Make sure the database server name, username and password are correct";
			require_once('forms/basic_configuration_form.inc');
			exit;
		}
		if(!mysql_select_db($db_name)){
			$error_message  = "Unable to select the database.";
			$error_message .= "<ul>".
                    "<li>Make sure it exists</li>".
                    "<li>Make sure the user have the appropriate permissions to use the database</li>".
                    "</ul>";
			require_once('forms/basic_configuration_form.inc');
			exit;
		}
	}
}

?>