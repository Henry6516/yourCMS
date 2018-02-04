<?php
namespace Admin\Controller;
use Home\Controller\ParentController;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class AdminController extends ParentController
{

    public function __construct($check=true){
        parent::__construct($check);
    }
/**
 * show_saler_page渲染页面的
 *
 */
    public function show_admin_page(){
        $username = session('username');
        $M = M();
        $sql = "select DISTINCT menuName,menuURL,rm.menuid,npm.pmenuid,npm.menulevel
                from Y_user u
                LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                LEFT JOIN Y_role r ON r.roleid=ur.roleid
                LEFT JOIN Y_role_menu rm ON rm.roleid=r.roleid
                LEFT JOIN Y_netprofitmenu npm ON npm.menuid=rm.menuid
                WHERE u.username='$username' ORDER BY rm.menuid";
        $result = $M->query($sql);    //菜单
        $sql_users = "SELECT u.uid,u.username,u.password,d.Dname from Y_user u
                        LEFT JOIN Y_userDepartment ud ON ud.Uid=u.uid
                        LEFT JOIN Y_Department d ON d.Did=ud.Did
                        ";
        $data = $M->query($sql_users);
        $this->assign('data',$data);
        $this->assign('result',$result);                                                                                      //nev侧边栏 对着的模块
        $this->assign('username',$username);
        $this->display('user_list');
    }

/*
 * 添加用户编辑用户 做更新 根据用户的ID
 * update Y_user set username='',password=''
 * insert into Y_user() values()
 */
    public  function add_edit_user(){
        $M = M();
        $data['uid'] = $_POST['uid'];
        $data['username'] = $_POST['username'];
        $data['password'] = $_POST['password'];
        $data['departmentid'] = $_POST['department'];
        $sql_user ="Z_Admin_AddUpdate '$data[uid]','$data[username]','$data[password]','$data[departmentid]'";
        $res_user = $M->execute($sql_user);
        if($data['uid']&&$res_user){
            echo '编辑用户成功!';
        }else{
            echo '添加用户成功！';
        }


    }

    /*
     * 删除用户 ，根据用户的ID删除
     */
    public  function  delete_user(){
        $M = M();
        $data['uid'] = $_POST['uid'];
        $sql_user ="delete from Y_user WHERE uid='$data[uid]'";
        $user_dep ="delete from Y_userDepartment WHERE uid='$data[uid]'";
        $res_user = $M->execute($sql_user);
        $res_dep = $M->execute($user_dep);

        if($res_user&&$res_dep){
            echo '删除用户成功!';
        }else{
            echo '删除失败!';
        }


    }







}