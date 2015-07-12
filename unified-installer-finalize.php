<?php
defined('PHPUTIL_DIR') or die('illegal or unauthorized access');
require_once(PHPUTIL_DIR. "/ini.php");
SessionManipulator::start_session();

$plugins_to_install             = Util::unstringify($_SESSION['plugins_to_install']);
$current_plugin                 = array_shift($plugins_to_install);
if(file_exists('unified-installer-check.php'))
{
	unlink('unified-installer-check.php');
}
if( empty($plugins_to_install) )
{  
	$final_redirect_url = $_SESSION['installation_complete_url'];
	unset($_SESSION['plugins_to_install']);
	unset($_SESSION['installation_complete_url']);
	UrlManipulator::redirect($final_redirect_url);
}

$_SESSION['plugins_to_install'] = Util::stringify($plugins_to_install);
$plugins_to_install             = Util::unstringify($_SESSION['plugins_to_install']);
$next_plugin                    = !empty($plugins_to_install) ? $plugins_to_install[0] : null;
	
$unified_installer_dir          = dirname(__FILE__); 
$plugins_dir                    = str_replace( '\\', '/', dirname($unified_installer_dir) );

$plugin_directory_full_path     = $plugins_dir. '/'. $next_plugin;
$plugin_install_directory       = $plugin_directory_full_path. '/install';
$plugin_config_file             = $next_plugin. '-app-config.php';
$plugin_is_installed            = file_exists($plugin_directory_full_path. '/'. $plugin_config_file);
	
if( !$plugin_is_installed )
{
	$plugin_install_paths = UrlInspector::get_path($plugin_directory_full_path. '/install');
	$plugin_install_url  = $plugin_install_paths['http_path'];
		
	if(UrlInspector::url_exists($plugin_install_url))
	{
		UrlManipulator::redirect($plugin_install_url);
	}
}

?>