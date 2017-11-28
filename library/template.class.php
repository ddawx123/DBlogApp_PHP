<?php
/**
 * Template模板引擎类/界面渲染
 * @package DingStudio/BlogAPP
 * @subpackage TemplateLibrary
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

class Tpl {

    /**
     * 载入模板并渲染页面
     * @param array $tplList 模板文件集合
     */
    private function Load($tplList = null) {
        require(APP_PATH.'library/global.inc.php');
        $tplPath = dirname(__FILE__).'/../template/';
        if ($tplList == null || !is_array($tplList)) {
            header('Content-Type: text/plain; Charset=UTF-8');
            echo '模板引擎运行发生异常，现已中止整个程序。';
            die();
        }
        header('Content-Type: text/html; Charset=UTF-8');
        $rows = count($tplList, COUNT_NORMAL);
        for ($i = 0; $i < $rows; $i++) {
            $tplfile = $tplList[$i];
            if (!file_exists($tplPath.$tplfile)) {
                echo '找不到模板文件：'.$tplPath.$tplfile;
                die();
            }
            else {
                include($tplPath.$tplfile);
            }
        }
    }

    /**
     * 模板引擎外部启动入口
     * 从外部类实例化本类后直接调用该方法即可启动页面渲染
     */
    public function initView() {
        if (!isset($_REQUEST['c'])) {
            $tplList = array('header.html','home.html','footer.html');
            self::Load($tplList);
        }
        else {
            switch (@$_REQUEST['c']) {
                case 'detail':
                break;
                case 'manager':
                break;
                case 'register':
                $tplList = array('register.html');
                self::Load($tplList);
                break;
                case 'login':
                break;
                case 'findpwd':
                break;
                case 'index':
                $tplList = array('header.html','home.html','footer.html');
                self::Load($tplList);
                break;
                case 'notfound':
                break;
                default:
                break;
            }
        }
    }
}