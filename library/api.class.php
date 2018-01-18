<?php
/**
 * API运行模式封装
 * Notice：本类已实现单实例设计模式
 * @package DingStudio/BlogAPP
 * @subpackage APILibrary
 * @author David Ding
 * @copyright 2012-2018 DingStudio All Rights Reserved
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
        header('Content-Type: application/json; Charset=UTF-8'); //设置全局Http会话头为JSON模式
        if (!isset($_REQUEST['action'])) {
            die(self::buildJSON(-2, 'Sorry, the invalid application interface method was called.', null));
        }
        switch($_REQUEST['action']) {
            case 'getArticle':
            self::articleReader();
            break;
            case 'getUser':
            if (!Base::getInstance()->isLogin()) {
                die(self::buildJSON(-1, 'Please login and try again later.', null));
            }
            $result = DB::getInstance()->QueryData('select * from member');
            die(self::buildJSON(0, 'OK', $result));
            break;
            case 'Template':
            if (!isset($_POST['Tpl'])) {
                die('无效的模板文件！');
            }
            else {
                switch ($_POST['Tpl']) {
                    case 'postmgr':
                    echo file_get_contents(APP_PATH.'template/inc/postmgr.html');
                    break;
                    case 'classmgr':
                    echo file_get_contents(APP_PATH.'template/inc/classmgr.html');
                    break;
                    case 'dashboard':
                    echo file_get_contents(APP_PATH.'template/inc/dashboard.html');
                    break;
                    default:
                    die('无效的模板文件！');
                    break;
                }
            }
            break;
            case 'auth/login':
            if (Base::getInstance()->isLogin()) {
                die(self::buildJSON(-12, 'Please logout and try again later.', null));
            }
            self::user_login();
            break;
            case 'auth/register':
            if (Base::getInstance()->isLogin()) {
                die(self::buildJSON(-12, 'Please logout and try again later.', null));
            }
            self::user_register();
            break;
            case 'post/update':
            if (!Base::getInstance()->isLogin()) {
                die(self::buildJSON(-1, 'Please login and try again later.', null));
            }
            self::articleUpdater();
            break;
            case 'post/remove':
            /*if (!Base::getInstance()->isLogin()) {
                die(self::buildJSON(-1, 'Please login and try again later.', null));
            }*/
            self::articleRemover();
            break;
            default:
            die(self::buildJSON(-2, 'Sorry, the invalid application interface method was called.', null));
            break;
        }
    }

    /**
     * 用户登录过程实现
     */
    private function user_login() {
        if (!isset($_POST['data'])) {
            die(self::buildJSON(-3, 'Sorry, the invalid data was sended.', null));
        }
        $reg_data = json_decode($_POST['data']);
        if (@$reg_data->uname == null || @$reg_data->upswd == null) {
            die(self::buildJSON(-5, 'Illegal configuration information.', null));
        }
        else {
            $uname = @$reg_data->uname;
            $upswd = sha1(@$reg_data->upswd);
        }
        $result = DB::getInstance()->QueryData('select * from member where username="'.$uname.'" and userpwd="'.$upswd.'"');
        if ($result) {
            $token = md5($uname.$upswd.date('YmdH',time())); //通过相关信息合成AccessToken（有效期1小时）
            $result = DB::getInstance()->QueryResult('update member set usertoken="'.$token.'" where username="'.$uname.'" and userpwd="'.$upswd.'"'); //更新AccessToken到数据库
            if ($result <= 0) { //判断AccessToken更新结果，如果更新失败则拦截登录过程
                die(self::buildJSON(-8, 'Operation timeout, please try again later.', null));
            }
            $_SESSION['username'] = $uname; //存储明文用户名到session
            $_SESSION['token'] = $token; //存储原始token到session
            setcookie("username", sha1($uname), time() + 3600,  "/", $_SERVER['HTTP_HOST']); //存储密文用户名到cookie
            setcookie("token", sha1($token), time() + 3600,  "/", $_SERVER['HTTP_HOST']); //存储二次加密的token到cookie
            die(self::buildJSON(0, 'Welcome back, '.$uname.'.', null)); //返回登录成功的json消息
        }
        else {
            die(self::buildJSON(-1, 'Invaild username or password, please check it again and change something.', null)); //返回登录失败的json消息
        }
    }

    /**
     * 用户注册过程实现
     */
    private function user_register() {
        if (!isset($_POST['data'])) {
            die(self::buildJSON(-3, 'Sorry, the invalid data was sended.', null));
        }
        $reg_data = json_decode($_POST['data']);
        if (@$reg_data->uname == null || @$reg_data->upswd == null || @$reg_data->email == null) {
            die(self::buildJSON(-5, 'Illegal configuration information.', null));
        }
        else {
            $uname = @$reg_data->uname;
            $upswd = sha1(@$reg_data->upswd);
            $email = @$reg_data->email;
        }
        $result = DB::getInstance()->QueryData('select * from member where username="'.$uname.'" or usermail="'.$email.'"');
        if ($result) {
            die(self::buildJSON(-10, 'Sorry, this username or e-mail has already existed.', null));
        }
        $result = DB::getInstance()->QueryResult('insert into member (username,userpwd,usermail) values ("'.$uname.'","'.$upswd.'","'.$email.'")');
        if ($result > 0) {
            die(self::buildJSON(0, 'Well, the account was successfully created.', null));
        }
        else {
            die(self::buildJSON(-7, 'Oh-No, our database was denied your request. Please try again later.', null));
        }
    }

    /**
     * 文章数据移除的实现
     */
    private function articleRemover() {
        if (!is_numeric(@$_POST['aid'])) {
            die(self::buildJSON(-31, 'Could not execute this operation, because you not input an article ID.', null));
        }
        $aid = htmlspecialchars(@$_POST['aid']);
        $result = DB::getInstance()->QueryResult('delete from article where aid='.$aid);
        if ($result != 0) {
            die(self::buildJSON(0, 'Well, the article was successfully deleted.', null));
        }
        else {
            die(self::buildJSON(-7, 'Oh-No, our database was denied your request. Please try again later.', null));
        }
    }

    /**
     * 文章数据读取的实现
     */
    private function articleReader() {
        switch (@$_REQUEST['type']) {
            case 'list':
            $column = 'aid,title,ctime';
            break;
            case 'full':
            $column = '*';
            break;
            default:
            die(self::buildJSON(-20, 'Could not fetch blog article data, because you are using the type of data acceptance that is not supported.', null));
            break;
        }
        if (is_numeric(@$_POST['aid'])) {
            $result = DB::getInstance()->QueryData('select '.$column.' from article where aid='.$_POST['aid']);
        }
        else if (!isset($_POST['limit'])) {
            $result = DB::getInstance()->QueryData('select '.$column.' from article order by ctime desc');
        }
        else if (is_numeric($_POST['limit'])) {
            if (@$_POST['limit'] == '0') {
                $result = DB::getInstance()->QueryData('select '.$column.' from article order by ctime desc');
            }
            else {
                $result = DB::getInstance()->QueryData('select '.$column.' from article order by ctime desc limit '.$_POST['limit']);
            }
        }
        else {
            $result = DB::getInstance()->QueryData('select * from article order by ctime desc');
        }
        die(self::buildJSON(0, 'OK', $result));
    }

    /**
     * 文章更新过程的实现
     */
    private function articleUpdater() {
        if (!isset($_POST['title']) || !isset($_POST['content']) || !isset($_POST['aid'])) {
            die(self::buildJSON(-30, 'Sorry, the invalid article data was sended.', null));
        }
        else {
            if ($_POST['title'] == '' || $_POST['content'] == '' || $_POST['aid'] == '') {
                die(self::buildJSON(-31, 'Illegal article data.', null));
            }
            else if ($_POST['aid'] == '0') {
                $result = DB::getInstance()->QueryResult('insert into article (title,content,class,ptime,status) values ("'.$_POST['title'].'","'.$_POST['content'].'",1,"'.date('Y-m-d H:i:s',time()).'",1)');
                if ($result > 0) {
                    die(self::buildJSON(0, 'Well, the article was successfully created.', null));
                }
                else {
                    die(self::buildJSON(-7, 'Oh-No, our database was denied your request. Please try again later.', null));
                }
            }
            else {
                $result = DB::getInstance()->QueryResult('update article set title="'.$_POST['title'].'",content="'.$_POST['content'].'" where aid="'.$_POST['aid'].'"');
                if ($result > 0) {
                    die(self::buildJSON(0, 'Well, the article was successfully updated.', null));
                }
                else {
                    die(self::buildJSON(-7, 'Oh-No, our database was denied your request. Please try again later.', null));
                }
            }
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
        $arr = array(
            'code'  =>  $code,
            'message'   =>  $message,
            'data'  =>  $data,
            'requestId' =>  $this->runtime
        );
        return json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
}
