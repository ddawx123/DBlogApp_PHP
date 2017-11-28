<?php
// 初始化应用目录全局变量
define('APP_PATH',dirname(__FILE__).'/');
// 导入基类设置全局运行环境
require_once(APP_PATH.'library/base.class.php');
// 实例化基类
$App = Base::getInstance();
// 调用基类Api Launcher
$App->Load('api');