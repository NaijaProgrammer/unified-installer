<?php
$unified_installer_dir = dirname(__FILE__); 
copy($unified_installer_dir. '/unified-installer-check.php', './unified-installer-check.php');
header("location:basic-config-setup.php");
exit;
?>