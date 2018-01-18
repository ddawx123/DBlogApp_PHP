<?php
/**
 * 全局功能模块封装
 * 在此定义的所有功能变量/方法
 * 将被允许在整个程序的任何位置调用
 * 温馨提示：
 * 本文件可被正常调用的前提是
 * 发起调用的PHP文件已经导入Base基类
 * @package DingStudio/BlogAPP
 * @subpackage GlobalLibrary
 * @author David Ding
 * @copyright 2012-2018 DingStudio All Rights Reserved
 */

if (version_compare(PHP_VERSION, '5.4.0') < 0) {
    header('Content-Type: text/plain; Charset=UTF-8');
    die('PHP执行环境版本过低，当前版本: ' . PHP_VERSION . "。系统最低需求：PHP(5.4.0)，兼容最新PHP7！");
}

$config = file_get_contents(APP_PATH.'data/config.json');
$config = json_decode($config);

// Define DingStudio Cloud Platform Global Variable
$dcp = array(
    'blog_title'  =>  $config->bloginfo->blog_name,
    'blog_desc' =>  $config->bloginfo->blog_description,
    'blog_keywords' =>  $config->bloginfo->blog_description,
    'upTime'    =>  substr($config->buildTime,0,4),
    'blog_author'   =>  $config->bloginfo->blog_author,
    'rss_url'   =>  './api.php?action=getArticle&type=list'
);