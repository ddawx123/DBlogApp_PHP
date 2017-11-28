<?php
// 初始化应用目录全局变量
define('APP_PATH',dirname(__FILE__).'/');
// 导入基类设置全局运行环境
require_once(APP_PATH.'library/base.class.php');
// 实例化基类
$App = Base::getInstance();
// 调用基类Api Launcher
$App->Load('view');
/*
<?php
$blog_keywords = '123';
$blog_title = '12x3';
$blog_desc = '1x4';
$upTime = '2012';
$blog_author = 'David Ding';

$rss_url = './atom.xml';
include('template/header.html');
include('template/home.html');
include('template/footer.html');
*/
