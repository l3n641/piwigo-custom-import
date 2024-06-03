<?php
set_time_limit(0);

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
include_once(CUSTOM_IMPORT_PATH . 'include/product.php');
include_once(CUSTOM_IMPORT_PATH . 'include/import.php');

global $template;

if (empty($_FILES["file"]["name"])) {
    $template->set_filename('plugin_admin_content', dirname(__FILE__) . '/tpl/admin.tpl');
    $template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
    return;
}

$file = $_FILES['file']['tmp_name'];
$galleries_url = trim($_POST["galleries_url"]);

if (!is_dir($galleries_url)) {
    $template->assign("error", "相册目录不存在");
    $template->set_filename('plugin_admin_content', dirname(__FILE__) . '/tpl/admin.tpl');
    $template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
    return false;
}


$import = new Import($file, $galleries_url);
$result = $import->run();
$template->assign('result', $result);
$template->set_filename('plugin_admin_content', dirname(__FILE__) . '/tpl/admin.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');







