<?php
/**
 * MySQL数据库操作类
 * @package DataBase
 * @subpackage MySQL Operator
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

class MySQL_C extends Common {

    /**
     * 使用配置文件连库
     * @return object 返回连接句柄
     */
    public function connect() {
        $conn = mysqli_connect(Common::R()->mysqlhost, Common::R()->mysqluser, Common::R()->mysqlpswd, Common::R()->mysqldbid);
        return $conn;
    }

    /**
     * 释放数据库连接过程
     * @param object $handle 连接句柄
     * @return null
     */
    public function close($handle) {
        mysqli_close($handle);
    }

    /**
     * 数据库查询过程
     * @param string $sqlcode SQL查询语句
     * @param string $encode 查询编码
     * @param string $handle 连接句柄
     * @return boolean 查询成功与否
     */
    public function ExcuteNonQuery($sqlcode, $encode = 'UTF8', $handle) {
        mysqli_query($handle, 'set names '.$encode);
        $result = mysqli_query($handle, $sqlcode);
        if ($result) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * 数据库查询过程
     * @param string $sqlcode SQL查询语句
     * @param string $encode 查询编码
     * @param string $handle 连接句柄
     * @return array 数据集合
     */
    public function ExcuteFullQuery($sqlcode, $encode = 'UTF8', $handle) {
        mysqli_query($handle, 'set names '.$encode);
        $result = mysqli_query($handle, $sqlcode);
        if (!$result) {
            return null;
        }
        $data = $result->fetch_array();
        return $data;
    }

    /**
     * 私有加密算法
     * @param string $str 需要加密的字串
     * @return string 返回加密后数据
     */
    public function SelfEncrypt($str) {
        $salt = Common::R()->salt;
        $encryptValue = sha1(md5($str.$salt.$str));
        return $encryptValue;
    }
}