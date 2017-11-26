<?php
/**
 * API运行模式封装
 * Notice：本类已实现单实例设计模式
 * @package DingStudio/BlogAPP
 * @subpackage APILibrary
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

require_once(APP_PATH.'library/database.class.php');

class API {

    static private $_runtime = null; // 定义操作时间
    static private $_instance = null; // 定义空实例

    /**
     * 构造函数
     */
    private function __construct() {
        $this->runtime = date('YmdHis',time()); // 读取系统时间
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
     * 请求路由
     */
    public function Router() {
        header('Content-Type: application/json; Charset=UTF-8'); // 设置全局Http会话头为JSON模式
        if (!isset($_REQUEST['action'])) {
            die(self::buildJSON(-2, 'Sorry, the invalid application interface method was called.', null));
        }
        switch($_REQUEST['action']) {
            case 'getArticle':
            break;
            case 'getUser':
            $result = DB::getInstance()->query('select * from member');
            die(self::buildJSON(0, 'OK', $result));
            break;
            case 'auth/login':
            break;
            case 'auth/register':
            if (!isset($_POST['data']) {
                die(self::buildJSON(-2, 'Sorry, the invalid data was sended.', null));
            }
            $reg_data = json_decode($_POST['data']);
            if ($reg_data['username'] == null)
            break;
            default:
            die(self::buildJSON(-2, 'Sorry, the invalid application interface method was called.', null));
            break;
        }
    }

    /**
     * 通过数组创建JSON字符串
     * @param integer $code 响应码
     * @param string $message 响应描述
     * @param array $data 数据集合
     * @return string JSON字符串
     */
    private function buildJSON($code = 0, $message = '', $data = null) {
        if ($data == null) {
            $arr = array(
                'code'  =>  -1,
                'message'   =>  'Sorry, this application interface does not return any valid data.',
                'data'  =>  null,
                'requestId' =>  $this->runtime
            );
            return json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        }
        $arr = array(
            'code'  =>  $code,
            'message'   =>  $message,
            'data'  =>  $data,
            'requestId' =>  $this->runtime
        );
        return json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
}
