<?php
/**
 * Ueditor客户组件封装
 * @package DingStudio/BlogAPP
 * @subpackage UeditorLibrary
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

class Ueditor {

    static private $_instance = null; // 定义空实例

    /**
     * 构造函数
     */
    private function __construct() {}
    
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
     * Ueditor初始化
     */
    public function Load() {
        if (!Base::getInstance()->isLogin()) {
            Base::getInstance()->redirect('./index.php?c=login&callback='.urlencode($_SERVER['REQUEST_URI']), 0);
            exit();
        }
        require_once(APP_PATH.'template/inc/ueditor.html');
    }
}