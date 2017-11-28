<?php
// 初始化应用目录全局变量
define('APP_PATH',dirname(__FILE__).'/');

if (!isset($_POST['sqlsrv']) || !isset($_POST['sqlusr']) || !isset($_POST['sqlpwd']) || !isset($_POST['sqlmdb']) || !isset($_POST['blog_name']) || !isset($_POST['blog_description']) || !isset($_POST['blog_author'])) {
    $arr = array(
        'code'  =>  405,
        'message'   =>  '无效参数'
    );
    out($arr);
}
else {
    $config = array(
        'sqlsrv'    =>  $_POST['sqlsrv'],
        'sqlusr'    =>  $_POST['sqlusr'],
        'sqlpwd'    =>  $_POST['sqlpwd'],
        'sqlmdb'    =>  $_POST['sqlmdb'],
        'bloginfo'  =>  array(
            'blog_name'  => $_POST['blog_name'],
            'blog_description'  =>  $_POST['blog_description'],
            'blog_author'   =>  $_POST['blog_author']
        ),
        'SSO'   =>  array(
            'cas_server_addr'    =>  'cas.example.org',
            'cas_server_port'   =>  80,
            'cas_server_path'   =>  'cas',
            'cas_server_auth'   =>  false
        ),
        'buildTime' =>  date('YmdHis',time())
    );
    $result = file_put_contents(APP_PATH.'data/config.json', json_encode($config));
    if (!$result) {
        $arr = array(
            'code'  =>  500,
            'message'   =>  '数据写入失败，可能是由于您的空间处于只读状态！请尝试修改服务器文件系统配置。'
        );
        out($arr);
    }
    else {
        $result = rename(APP_PATH.'process.php',APP_PATH.'process.php.bak');
        if ($result) {
            $arr = array(
                'code'  =>  200,
                'message'   =>  '操作成功结束，本程序将自动屏蔽。如需再次使用，请手动登录服务器重新修改本程序文件后缀名！'
            );
        }
        else {
            $arr = array(
                'code'  =>  200,
                'message'   =>  '操作成功结束，但由于服务器安全保护机制本程序无法自动屏蔽入口。为了安全考虑，请手动登录服务器移除或重命名本程序（process.php）！'
            );
        }
        out($arr);
    }
}

function out($data = array()) {
    header('Content-Type: application/json; Charset=UTF-8');
    die(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
}