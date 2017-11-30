<?php
/**
 * Base基类/主程序入口
 * @package DingStudio/BlogAPP
 * @subpackage BaseLibrary
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

class Base {

    static private $_instance = null; // 定义空实例

    /**
     * 构造函数
     */
    private function __construct() {
        header('Product: DingStudio Cloud Platform');
        $this->checkConf(); //开箱即用拦截器（OOBE）
        if (!defined('APP_PATH')) { //判断APP_PATH常量是否在入口文件正确定义
            header('Content-Type: text/plain; Charset=UTF-8');
            echo '非常抱歉，由于您的应用程序未被正确配置，现无法启动。技术支持信息显示如下：Before you load base class, please set the APP_PATH constant first.';
            exit();
        }
        if (!session_id()) { //按需全局启用Session机制
            session_start();
        }
        require_once(APP_PATH.'library/global.inc.php'); //载入全局函数模块
    }

    /**
     * 统一预留实例化入口
     * @return instance 实例化对象
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 全局初始化
     * @param string $mod 启动方式
     */
    public function Load($mod = 'view') {
        switch ($mod) {
            case 'view':
            require_once(APP_PATH.'library/template.class.php');
            $view = new Tpl();
            $view->initView();
            break;
            case 'api':
            require_once(APP_PATH.'library/api.class.php');
            API::getInstance()->Router();
            break;
            case 'ueditor':
            require_once(APP_PATH.'library/ueditor.class.php');
            Ueditor::getInstance()->Load();
            break;
            default:
            header('Content-Type: text/plain; Charset=UTF-8');
            echo 'Exception: Could not fount this request router, please try again later.';
            break;
        }
    }

    /**
     * 配置文件检查与初始化
     * @return null
     */
    private function checkConf() {
        if (!file_exists(APP_PATH.'data/config.json')) {
            header('Content-Type: text/html; Charset=UTF-8');
            include(APP_PATH.'template/install.html');
            exit();
        }
    }

    /**
     * 请求重定向模型
     * @param string $url URL字串
     * @return boolean 响应结果
     */
    public static function redirect($url = null, $timeout = 1) {
        if ($url == null) {
            return false;
        }
        else {
            header('Content-Type: text/plain; Charset=UTF-8');
            header('refresh:'.$timeout.'; url='.$url);
            echo 'Redirecting now, please wait ...';
            exit();
        }
    }

    /**
     * 检查用户是否已经登录
     */
    public static function isLogin() {
        if (!isset($_SESSION['username']) || !isset($_SESSION['token']) || !isset($_COOKIE['username']) || !isset($_COOKIE['token']) || $_COOKIE['username'] != sha1($_SESSION['username']) || $_COOKIE['token'] != sha1($_SESSION['token'])) {
            return false;
        }
        else {
            return true;
        }
    }
}