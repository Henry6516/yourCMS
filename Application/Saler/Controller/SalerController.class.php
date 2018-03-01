<?php
namespace Saler\Controller;
use Home\Controller\ParentController;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class SalerController extends ParentController
{

    public function __construct($check=true){
        parent::__construct($check);
    }
/**
 * show_saler_page渲染页面的
 *
 */
    public function show_saler_page(){
        $username = session('username');
        $M = M();
        $sql = "select DISTINCT menuName,menuURL,rm.menuid
                from Y_user u
                LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                LEFT JOIN Y_role r ON r.roleid=ur.roleid
                LEFT JOIN Y_role_menu rm ON rm.roleid=r.roleid
                LEFT JOIN Y_netprofitmenu npm ON npm.menuid=rm.menuid
                WHERE u.username='$username' 
                ORDER BY rm.menuid";
        $result = $M->query($sql);    //菜单

        $res = $M->query("select mid from Y_manger WHERE manger='$username'");
        $mangerid =$res[0]['mid'];
        $var = [];
        if($res){
            $form_condata = $M->query("SELECT d.Did ,d.Dname,u.username,ss.suffix,sp.pingtai FROM Y_userDepartment ud
                                        LEFT JOIN Y_user u ON u.Uid=ud.Uid
                                        LEFT JOIN Y_Department d ON d.Did=ud.did
                                        LEFT JOIN Y_SuffixSalerman ss ON ss.uid=u.Uid
                                        LEFT JOIN Y_suffixPingtai sp ON sp.suffix=ss.suffix
                                        WHERE mangerid='$mangerid'  ORDER BY ss.suffix ASC ");
            $var = $this->fetch_saler_data($form_condata);
        }elseif($username == "admin"){
            $sql = "SELECT d.Did ,d.Dname,u.username,ss.suffix,sp.pingtai FROM Y_userDepartment ud
                    LEFT JOIN Y_user u ON u.Uid=ud.Uid
                    LEFT JOIN Y_Department d ON d.Did=ud.did
                    LEFT JOIN Y_SuffixSalerman ss ON ss.uid=u.Uid
                    LEFT JOIN Y_suffixPingtai sp ON sp.suffix=ss.suffix
                    WHERE d.Dname is not NULL AND sp.suffix is not null  ORDER BY ss.suffix ASC  ";
            $form_condata =  $M->query($sql);
            $var = $this->fetch_saler_data($form_condata);
        }else{
            $form_condata = $M->query("SELECT d.Did ,d.Dname,u.username,ss.suffix,sp.pingtai FROM Y_userDepartment ud
                                        LEFT JOIN Y_user u ON u.Uid=ud.Uid
                                        LEFT JOIN Y_Department d ON d.Did=ud.did
                                        LEFT JOIN Y_SuffixSalerman ss ON ss.uid=u.Uid
                                        LEFT JOIN Y_suffixPingtai sp ON sp.suffix=ss.suffix
                                        WHERE username='$username'
                                         ORDER BY ss.suffix ASC  ");

            $var =  $this->fetch_saler_data($form_condata);
        }
        //处理部门数组(全部部门)
        if(count($var['department']) == 10){
            $arr = array_slice($var['department'],0,8, true);
            $var['department'] = $arr + ['12' => $var['department'][12]] + ['11' => $var['department'][11]];
        }
        $var['department'] = $arr;
        $this->assign('result',$result);                                                                                        //nev侧边栏 对着的模块
        $this->assign('department',$var['department']);                                                                         //部门
        $this->assign('suffix',$var['suffix']);                                                                                 //账号
        $this->assign('saler',$var['saler']);                                                                           //销售员
        $this->assign('pingtai',$var['pingtai']);
        $this->assign('username',$username);
        $this->display('saler_con');
    }



//处理数据
        public function fetch_saler_data($form_condata){

            foreach ($form_condata as $k=>$v){
                $did[]     = $v['did'];
                $dname[]   = $v['dname'];
                $suffix[]  = $v['suffix'];
                $saler[]   = $v['username'];
                $pingtai[] = $v['pingtai'] ;
            }
            $data['did'] = array_unique($did);
            $data['dname'] = array_unique($dname);
            $data['suffix'] = array_unique($suffix);
            $data['saler'] =array_unique($saler);
            $data['pingtai'] = array_unique($pingtai);
            $data['department'] = array_combine($data['did'], $data['dname']);
            ksort($data['department']);
            return $data;
        }




}
