<?php
/**
 * phpCAS客户端实现过程封装
 * @package CAS
 * @subpackage Central Authentication
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

class mCAS extends Common {

    /**
     * CAS互联登录协议
     * @return null
     */
    public function CASLogin() {
        include(dirname(__FILE__).'/CAS-1.3.5/CAS.php');
        //phpCAS::setDebug();
        $SSOConf = Common::R()->SSO;
        phpCAS::client(CAS_VERSION_2_0, $SSOConf->server_address, $SSOConf->server_port, $SSOConf->server_path);
        phpCAS::setNoCasServerValidation();
        phpCAS::handleLogoutRequests();
        if (phpCAS::isAuthenticated()) {
            $user = phpCAS::getUser();
            $_SESSION['user'] = $user;
            $_SESSION['logintime'] = date('Y/m/d H:i:s',time());
            $_SESSION['token'] = md5($user.$_SESSION['logintime']);
            $_SESSION['caslogin'] = 'true';
            $_SESSION['logoutUrl'] = './index.php?c=CASLogout';
            Common::redirect('./index.php?c=Index', $user.'，您已使用CAS系统成功互联登录本系统！正在进入后台，请稍候。', 3);
        }
        else {
            phpCAS::forceAuthentication();
        }
    }

    /**
     * CAS同步退出协议
     * @return null
     */
    public function CASLogout() {
        include(dirname(__FILE__).'/CAS-1.3.5/CAS.php');
        $SSOConf = Common::R()->SSO;
        phpCAS::client(CAS_VERSION_2_0, $SSOConf->server_address, $SSOConf->server_port, $SSOConf->server_path);
        phpCAS::logout( array( 'url' => $_SERVER['HTTP_REFERER']));
        session_destroy();
        session_write_close();
    }
}