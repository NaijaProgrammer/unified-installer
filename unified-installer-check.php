<?php
$unified_installer_config_file = dirname(dirname(dirname(__FILE__))). '/unified-install.config.php';
if( !file_exists($unified_installer_config_file) )
{
	if( $_SERVER['REQUEST_METHOD'] != 'POST' )
	{ 
		UrlManipulator::redirect("."); 
	}
}

?>