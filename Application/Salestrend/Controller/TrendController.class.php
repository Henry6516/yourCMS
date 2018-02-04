<?php
namespace SalesTrend\Controller;
use Home\Controller\ParentController;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");
class TrendController extends ParentController
{

    public function __construct($check=true)
    {
        parent::__construct($check);

    }

    public function getCondition()

    {
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
                                        WHERE mangerid='$mangerid'
                                        ORDER BY ss.suffix ASC");
            $var = $this->fetch_saler_data($form_condata);
        }elseif($username == "admin"){
            $sql = "SELECT d.Did ,d.Dname,u.username,ss.suffix,sp.pingtai FROM Y_userDepartment ud
                    LEFT JOIN Y_user u ON u.Uid=ud.Uid
                    LEFT JOIN Y_Department d ON d.Did=ud.did
                    LEFT JOIN Y_SuffixSalerman ss ON ss.uid=u.Uid
                    LEFT JOIN Y_suffixPingtai sp ON sp.suffix=ss.suffix
                    WHERE d.Dname is not NULL AND sp.suffix is not null
                     ORDER BY ss.suffix ASC";
            $form_condata =  $M->query($sql);
            $var = $this->fetch_saler_data($form_condata);
        }else{
            $form_condata = $M->query("SELECT d.Did ,d.Dname,u.username,ss.suffix,sp.pingtai FROM Y_userDepartment ud
                                        LEFT JOIN Y_user u ON u.Uid=ud.Uid
                                        LEFT JOIN Y_Department d ON d.Did=ud.did
                                        LEFT JOIN Y_SuffixSalerman ss ON ss.uid=u.Uid
                                        LEFT JOIN Y_suffixPingtai sp ON sp.suffix=ss.suffix
                                        WHERE username='$username'
                                        ORDER BY ss.suffix ASC");

            $var =  $this->fetch_saler_data($form_condata);
        }

        $this->assign('result',$result);                                                                                        //nev侧边栏 对着的模块
        $this->assign('department',$var['department']);                                                                         //部门
        $this->assign('suffix',$var['suffix']);                                                                                 //账号
        $this->assign('saler',$var['saler']);                                                                           //销售员
        $this->assign('pingtai',$var['pingtai']);
        $this->assign('username',$username);
        $this->display('salestrend');
    }


//处理数据
    public function fetch_saler_data($form_condata)
    {

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


    //查询结果
    public function  salesData(){
        $model= M();
        $_POST['department'] = rtrim(implode($_POST['department'],','),',');
        $_POST['pingtai'] = rtrim(implode($_POST['pingtai'],','),',');
        $_POST['saler'] = rtrim(implode($_POST['saler'],','),',');
        $_POST['suffix'] = rtrim(implode($_POST['suffix'],','),',');
        $sql = "z_p_saletrendy $_POST[DateFlag], '$_POST[BeginDate]', '$_POST[EndDate]', 0, '$_POST[saler]','$_POST[pingtai]','$_POST[suffix]','$_POST[department]'";
        $res = $model->query($sql);
        $rows = [];
        $data = [];
        $final = [];
        foreach($res as $key=>$value){
            if(!in_array($value['title'],$rows)){
                array_push($rows,$value['title']);
            }
        }
        $max_dates = [];
            foreach ($rows as $key=>$value){
                $dates = [];
                $amt = [];
                $dates_amt = [];
                foreach ($res as $res_key=>$res_value){
                    if($value == $res_value['title']){
                        array_push($dates, $res_value['ordertime']);
                        array_push($amt, round($res_value['totalamt'],0));
                    }
                    $dates_amt['amt'] = $amt.'$';
                }
                array_push($data,$amt);
                $max_dates = count($dates) > count($max_dates)?$dates:$max_dates;
        }

        $final['ordertime'] = $max_dates;
        $final['title'] = $rows;
        $final['value'] = $data;

        session('final',$final);
        $this->display('trendview');

    }

    public function sales(){
        $final = session('final');
        $ret = json_encode($final);
        echo $ret;
        session('final',null);
    }


    public function roleBase(){
        $username = session('username');
        $Model = M();
        $manger = $Model->query("select * from Y_manger where manger='$username'");


        if(!empty($manger)){
            $saData = $_POST;
            //4个参数全空 按部门  0000 空时0 不空1
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){
                echo 888;
                $sql = "select Dname FROM Y_manger m
                                        LEFT JOIN Y_masterDepartment md ON md.mangerid=m.mid
                                        LEFT JOIN Y_Department d on md.departmentid=d.did
                                        WHERE manger='$username'";
                $result = $Model->query($sql);
                foreach($result as $val){
                    foreach($val as $v)
                        $res[] =$v;
                }
                $saData['department'] = implode(',',$res);
            }

            //0001
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }


            //0010
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }

            //0011
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['saler'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

            //0100
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){
                $sql = "select Dname FROM Y_manger m
                                        LEFT JOIN Y_masterDepartment md ON md.mangerid=m.mid
                                        LEFT JOIN Y_Department d on md.departmentid=d.did
                                        WHERE manger='$username'";
                $result = $Model->query($sql);
                foreach($result as $val){
                    foreach($val as $v)
                        $res[] =$v;
                }
                $saData['department'] = implode(',',$res);
                $saData['pingtai'] = rtrim(implode($_POST['pingtai'],','),',');
            }

            //0101
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['pingtai'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

            //0110
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['pingtai'] = '';
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }

            //0111
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['pingtai'] = '';
                $saData['saler'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

            //1000
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = rtrim(implode($_POST['department'],','),',');
            }

//            1001
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

//            1010
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }

            //            1011
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['saler'] = '' ;
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

//            1100
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = rtrim(implode($_POST['department'],','),',');
                $saData['pingtai'] = rtrim(implode($_POST['pingtai'],','),',');
            }

            //            1101
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['pingtai'] = '' ;
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

            //            1110
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['pingtai'] = '' ;
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }

//            1111
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){

                $saData['department'] = '';
                $saData['pingtai'] = '';
                $saData['saler'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

            $this->commsql($saData);


        }elseif($username=='admin'){
            $saData = $_POST;
            //全空 按销售
            //0000
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData = $_POST;
            }
            //0001
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }
            //0010
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }
            //0011
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['saler'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

            //0100
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){

                $saData['pingtai'] = rtrim(implode($_POST['pingtai'],','),',');
            }
            //0101
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['pingtai'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }
            //0110
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){

                $saData['pingtai'] = '';
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }
            //0111
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['pingtai'] = '';
                $saData['saler'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }
            //1000
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){

                $saData['department'] = rtrim(implode($_POST['department'],','),',');

            }
            //            1001
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }
            //            1010
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }
            //            1011
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['saler'] = '' ;
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

            //            1100
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = rtrim(implode($_POST['department'],','),',');
                $saData['pingtai'] = rtrim(implode($_POST['pingtai'],','),',');
                }
            //            1101
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['pingtai'] = '' ;
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }
            //            1110
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['pingtai'] = '' ;
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }

//            1111
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){

                $saData['department'] = '';
                $saData['pingtai'] = '';
                $saData['saler'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');

            }
            $this->commsql($saData);

        }else{
            $saData = $_POST;
            //全空 按销售
            //0000
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['saler'] = $username;
            }
            //0001
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }
            //0010
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }
            //0011
            if(empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['saler'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

            //0100
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){

                $saData['saler'] = $username;
                $saData['pingtai'] = rtrim(implode($_POST['pingtai'],','),',');
            }
            //0101
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['pingtai'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }
            //0110
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){

                $saData['pingtai'] = rtrim(implode($_POST['pingtai'],','),',');
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }
            //0111
            if(empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['pingtai'] = '';
                $saData['saler'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }


            //1000
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = '';
                $saData['saler'] = $username;
            }

            //            1001
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }
            //            1010
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }

            //            1011
            if(!empty($_POST['department'])&&empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['saler'] = '' ;
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }

            //            1100
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = '';
                $saData['pingtai'] = rtrim(implode($_POST['pingtai'],','),',');
                $saData['saler'] = $username;
            }
            //            1101
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&empty($_POST['saler'])&&!empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['pingtai'] = '' ;
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');
            }
            //            1110
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&empty($_POST['suffix'])){
                $saData['department'] = '' ;
                $saData['pingtai'] = '' ;
                $saData['saler'] = rtrim(implode($_POST['saler'],','),',');
            }

            //            1111
            if(!empty($_POST['department'])&&!empty($_POST['pingtai'])&&!empty($_POST['saler'])&&!empty($_POST['suffix'])){

                $saData['department'] = '';
                $saData['pingtai'] = '';
                $saData['saler'] = '';
                $saData['suffix'] = rtrim(implode($_POST['suffix'],','),',');

            }

            $this->commsql($saData);

        }




    }
    //公用的sql
    public function  commsql($da)
      {
          $Model = M();
          $sql = "z_p_saletrendy $da[DateFlag], '$da[BeginDate]', '$da[EndDate]', $da[Flag], '$da[saler]','$da[pingtai]','$da[suffix]','$da[department]'";

          $res = $Model->query($sql);
          $rows = [];
          $data = [];
          $final = [];
          foreach($res as $key=>$value){
              if(!in_array($value['title'],$rows)){
                  array_push($rows,$value['title']);
              }
          }
          $max_dates = [];
          foreach ($rows as $key=>$value){
              $dates = [];
              $amt = [];
              $dates_amt = [];

              foreach ($res as $res_key=>$res_value){
                  if($value == $res_value['title']){
                      array_push($dates, $res_value['ordertime']);
                      array_push($amt,round($res_value['totalamt'],0));

                  }
                  $dates_amt['amt'] = $amt;
              }
              array_push($data,$amt);
              $max_dates = count($dates) > count($max_dates)?$dates:$max_dates;
          }
          $final['ordertime'] = $max_dates;
          $final['title'] = $rows;
          $final['value'] = $data;
//          var_dump($final);die;
          session('final',$final);
          $this->display('trendview');

      }






}