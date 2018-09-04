<?php

namespace Introducer\Controller;

use Home\Controller\ParentController;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");

class IndexController extends ParentController
{

    public function __construct($check = true)
    {
        parent::__construct($check);
    }

//显示开发条件页面
    public function show_dev_page()
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
        $result = $M->query($sql);
        $res = $M->query("select mid from Y_manger WHERE manger='$username'");
        $mangerid = $res[0]['mid'];
        if ($res) {
            $sql = "select DISTINCT u.username,ud.Did,d.Dname 
                from Y_user u
                LEFT JOIN Y_userDepartment ud ON ud.Uid=u.Uid
                LEFT JOIN Y_Department d ON d.Did=ud.Did
                LEFT JOIN Y_masterDepartment md ON md.departmentid=d.Did
                WHERE  mangerid='$mangerid'";
        } elseif ($username == 'admin') {
            $sql = "select u.username,ud.Did,d.Dname 
                    from Y_user u
                    INNER JOIN Y_userDepartment ud ON ud.Uid=u.Uid
                    INNER JOIN Y_Department d ON d.Did=ud.Did";
        } else {
            $sql = "select u.username,ud.Did,d.Dname 
                    from Y_user u
                    INNER JOIN Y_userDepartment ud ON ud.Uid=u.Uid
                    INNER JOIN Y_Department d ON d.Did=ud.Did
                    WHERE  u.username='$username'";
        }

        $developer = $M->query($sql);
        $data = array();
        foreach($developer as $v){
            $depart[$v['did']] = $v['dname'];
            $dev[]  = $v['username'];
        }
        $data['dev'] = array_unique($dev);
        $data['department'] = $depart;
        ksort($data['department']);
        $this->assign('department', $data['department']);
        $this->assign('dev', $data['dev']);
        $this->assign('result', $result);
        $this->assign('username', $username);
        $this->display('dev_con');
    }

    //处理 部门对应的开发
    public function department_dev($developer)
    {
        foreach ($developer as $v) {
            $did[] = $v['did'];
            $dname[] = $v['dname'];
            $pos[] = $v['username'];
        }
    }


    // 判断 不同角色的 人员
    public function fetch_dev()
    {

        $Model = M();
        $username = session('username');
        $res = $Model->query("select mid from Y_manger WHERE manger='$username'");
        $mangerid = $res[0]['mid'];
        $var = [];
        if ($username == 'admin') {
            $sql = "select u.username,ud.Did,d.Dname from Y_role r
                    LEFT JOIN Y_user_role ur on ur.roleid=r.roleid
                    LEFT JOIN Y_user u ON u.uid=ur.uid
                    LEFT JOIN Y_userDepartment ud ON ud.Uid=u.Uid
                    LEFT JOIN Y_Department d ON d.Did=ud.Did
                    WHERE rolename='开发员'";

        } elseif ($res) {
            $sql = "select u.username,ud.Did,d.Dname from Y_role r
                    LEFT JOIN Y_user_role ur on ur.roleid=r.roleid
                    LEFT JOIN Y_user u ON u.uid=ur.uid
                    LEFT JOIN Y_userDepartment ud ON ud.Uid=u.Uid
                    LEFT JOIN Y_Department d ON d.Did=ud.Did
                    WHERE rolename='开发员'AND u.username='$username'";
        } else {
            $sql = "select developer as username FROM Y_masterDev where developer='$username' ";

        }
        $result = $Model->query($sql);
        echo json_encode($result);
    }


    public function test()
    {
        $username = session('username');
        $Model = M();
        $data['DateFlag'] = I('post.DateFlag');
        $data['salername'] = I('post.salername');
        $data['BeginDate'] = I('post.BeginDate');
        $data['EndDate'] = I('post.EndDate');
        $res = $Model->query("select mid from Y_manger WHERE manger='$username'");
        $departModel = $Model->query("select did from Y_userDepartment d LEFT JOIN Y_user u ON u.uid=d.uid WHERE username='$username'");
        $did = $departModel[0]['did'];
        $value = '';
        if (empty($_POST['salername'])) {
            if (empty($_POST['department'])) {
                if ($res) {
                    $developer = "SELECT u.username from Y_user u
                    LEFT JOIN Y_userDepartment d ON u.uid=d.uid
                    WHERE d.did='$did'";
                    $devres = $Model->query($developer);
                    foreach ($devres as $vv) {
                        $value .= $vv['username'] . ',';
                    }
                    $data['salername'] = substr($value, 0, -1);
                } elseif ($username == 'admin') {
                    $developer = "select u.username from Y_user u 
                    INNER JOIN Y_userDepartment ud ON ud.Uid=u.Uid
                    WHERE ISNULL(did,'')<>''";
                    $devres = $Model->query($developer);
                    foreach ($devres as $vv) {
                        $value .= $vv['username'] . ',';
                    }
                    $data['salername'] = $value;
                } else {
                    $data['salername'] = $username;
                }
            } else {
                if ($res || $username=='admin') {
                    $devdepart = "select u.username from Y_user u
                                LEFT JOIN Y_userDepartment d ON u.uid=d.uid
                                WHERE did='$_POST[department]'";
                    $devres = $Model->query($devdepart);
                    foreach ($devres as $vv) {
                        $value .= $vv['username'] . ',';
                    }
                    $data['salername'] = substr($value, 0, -1);
                } else {
                    $data['salername'] = $username;
                }
            }
        }

    //    $sql = "exec P_DevNetprofit_advanced '$data[DateFlag]','$data[BeginDate]','$data[EndDate]','','$data[salername]','','','','','','','0','0','0','0'";
        $sql = "exec P_RefereeProfit_advanced '$data[DateFlag]','$data[BeginDate]','$data[EndDate]','$data[salername]'";
       //print_r($sql);exit;
        $recoder = $Model->query($sql);
        session('introducer', $recoder);
        $this->display('dev_netprofit');
    }

    //根据部门获取用户列表
    public function getDepartUser(){
        $Model = M();
        $sql ="select u.username from Y_userDepartment d
                INNER JOIN Y_user  u ON u.uid=d.uid
                 WHERE d.did='$_GET[opration]' and d.role='销售员' 
                 order by u.username asc";
        $result = $Model->query($sql);

        if(isset($result)){
            echo json_encode($result,true);
        }
    }



//业绩归属人1 table data
    public function salername()
    {
        $recoder = session('recoder');
        foreach ($recoder as $value) {
            if ($value['tabletype'] == '归属1人表') {
                $res[] = array_slice($value, 1);
            }
        }
        echo json_encode($res);
    }


//业绩归属人2的表数据
    public function SalerName2()
    {

        $recoder = session('recoder');
        foreach ($recoder as $value) {
            if ($value['tabletype'] == '归属2人表') {
                $res[] = array_slice($value, 1);
            }

        }
        echo json_encode($res);

    }


    //导出数据

    public function export()
    {
        $recoder = session('recoder');
        session('recoder', null);
        foreach ($recoder as $field => $v) {
            if ($field == 'tableType') {
                $headArr[] = '表类型';
            }
            if ($field == 'timegroupzero') {
                $headArr[] = '时间分组';
            }
            if ($field == 'salernameZero') {
                $headArr[] = '业绩归属人';
            }

            if ($field == 'salemoneyrmbuszero') {
                $headArr[] = '销售额$(0-6月)';
            }
            if ($field == 'salemoneyrmbznzero') {
                $headArr[] = '销售额￥(0-6月)';
            }
            if ($field == 'costmoneyrmbzero') {
                $headArr[] = '商品成本￥(0-6月)';
            }
            if ($field == 'ppebayuszero') {
                $headArr[] = '交易费汇总$(0-6月)';
            }
            if ($field == 'ppebayznzero') {
                $headArr[] = '交易费汇总￥(0-6月)';
            }
            if ($field == 'inpackagefeermbzero') {
                $headArr[] = '内包装成本￥(0-6月)';
            }
            if ($field == 'expressfarermbzero') {
                $headArr[] = '运费成本￥(0-6月)';
            }
            if ($field == 'devofflinefeezero') {
                $headArr[] = '死库费用￥(0-6月)';
            }
            if ($field == 'devOpeFeeZero') {
                $headArr[] = '运营杂费(0-6月)';//	devOpeFeeSix	devOpeFeeTwe
            }
            if ($field == 'netprofitzero') {
                $headArr[] = '毛利润￥(0-6月)';

            }
            if ($field == 'netratezero') {
                $headArr[] = '毛利润率%(0-6月)';
            }


            if ($field == 'timegroupSix') {
                $headArr[] = '时间分组';
            }

            if ($field == 'salemoneyrmbusSix') {
                $headArr[] = '销售额$(6-12月)';
            }
            if ($field == 'salemoneyrmbznSix') {
                $headArr[] = '销售额￥(6-12月)';
            }
            if ($field == 'costmoneyrmbSix') {
                $headArr[] = '商品成本￥(6-12月)';
            }
            if ($field == 'ppebayusSix') {
                $headArr[] = '交易费汇总$(6-12月)';
            }
            if ($field == 'ppebayznSix') {
                $headArr[] = '交易费汇总￥(6-12月)';
            }
            if ($field == 'inpackagefeermbSix') {
                $headArr[] = '内包装成本￥(6-12月)';
            }
            if ($field == 'expressfarermbSix') {
                $headArr[] = '运费成本￥(6-12月)';
            }
            if ($field == 'devofflinefeeSix') {
                $headArr[] = '死库费用￥(6-12月)';
            }
            if ($field == 'devOpeFeeSix') {
                $headArr[] = '运营杂费(6-12月)';//		devOpeFeeTwe
            }
            if ($field == 'netprofitSix') {
                $headArr[] = '毛利润￥(6-12月)';
            }
            if ($field == 'netrateSix') {
                $headArr[] = '毛利润率%(6-12月)';
            }

            if ($field == 'timegroupTwe') {
                $headArr[] = '时间分组';
            }

            if ($field == 'salemoneyrmbusTwe') {
                $headArr[] = '销售额$(12月以上)';
            }
            if ($field == 'salemoneyrmbznTwe') {
                $headArr[] = '销售额￥(12月以上)';
            }
            if ($field == 'costmoneyrmbTwe') {
                $headArr[] = '商品成本￥(12月以上)';
            }
            if ($field == 'ppebayusTwe') {
                $headArr[] = '交易费汇总$(12月以上)';
            }
            if ($field == 'ppebayznTwe') {
                $headArr[] = '交易费汇总￥(12月以上)';
            }
            if ($field == 'inpackagefeermbTwe') {
                $headArr[] = '内包装成本￥(12月以上)';
            }
            if ($field == 'expressfarermbTwe') {
                $headArr[] = '运费成本￥(12月以上)';
            }
            if ($field == 'devofflinefeeTwe') {
                $headArr[] = '死库费用￥(12月以上)';
            }
            if ($field == 'devOpeFeeTwe') {
                $headArr[] = '运营杂费(12月以上)';
            }
            if ($field == 'netprofitTwe') {
                $headArr[] = '毛利润￥(12月以上)';
            }
            if ($field == 'netrateTwe') {
                $headArr[] = '毛利润率%(12月以上)';
            }

            if ($field == 'salemoneyrmbtotal') {
                $headArr[] = '销售额￥(汇总)';
            }
            if ($field == 'netprofittotal') {
                $headArr[] = '毛利润￥(汇总)';
            }
            if ($field == 'netratetotal') {
                $headArr[] = '毛利润率%(汇总)';
            }

        }
        $filename = "开发毛利润报表";

        $this->exportexcel($recoder, $headArr, $filename);
    }

    /**
     * 导出数据为excel表格
     * @param $data    一个二维数组,结构如同从数据库查出来的数组
     * @param $title   excel的第一行标题,一个数组,如果为空则没有标题
     * @param $filename 下载的文件名
     * @examlpe
    $stu = M ('User');
     * $arr = $stu -> select();
     * exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
     */
    function exportexcel($data = array(), $title = array(), $filename = 'report')
    {
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能import导入
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.IOFactory.php");
        $filename .= ".xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
        //设置表头
        $key = ord("A");
        //print_r($headArr);exit;
        foreach ($title as $v) {
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach ($data as $key => $rows) { //行写入
            $span = ord("A");
            foreach ($rows as $keyName => $value) {// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j . $column, $value);
                $span++;
            }
            $column++;
        }
        $fileName = iconv("utf-8", "gb2312", $filename);

        //重命名表
        //$objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename='$fileName'");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;


    }
}