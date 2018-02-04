<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-11
 * Time: 13:50
 */

namespace Admin\Controller;


use Think\Controller;

class RoleController extends Controller
{
    public function show_role(){
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
        $sql_users = "select ur.id,u.username,r.roleName from Y_user_role ur
                    LEFT JOIN Y_user u ON u.uid=ur.uid
                    LEFT JOIN  Y_role r ON  r.roleid=ur.roleid
                    ORDER  by ur.id ASC ";
        $data = $M->query($sql_users);
        $this->assign('data',$data);
        $this->assign('result',$result);
        $this->assign('username',$username);
        $this->display('role_list');
    }
    /*
     * 添加 编辑角色
     * Y_user_role
     * Y_userDepartment
     */
    public function add_edit_role(){
        $M =M();
        $data['id'] = $_POST['id'];
        $data['username'] = $_POST['username'];
        $data['rolename'] = $_POST['rolename'];
        $add_sql ="z_userRole '$data[id]','$data[username]','$data[rolename]'"; //插入角色表
        $res = $M->execute($add_sql);
            if($data['id']&&$res){
                echo '添加角色操作成功!';

            }else{
                echo '更新操作成功!';

            }
    }
    /*
     * 删除角色
     */
    public function delete_role(){
        $data['id'] = $_POST['id'];
        $delete_role = "delete from Y_user_role WHERE id='$data[id]'";
        $M = M();
        $res = $M->execute($delete_role);
        if($res){
            echo '删除成功!';
        }else{
            echo '删除失败!';
        }
    }



}