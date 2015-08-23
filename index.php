<?php
require_once dirname(dirname(dirname(__FILE__))). '/pcl/ini.php';

SessionManipulator::start_session();
define("INSTALLER_INDEX", true);
define("NL", "\r\n");

/** 
* define and initialize default values for form variables
*/
$db_server     = '';
$db_user       = '';
$db_pass       = '';
$db_name       = '';
$tables_prefix = 'mo_';
$installation_complete_url  = '';
$status_message = '';

/**
* Get the plugins to install from the current directory.
* The current directory is the directory housing the installer directory
*/
$unified_installer_dir = dirname(__FILE__); 
$plugins_dir           = str_replace( '\\', '/', dirname($unified_installer_dir) );
$directories           = DirectoryInspector::get_directory_contents($plugins_dir, 'DIRECTORIES_ONLY', false);
$eligible_plugins      = array(); //every plugin in the directory
$plugins_to_install    = array(); //plugins selected by the user to install, this is passed in a session variable to finalize.php

foreach( $directories AS $directory)
{
	$config_filename = $directory. '-app-config.php';
	
	if( ($directory != 'unified-installer') && !file_exists($plugins_dir. '/'. $directory. '/'. $config_filename) )
	{
		$eligible_plugins[] = $directory;
	}
}

/**
* declare and set variables needed by installer-form.php
*/
$user_manager_directory_exists = $session_manager_directory_exists = false;
$available_plugins_str  = "<div><label>Choose Apps to Install</label><br/>". "\r\n";

foreach($eligible_plugins AS $plugin_directory)
{	
	if($plugin_directory == 'session-manager')
	{
		$session_manager_directory_exists = true;
	}
	if($plugin_directory == 'user-manager')
	{
		$user_manager_directory_exists = true;
	}
	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		/**
		* set values of variables needed by installer-form.php
		*/
		if(isset($_POST['plugins'][$plugin_directory]))
		{
			$plugins_to_install[] = $plugin_directory;
		}
		$state = isset($_POST['plugins'][$plugin_directory]) ? 'checked="checked"' : '';
		$available_plugins_str .= $plugin_directory. " <input type=\"checkbox\" name=\"plugins[$plugin_directory]\" $state /><br/>". "\r\n";
	}
	else
	{
		$plugins_to_install[] = $plugin_directory;
	}
}

$available_plugins_str .= "</div>";

if( ($_SERVER['REQUEST_METHOD'] == 'POST') || isset($_SESSION['unified-installer-plugin-installation-values']) ):

	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		foreach($_POST AS $key => $value)
		{
			$$key = ( !is_array($value) && is_string($value) ) ? trim($value) : $value;
		}
	}
	else if(isset($_SESSION['unified-installer-plugin-installation-values']))
	{
		foreach($_SESSION['unified-installer-plugin-installation-values'] AS $key => $value)
		{
			$$key = ( !is_array($value) && is_string($value) ) ? trim($value) : $value;
		}
	}
	
	$mat[0]['error_condition'] = empty($db_server);
	$mat[0]['error_message']   = 'Database server field cannot be empty';
	$mat[1]['error_condition'] = empty($db_user);
	$mat[1]['error_message']   = 'The Database user field cannot be empty';
	$mat[2]['error_condition'] = empty($db_name);
	$mat[2]['error_message']   = 'The Database name field cannot be empty';
	$mat[3]['error_condition'] = empty($plugins_to_install); //!isset($_POST['plugins']);
	$mat[3]['error_message']   = 'You must specify at least one plugin to install';
	$mat[4]['error_condition'] = empty($installation_complete_url);
	$mat[4]['error_message']   = 'Specify the URL to redirect to on successful install';
	
	$db = new MySql();
	$connection_id = @$db->connect( $db_server, $db_user, $db_pass, $db_name );
	
	$mat[5]['error_condition'] = ( $connection_id === false ) || ( $connection_id < 0 );
	$mat[5]['error_message']   = 'Unable to connect to the database server. <br/> Make sure the database server name, username and password are correct';
	
	$validate = Validator::validate($mat);
	
	if($validate['error'])
	{
		$status_message = $validate['status_message'];
		require_once('installer-form.php');
		exit;
	}
    
	$content = "<?php". NL. NL.
	
               "require_once('". PCL_DIR. "/ini.php');". NL. NL. NL.
			  
			   "/**". NL. "* database server name".  NL. "*/". NL.
               "defined('DB_SERVER') or define('DB_SERVER', '$db_server');". NL. NL. NL.
			   
			   "/**". NL. "* database user name". NL. "*/". NL.
               "defined('DB_USER') or define('DB_USER', '$db_user');" . NL. NL. NL.
			   
			   "/**". NL. "* database user password". NL. "*/". NL.
               "defined('DB_PASS') or define('DB_PASS', '$db_pass');".      NL. NL. NL.
			   
			   "/**". NL. "* database name".     NL. "*/". NL.
               "defined('DB_NAME') or define('DB_NAME', '$db_name');". NL. NL. NL.
			   
			   "/**". NL. "* database tables prefix".        NL. "*/". NL.
               "defined('TABLES_PREFIX') or define('TABLES_PREFIX', '$tables_prefix');". NL. NL. NL.
			   
			   "/**". NL. "* File system path to installed plugins directory". NL. "*/". NL.
			   "defined('PLUGINS_DIR') or define('PLUGINS_DIR',   '$plugins_dir');". NL. "";
			   
	$config_file = new FileWriter('../unified-install.config.php', 'WRITE_ONLY');
	$config_file->write($content);
	  
	$_SESSION['plugins_to_install']        = Util::stringify($plugins_to_install); //used to pass the eligible plugins array to 'finalize.php'
	$_SESSION['installation_complete_url'] = $installation_complete_url; //ditto
	$paths = UrlInspector::get_path($plugins_dir. '/'. $eligible_plugins[0]. '/install');
	UrlManipulator::redirect($paths['http_path']);

endif; //end if $_SERVER['REQUEST_METHOD'] == 'POST'

require_once('installer-form.php');