<?php
/**
 * 数据库自助导库类
 * Notice：这是一个单实例设计模式下工作的类
 * @package DingStudio/BlogAPP
 * @subpackage DBInstaller
 * @author David Ding
 * @copyright 2012-2018 DingStudio All Rights Reserved
 */

class DBInstaller {

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
    private function __construct($sqlsrv = null, $sqlusr = null, $sqlpwd = null, $sqlmdb = null, $sqlfile = null) {
        $this->sqlsrv = $sqlsrv;
        $this->sqlusr = $sqlusr;
        $this->sqlpwd = $sqlpwd;
        $this->sqlmdb = $sqlmdb;
        $_sqlcode = file_get_contents($sqlfile);
        $_array = explode(';', $_sqlcode);
        //error_reporting(0);
        try {
            $_mysqli = mysqli_connect($this->sqlsrv, $this->sqlusr, $this->sqlpwd);
            if (!$_mysqli) {
                throw new Exception();
            }
        }
        catch (Exception $e) {
            $arr = array(
                'code'  =>  500,
                'message'   =>  '抱歉，数据库连接失败！请检查信息填写是否正确，以及数据库服务器是否工作正常。'
            );
            self::out($arr);
        }
        $result = mysqli_select_db($_mysqli, $this->sqlmdb);
        if (!$result) {
            $arr = array(
                'code'  =>  500,
                'message'   =>  '抱歉，数据库表写入失败！请检查您所填写的数据库是否存在，且您的数据库账号对其有正常读写权限。'
            );
            self::out($arr);
        }
        $_mysqli->query('set names UTF8');
        //执行sql语句
        try {
            foreach ($_array as $_value) {
                $result = $_mysqli->query($_value.';');
            }
        }
        catch (Exception $e) {
            $arr = array(
                'code'  =>  500,
                'message'   =>  '抱歉，数据库表写入失败！请核实您的数据库账号对当前所提供的数据库有正常读写权限。'
            );
            self::out($arr);
        }
        $_mysqli->close();
        $_mysqli = null;
    }

    /**
     * 统一预留实例化入口
     * @param string $sqlfile sql文件路径
     * @return instance 实例化对象
     */
    public static function getInstance($sqlsrv = null, $sqlusr = null, $sqlpwd = null, $sqlmdb = null, $sqlfile = null) {
        if ($sqlfile == null || $sqlfile == '') {
            $arr = array(
                'code'  =>  500,
                'message'   =>  '数据库表文件查询意外中止，错误类型：Invaild struct query language file path.'
            );
            self::out($arr);
        }
        else if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($sqlsrv, $sqlusr, $sqlpwd, $sqlmdb, $sqlfile);
        }
        return self::$_instance;
    }

    private static function out($data = array()) {
        header('Content-Type: application/json; Charset=UTF-8');
        array_push($data, array('requestId', sha1(date('YmdHis',time()))));
        die(json_encode($data));
    }
}