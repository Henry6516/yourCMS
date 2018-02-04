<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class UsersController extends ParentController {

    public function __construct($check=true){
        parent::__construct($check);
    }

    /*相对路径 跳转到登陆页面*/
    public function index()
    {
        redirect('./login');
    }

    /* 渲染登陆页面
     * 其中包含注册页面
     */
    public function login()
    {
        $this->render('用户登陆');
        $this->display('Index:login');
    }


    /* 执行登陆功能
     * 获取post传值->验证验证码->确认用户存在->检测密码是否相符->执行登陆或返回失败
     */
    public function do_login()
    {
        if(IS_POST){
            $postdata['username'] = $_POST['username'];
            $postdata['password'] = $_POST['password'];
            session('username',$postdata['username']);
            session('password',$postdata['password']);
        }else{
            $postdata['username'] = session('username');
            $postdata['password'] = session('password');
        }

        $Model=M('user');
            $is_registered=$Model->where("username='$postdata[username]'")->find();
            if(!empty($is_registered)){
                if($is_registered['password']==$postdata['password'])
                {
                    session('session_name',$is_registered['username']);
                    $return['status']="OK";
                    $return['msg']="登陆成功";
                }else{
                    $return['status']="NO";
                    $return['msg']="密码有误";
                }
            }else{
                $return['status']="NO";
                $return['msg']="用户不存在";
            }
        if($return['status']=="OK"){
            $M = M();
            $sql = "select DISTINCT menuName,menuURL,rm.menuid
                from Y_user u
                LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                LEFT JOIN Y_role r ON r.roleid=ur.roleid
                LEFT JOIN Y_role_menu rm ON rm.roleid=r.roleid
                LEFT JOIN Y_netprofitmenu npm ON npm.menuid=rm.menuid
                WHERE u.username='$_POST[username]' 
                ORDER BY rm.menuid";
            $result = $M->query($sql);
            $this->assign('username',$_POST['username']);
            $this->assign('result',$result);
            $this->display('demo');
        }else{
           $return = '用户名或密码不对！！';
           $this->assign('return',$return);
           $this->display('Index:login');
        }


    }




    /* 执行注销功能
     * 销毁用户session->跳转到登陆页面
     */
    public function logout(){
        session('username',null);
        redirect('/home/users/login');
    }




    /*执行用户注册功能*/
    public function do_register(){

    }




}
