<?php
/**
 * 数据库驱动类
 * Notice：这是一个单实例设计模式下工作的类
 * @package DingStudio/BlogAPP
 * @subpackage DBDriver
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

class DB {

    static private $_instance = null; // 定义空实例
    static private $_sqlcon; // 定义SQL连接句柄
    /**
     * 初始化SQL数据库服务器信息变量
     */
    private $sqlsrv;
    private $sqlusr;
    private $sqlpwd;
    private $sqlmdb;

    /**
     * 构造函数
     */
    private function __construct() {
        $config = file_get_contents(APP_PATH.'data/config.json');
        $config = json_decode($config);
        $this->sqlsrv = $config->sqlsrv;
        $this->sqlusr = $config->sqlusr;
        $this->sqlpwd = $config->sqlpwd;
        $this->sqlmdb = $config->sqlmdb;
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
     * 数据库查询语句的执行与返回过程
     * @param string $sqlcode 数据库SQL查询语句
     * @return array 数据集合
     */
    public function query($sqlcode) {
        if (!self::$_sqlcon) {
            self::$_sqlcon = self::connect();
        }
        $result = self::$_sqlcon->query($sqlcode);
        $data = $result->fetch_all(MYSQL_ASSOC);
        if ($data == null || $data == '') {
            return null;
        }
        else {
            return $data;
        }
    }

    /**
     * 数据库连接建立过程
     * @return mixed {成功返回连接句柄/失败返回布尔值FALSE}
     */
    private function connect() {
        if (!self::$_sqlcon) {
            self::$_sqlcon = mysqli_connect($this->sqlsrv, $this->sqlusr, $this->sqlpwd);
            if (!self::$_sqlcon) {
                //throw new Exception('MySQL Server connect failed, error detail: '.mysqli_error());
                return false;
            }
            mysqli_select_db(self::$_sqlcon, $this->sqlmdb);
            self::$_sqlcon->query('set names UTF8');
        }
        return self::$_sqlcon;
    }

    /**
     * 数据库连接释放过程
     * @return boolean 释放结果
     */
    private function close() {
        if (!self::$_sqlcon) { // 判断是否已有线上的连接句柄
            return false;
        }
        $result = self::$_sqlcon->close(); // 尝试关闭已有连接句柄并返回操作结果
        if ($result) { // 关闭成功
            return true;
        }
        else { // 关闭失败
            return false;
        }
    }
}