<?php
namespace Exchange\Controller;
use Home\Controller\ParentController;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class IndexController extends ParentController
{

    public function __construct($check=true){
        parent::__construct($check);
    }
//显示开发
    public function index(){
        $username = session('username');
        $this->exchange_rate();
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

        $this->assign('result',$result);
        $this->assign('username',$username);
        $this->display('exchangerate_con');
    }

//查询对应的汇率
    public function exchange_rate(){
        $Model = M();
        $sql ="select * from Y_Ratemanagement";
        $res = $Model->query($sql);
        $this->assign('res',$res[0]);

    }




}