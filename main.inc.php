<?php
/*
Plugin Name: custom import 导入
Version: 0.0.1
Description: 自定义导入图片
Plugin URI:
Author:l3n641
Author URI: http://piwigo.org
Has Settings: true
*/
if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}
define('CUSTOM_IMPORT_PATH' , PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');

/** Tour sended via $_POST or $_GET**/
if ( isset($_REQUEST['submited_tour_path']) and defined('IN_ADMIN') and IN_ADMIN )
{
  check_pwg_token();
  pwg_set_session_var('tour_to_launch', $_REQUEST['submited_tour_path']);
  global $TAT_restart;
  $TAT_restart=true;
}
elseif ( isset($_GET['tour_ended']) and defined('IN_ADMIN') and IN_ADMIN )
{
  pwg_unset_session_var('tour_to_launch');
}








?>
