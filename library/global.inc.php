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
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

$config = file_get_contents(APP_PATH.'data/config.json');
$config = json_decode($config);

// Define DingStudio Cloud Platform Global Variable
$dcp = array(
    'blog_title'  =>  $config->bloginfo->blog_name,
    'blog_desc' =>  $config->bloginfo->blog_description,
    'blog_keywords' =>  $config->bloginfo->blog_description,
    'upTime'    =>  substr($config->buildTime,0,4),
    'blog_author'   =>  $config->bloginfo->blog_author
);
/*
$blog_keywords = '123';
$blog_title = '12x3';
$blog_desc = '1x4';
$upTime = '2012';
$blog_author = 'David Ding';

$rss_url = './atom.xml';*/