<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class IndexController extends ParentController{

    public function __construct($check=true){
        parent::__construct($check);
    }

    //主方法
    public function index(){
        $username = session('username');
        if($username){
            $M = M();
            $sql = "select DISTINCT menuName,menuURL,rm.menuid
                from Y_user u
                LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                LEFT JOIN Y_role r ON r.roleid=ur.roleid
                LEFT JOIN Y_role_menu rm ON rm.roleid=r.roleid
                LEFT JOIN Y_netprofitmenu npm ON npm.menuid=rm.menuid
                WHERE u.username='$username' 
                ORDER BY rm.menuid";
            $result = $M->query($sql);
            $this->assign('username',$username);
            $this->assign('result',$result);
            $this->display('demo');
        }else{
            redirect('/home/users/login');
        }
    }


    /*
    *登陆页面
     *
     */
    public function home(){
        $admin=session('session_name');
        $this->assign('admin',$admin);
        $this->render('管理中心');
        $this->display('home');
    }




}