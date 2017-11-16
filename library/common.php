<?php
/**
 * 全局通用函数封装
 * @package Common
 * @subpackage SmartTraffic
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

class Common {

    /**
     * 前台程序渲染入口
     * @param string $operator 页面渲染类型
     * @return null
     */
    public function Main($operator = null) {
        define('APP_NAME',self::C('read','app_name'));
        define('APP_VER',self::C('read','app_version'));
        if (!session_id()) {
            session_start();
        }
        switch ($operator) {
            case "CASLogin":
            include(dirname(__FILE__).'/cas.class.php');
            $mCAS = new mCAS();
            $mCAS->CASLogin();
            break;
            case "CASLogout":
            include(dirname(__FILE__).'/cas.class.php');
            $mCAS = new mCAS();
            $mCAS->CASLogout();
            break;
            case "Login":   
            require_once(APP_PATH.'template/login.html');
            break;
            case "postLogin":
            if (!isset($_POST['user']) || !isset($_POST['pswd']) || @$_POST['user'] == '' || @$_POST['pswd'] == '') {
                self::errorAction(405, '非法操作', '用户名或密码不能为空，请重试。', "location.href='./index.php?c=Login'", '立即返回');
            }
            self::loginAction(@$_POST['user'], @$_POST['pswd']);
            break;
            case "postRegister":
            if (!isset($_POST['user']) || !isset($_POST['pswd']) || !isset($_POST['mail']) || @$_POST['user'] == '' || @$_POST['pswd'] == '' || @$_POST['mail'] == '') {
                self::errorAction(405, '非法操作', '用户名、密码或邮箱不能为空，请重试。', "location.href='./index.php?c=Register'", '立即返回');
            }
            self::registerAction(@$_POST['user'], @$_POST['pswd'], @$_POST['mail']);
            break;
            case "Logout":
            if (!self::checkPerm()) {
                self::redirect('./index.php?c=Login', '无需清理用户会话，因为您尚未登录。正在重定向至登陆页！', 3);
            }
            session_destroy();
            session_write_close();
            self::redirect('./index.php?c=Login', '用户会话已退出，正在重定向至登陆页。', 2);
            break;
            case "Register":
            if (self::checkPerm()) {
                self::errorAction(405, '无需执行该操作', '您已登录，无需执行该操作！', "location.href='./index.php?c=Index'", '进入后台');
            }
            require_once(APP_PATH.'template/sign-up.html');
            break;
            case "Index":
            if (!self::checkPerm()) {
                self::errorAction(401, '这些内容受到保护', '抱歉，您尚未登录。请先登录您的账号获得访问权限！', "location.href='./index.php?c=Login'", '立即登录');
            }
            require_once(APP_PATH.'template/dashboard.html');
            break;
            case "AllRoad":
            if (!self::checkPerm()) {
                self::errorAction(401, '没有访问权限', '抱歉，您尚未登录。请先登录您的账号获得访问权限！', "location.href='./index.php?c=Login'", '立即登录');
            }
            require_once(APP_PATH.'template/allroad.html');
            break;
            case "Api":
            self::APIServlet();
            break;
            default:
            if (!self::checkPerm()) {
                self::errorAction(401, '没有访问权限', '抱歉，您尚未登录。请先登录您的账号获得访问权限！', "location.href='./index.php?c=Login'", '立即登录', './index.php?c=Login');
            }
            else {
                self::redirect('./index.php?c=Index', '正在重定向请求，请稍候。', 3);
            }
            break;
        }
    }

    /**
     * 用户权限检查过程
     * @return boolean 检查结果
     */
    public function checkPerm() {
        if (!isset($_SESSION['token']) || $_SESSION['token'] == '') {
            return false;
        }
        else if ($_SESSION['token'] != $_SESSION['token']) {
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * 用户会话登录过程
     * @param string $user 用户账号
     * @param string $pswd 用户密码
     * @return boolean 登录结果
     */
    public function loginAction($user = null, $pswd = null) {
        if ($user == null || $pswd == null) {
            return false;
        }
        require_once(APP_PATH.'library/mysql.driver.php');
        $sqlcon = new MySQL_C();
        $token = $sqlcon->SelfEncrypt($user.$pswd);
        $euser = $sqlcon->SelfEncrypt($user);
        $encrypt = $sqlcon->SelfEncrypt($pswd);
        $conn = $sqlcon->connect();
        $result = $sqlcon->ExcuteFullQuery('select * from member where account="'.$euser.'" and password="'.$encrypt.'"', 'UTF8', $conn);
        if ($result) {
            $_SESSION['token'] = $token;
            $_SESSION['user'] = $user;
            $_SESSION['logintime'] = date('Y/m/d H:i:s',time());
            $_SESSION['caslogin'] = 'false';
            $_SESSION['logoutUrl'] = './index.php?c=Logout';
            self::redirect('./index.php?c=Index', '登录成功！正在进入后台，请稍候。', 1);
        }
        else {
            self::errorAction(403, '登录失败', '用户名或密码不正确，请重试。', "location.href='./index.php?c=Login'", '立即返回');
        }
        exit();
    }

    public function registerAction($user = null, $pswd = null, $mail = null) {
        if ($user == null || $pswd == null || $mail == null) {
            return false;
        }
        require_once(APP_PATH.'library/mysql.driver.php');
        $sqlcon = new MySQL_C();
        $token = $sqlcon->SelfEncrypt($user.$pswd);
        $euser = $sqlcon->SelfEncrypt($user);
        $encrypt = $sqlcon->SelfEncrypt($pswd);
        $conn = $sqlcon->connect();
        $result = $sqlcon->ExcuteFullQuery('select * from member where account="'.$euser.'"', 'UTF8', $conn);
        if ($result) {
            self::errorAction(500, '注册失败', '系统已有相同用户名，请换一个更有创意的ID吧。', "location.href='./index.php?c=Register'", '返回修改');
        }
        else {
            $result = $sqlcon->ExcuteNonQuery('insert into member (account,password,token,email) values ("'.$euser.'","'.$encrypt.'","'.$token.'","'.$mail.'")', 'UTF8', $conn);
            if ($result == 1) {
                self::redirect('./index.php?c=Login', '注册成功！正在为您跳转到登录页面，请稍候。', 2);
            }
            else {
                self::errorAction(502, '注册失败', '非常抱歉，由于服务器未正确响应您的注册请求，本次注册已被取消！如果该现象多次出现，请联系管理员。', "location.href='./index.php?c=Register'", '重新注册');
            }
        }
    }

    /**
     * 重定向逻辑封装
     * @param string $url 重定向目标
     * @param string $msg 重定向提示信息
     * @param integer $interval 跳转等待秒数
     * @return null
     */
    public function redirect($url = null, $msg = '页面跳转中', $interval = 3) {
        if ($url == null) {
            return null;
        }
        else {
            header('Content-Type: text/html; Charset=UTF-8');
            header('refresh:'.$interval.'; url='.$url);
            echo $msg;
            exit();
        }
    }
    
    /**
     * 错误页面显示逻辑
     * @param integer $errnum 错误代码
     * @param string $title 错误标题
     * @param string $message 错误消息
     * @param string $script 按钮点击事件（标准JS代码）
     * @param string $continueStr 确认按钮文本
     * @return null
     */
    public function errorAction($errnum = 404, $title = '错误', $message = '应用程序发生未知错误！', $script = '', $continueStr = '继续', $forceRedirect = null) {
        if ($forceRedirect != null) {
            header('Location: '.$forceRedirect);
            exit();
        }
        $errcode = $errnum;
        $errtitle = $title;
        $errmsg = $message;
        $scriptCode = $script;
        header('Content-Type: text/html; Charset=UTF-8');
        require_once(APP_PATH.'template/error.html');
        exit();
    }

    /**
     * 配置文件外部只读权限开放
     * @return array
     */
    public function R() {
        return self::C('read', null);
    }

    /**
     * 绍兴市智慧交通平台API私有代理入口
     * @return mixed
     */
    private function APIServlet() {
        $Server = self::R()->apisrv; //获取智慧交通平台数据接口服务器地址
        if (self::R()->apissl == 'false') {
            $SRV = 'http://'.$Server;
        }
        else {
            $SRV = 'https://'.$Server;
        }
        $Service = self::R()->apiList; //获取智慧交通平台可用接口
        switch (@$_GET['mod']) {
            case "AllRoad":
            header('Content-Type: application/json; Charset=UTF-8');
            echo json_encode(json_decode(file_get_contents($SRV.$Service->AllRoad)),JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            break;
            case "Area":
            header('Content-Type: application/json; Charset=UTF-8');
            echo json_encode(json_decode(file_get_contents($SRV.$Service->Area)),JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            break;
            case "OneLoopLine":
            header('Content-Type: image/png; Charset=UTF-8');
            echo file_get_contents($SRV.$Service->OneLoopLine);
            break;
            case "HotArea":
            header('Content-Type: application/json; Charset=UTF-8');
            echo json_encode(json_decode(file_get_contents($SRV.$Service->HotArea)),JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            break;
            case "ApiSqlList":
            echo json_decode(json_encode(dirname(__FILE__).'/data.json'));
            break;
            default:
            header('Content-Type: text/plain; Charset=UTF-8');
            echo 'Error';
            break;
        }
    }

    /**
     * 配置文件读取过程
     * @param string $mod 读写模式
     * @param array $data 配置参数
     * @return array 返回配置文件数据集合
     */
    private function C($mod = 'read', $data = null) {
        $confPath = APP_PATH.'data/config.json';
        if (!file_exists($confPath)) {
            return null;
        }
        if ($mod == 'read') {
            $str = file_get_contents($confPath);
            if ($data != null) {
                return json_decode($str)->$data;
            }
            else {
                return json_decode($str);
            }
        }
        else {
            if ($data == null) {
                return null;
            }
            else {
                $conf = json_encode($data);
                $result = file_put_contents($confPath, $conf);
                if ($result) {
                    return file_get_contents($confPath);
                }
                else {
                    return null;
                }
            }
        }
    }
}