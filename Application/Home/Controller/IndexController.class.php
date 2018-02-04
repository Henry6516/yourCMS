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
        redirect('/home/users/login');
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