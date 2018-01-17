<?php
/**
 * 网站背景音乐管理组件封装
 * @package DingStudio/BlogAPP
 * @subpackage MusicLibrary
 * @author David Ding
 * @copyright 2012-2018 DingStudio All Rights Reserved
 */

class WebMusic {

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
     * 系统初始化
     */
    public function Load() {
        $config = APP_PATH.'data/music.json';
        $config_demo = APP_PATH."data/music.example.json";
        $autoplay = APP_PATH.'data/autoplay.dat';
        if (@$_REQUEST['output'] == 'js') {
            header('Content-Type: application/x-javascript; Charset=UTF-8');
            $js = 'var playlist = '.file_get_contents($config).';';
            if (file_exists($autoplay)) {
                $js .= 'var isRotate = true;';
                $js .= 'var autoplay = true;';
            }
            else {
                $js .= 'var isRotate = true;';
                $js .= 'var autoplay = false;';
            }
            echo $js;
            exit();
        }
        if (!Base::getInstance()->isLogin()) {
            Base::getInstance()->redirect('./index.php?c=login&callback='.urlencode($_SERVER['REQUEST_URI']), 0);
            exit();
        }
        else if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['method'])) {
            header('Content-Type: application/json; Charset=UTF-8');
            switch($_POST['method']) {
                case 'autoplay':
                if ($_POST['autoplay'] == 'yes') {
                    $result = file_put_contents($autoplay,'ok');
                    if (!$result) {
                        $arr = array('code'=>-7,'message'=>'文件写入失败');
                        echo json_encode($arr);
                        exit();
                    }
                    else {
                        $arr = array('code'=>0,'message'=>'操作成功结束');
                        echo json_encode($arr);
                        exit();
                    }
                }
                else {
                    $result = unlink($autoplay);
                    if (!$result) {
                        $arr = array('code'=>-7,'message'=>'文件写入失败');
                        echo json_encode($arr);
                        exit();
                    }
                    else {
                        $arr = array('code'=>0,'message'=>'操作成功结束');
                        echo json_encode($arr);
                        exit();
                    }
                }
                break;
                case 'upgrade':
                $result = file_put_contents($config, $_POST['data']);
                if (!$result) {
                    $arr = array('code'=>-7,'message'=>'文件写入失败');
                    echo json_encode($arr);
                    exit();
                }
                else {
                    $arr = array('code'=>0,'message'=>'操作成功结束');
                    echo json_encode($arr);
                    exit();
                }
                break;
                case 'factory':
                if (!file_exists($config_demo)) {
                    $arr = array('code'=>-51,'message'=>'配置范例文件丢失');
                    echo json_encode($arr);
                    exit();
                }
                else if (file_exists($config)) {
                    $result = unlink($config);
                    if (!$result) {
                        $arr = array('code'=>-7,'message'=>'文件写入失败');
                        echo json_encode($arr);
                        exit();
                    }
                    $demoInfo = file_get_contents($config_demo);
                    $result = file_put_contents($config, $demoInfo);
                    if (!$result) {
                        $arr = array('code'=>-7,'message'=>'文件写入失败');
                        echo json_encode($arr);
                        exit();
                    }
                    else {
                        $arr = array('code'=>0,'message'=>'操作成功结束');
                        echo json_encode($arr);
                        exit();
                    }
                }
                else {
                    $demoInfo = file_get_contents($config_demo);
                    $result = file_put_contents($config, $demoInfo);
                    if (!$result) {
                        $arr = array('code'=>-7,'message'=>'文件写入失败');
                        echo json_encode($arr);
                        exit();
                    }
                    else {
                        $arr = array('code'=>0,'message'=>'操作成功结束');
                        echo json_encode($arr);
                        exit();
                    }
                }
                break;
                default:
                require_once(APP_PATH.'template/inc/music.html');
                break;
            }
        }
        else {
            require_once(APP_PATH.'template/inc/music.html');
        }
    }
}