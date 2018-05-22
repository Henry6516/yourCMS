<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-11
 * Time: 15:16
 */

namespace Admin\Controller;
use Think\Controller;


class SuffixController extends Controller
{

    public function show_suffix(){
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
        $sql_users = "SELECT ss.id,ss.suffix,salesman as username,m.manger,sp.pingtai FROM Y_SuffixSalerman ss
                    left JOIN  Y_manger m ON m.mid =ss.mangerid
                    LEFT JOIN Y_suffixPingtai sp ON sp.suffix=ss.suffix
                    WHERE sp.pingtai IN ('eBay','Wish','Amazon','Joom','SMT')
                    ORDER BY ss.suffix ASC ";
        $data = $M->query($sql_users);
        $this->assign('data',$data);
        $this->assign('result',$result);
        $this->assign('username',$username);
        $this->display('saler_suffix_pingtai');
    }

    /*
     * 添加账号
     * 编辑账号
     * 涉及到Y_SuffixSalerman,Y_Suffixpingtai
     *
     */
    public function add_edit_suffix(){
        $M = M();
        $data['id'] = $_POST['id'];
        $data['suffix'] = $_POST['suffix'];
        $data['username'] = $_POST['username'];
        $data['manger'] = $_POST['manger'];
        $data['pingtai'] = $_POST['pingtai'];
        $mangerid_sql = "select mid from Y_manger WHERE manger='$data[manger]'";
        $mangerid = $M->query($mangerid_sql);
        $userid = $M->query("select uid from Y_user WHERE username='$data[username]'");
        $pingtai = $M->query("select id as spid from Y_suffixPingtai WHERE suffix='$data[suffix]'");
        $data['uid'] = $userid[0]['uid'];
        $data['mid'] = $mangerid[0]['mid'];
        $data['spid'] = $pingtai[0]['spid'];

        if($data['id']){
            $edit_suffix = "update Y_SuffixSalerman set suffix='$data[suffix]',salesman='$data[username]',mangerid='$data[mid]',uid='$data[uid]' WHERE id='$data[id]'";
            $edit_pingtai = "update Y_suffixPingtai set suffix='$data[suffix]',pingtai='$data[pingtai]' where id='$data[spid]'";

            $res['edit_suffix'] = $M->execute($edit_suffix);
            $res['edit_pingtai'] = $M->execute($edit_pingtai);


        }else{
            $add_suffix = "insert into Y_SuffixSalerman (suffix,salesman,mangerid,uid) VALUES ('$data[suffix]','$data[username]','$data[mid]','$data[uid]')";
            $add_pingtai = "insert into Y_suffixPingtai(suffix,pingtai) VALUES ('$data[suffix]','$data[pingtai]')";
            $res['add_suffix_res'] = $M->execute($add_suffix);
            $res['add_pingtai_res'] = $M->execute($add_pingtai);
        }

        if($res['edit_suffix']&&$res['edit_pingtai']){
            echo '更新成功!';

        }elseif($res['add_suffix_res']&&$res['add_pingtai_res']){
            echo '添加成功!';
        }


    }


    /*
     * 删除账号 根据 账号对应的 ID 删除
     * Y_SuffixSalerman,Y_Suffixpingtai
     *
     */
    public  function delete_suffix(){
       $M =M();
       $data['id'] = $_POST['id'];
       $data['suffix'] = $_POST['suffix'];
       $suffix_del = "delete from Y_SuffixSalerman WHERE id='$data[id]'";
       $pingtai_del = "delete from Y_suffixPingtai WHERE suffix='$data[suffix]'";
       $res_delsuffix = $M->execute($suffix_del);
       $res_delpingtai = $M->execute($pingtai_del);
       if($res_delsuffix&&$res_delpingtai){
           echo '删除成功!';
       }
    }

}