<?php
/**
 * Base基类/主程序入口
 * @package DingStudio/BlogAPP
 * @subpackage BaseLibrary
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

class Base {

    /**
     * 全局初始化
     * @param string $mod 启动方式
     */
    public function Load($mod = 'view') {
        $this->checkConf();
        switch ($mod) {
            case 'view':
            //TODO Something
            $Model->View();
            break;
            case 'api':
            require_once(APP_PATH.'library/api.class.php');
            API::getInstance()->Router();
            break;
            default:
            break;
        }
    }

    /**
     * 配置文件检查与初始化
     * @return null
     */
    private function checkConf() {
        if (!file_exists(APP_PATH.'data/config.json')) {
            include(APP_PATH.'template/install.html');
            exit();
        }
    }

    /**
     * 请求重定向模型
     * @param string $url URL字串
     * @return boolean 响应结果
     */
    public function redirect($url = null) {
        if ($url == null) {
            return false;
        }
        else {
            header('Content-Type: text/plain; Charset=UTF-8');
            header('refresh:1; url='.$url);
            echo 'Redirecting now, please wait ...';
            return true;
        }
    }
}