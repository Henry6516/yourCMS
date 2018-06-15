<?php

namespace Profit\Controller;

use Home\Controller\ParentController;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");

class ProfitController extends ParentController
{

    public function __construct($check = true)
    {
        parent::__construct($check);
    }

    /**
     * show_saler_page渲染页面的
     *
     */
    public function index()
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
        $mangerid = $res[0]['mid'];

        //获取角色
        $role = $M->query("SELECT roleName FROM Y_user u
                            LEFT JOIN Y_user_role ur ON ur.Uid=u.Uid
                            LEFT JOIN Y_role r ON r.roleid=ur.roleid
                            WHERE username='$username' ");
        $var = [];
        if ($res) {
            $form_condata = $M->query("SELECT d.Did ,d.Dname,u.username,ss.suffix,sp.pingtai FROM Y_userDepartment ud
                                        LEFT JOIN Y_user u ON u.Uid=ud.Uid
                                        LEFT JOIN Y_Department d ON d.Did=ud.did
                                        LEFT JOIN Y_SuffixSalerman ss ON ss.uid=u.Uid
                                        LEFT JOIN Y_suffixPingtai sp ON sp.suffix=ss.suffix
                                        WHERE mangerid='$mangerid' AND sp.pingtai IN ('eBay','Wish','Amazon','Joom','SMT')  ORDER BY ss.suffix ASC ");
            $var = $this->fetch_saler_data($form_condata);
        } elseif ($username == "admin") {
            $sql = "SELECT d.Did ,d.Dname,u.username,ss.suffix,sp.pingtai FROM Y_userDepartment ud
                    LEFT JOIN Y_user u ON u.Uid=ud.Uid
                    LEFT JOIN Y_Department d ON d.Did=ud.did
                    LEFT JOIN Y_SuffixSalerman ss ON ss.uid=u.Uid
                    LEFT JOIN Y_suffixPingtai sp ON sp.suffix=ss.suffix
                    WHERE d.Dname is not NULL AND sp.suffix is not null AND sp.pingtai IN ('eBay','Wish','Amazon','Joom','SMT')  ORDER BY ss.suffix ASC  ";
            $form_condata = $M->query($sql);
            $var = $this->fetch_saler_data($form_condata);
        } elseif ($role && $role[0]['rolename'] == "客服") {
            $form_condata = $M->query("SELECT d.Did ,d.Dname,u.username,ss.suffix,sp.pingtai FROM Y_userDepartment ud
                    LEFT JOIN Y_user u ON u.Uid=ud.Uid
                    LEFT JOIN Y_Department d ON d.Did=ud.did
                    LEFT JOIN Y_SuffixSalerman ss ON ss.uid=u.Uid
                    LEFT JOIN Y_suffixPingtai sp ON sp.suffix=ss.suffix
                    WHERE d.Dname is not NULL AND sp.suffix is not null AND sp.pingtai='eBay' ORDER BY ss.suffix ASC  ");
            $var = $this->fetch_saler_data($form_condata);
        } else {
            $form_condata = $M->query("SELECT d.Did ,d.Dname,u.username,ss.suffix,sp.pingtai FROM Y_userDepartment ud
                                        LEFT JOIN Y_user u ON u.Uid=ud.Uid
                                        LEFT JOIN Y_Department d ON d.Did=ud.did
                                        LEFT JOIN Y_SuffixSalerman ss ON ss.uid=u.Uid
                                        LEFT JOIN Y_suffixPingtai sp ON sp.suffix=ss.suffix
                                        WHERE username='$username' AND sp.pingtai IN ('eBay','Wish','Amazon','Joom','SMT')
                                         ORDER BY ss.suffix ASC  ");

            $var = $this->fetch_saler_data($form_condata);
        }
        //处理部门数组(全部部门)
        if (count($var['department']) == 10) {
            $arr = array_slice($var['department'], 0, 8, true);
            $var['department'] = $arr + ['12' => $var['department'][12]] + ['11' => $var['department'][11]];
        }
        $this->assign('result', $result);                                                                                        //nev侧边栏 对着的模块
        $this->assign('department', $var['department']);                                                                         //部门
        $this->assign('suffix', $var['suffix']);                                                                                 //账号
        $this->assign('saler', $var['saler']);                                                                           //销售员
        $this->assign('pingtai', $var['pingtai']);
        $this->assign('username', $username);
        $this->display('saler_con');
    }


    /**处理数据
     * @param $form_condata
     * @return mixed
     */
    public function fetch_saler_data($form_condata)
    {

        foreach ($form_condata as $k => $v) {
            $did[] = $v['did'];
            $dname[] = $v['dname'];
            $suffix[] = $v['suffix'];
            $saler[] = $v['username'];
            $pingtai[] = $v['pingtai'];
        }
        $data['did'] = array_unique($did);
        $data['dname'] = array_unique($dname);
        $data['suffix'] = array_unique($suffix);
        $data['saler'] = array_unique($saler);
        $data['pingtai'] = array_unique($pingtai);
        $data['department'] = array_combine($data['did'], $data['dname']);
        ksort($data['department']);
        return $data;
    }


    public function list2(){
        $result =session('result');
        echo json_encode($result,true);
    }

    /**
     * 显示查询结果列表
     */
    public function list()
    {
        $data['department'] = $_POST['department'];
        $data['pingtai'] = $_POST['pingtai'];
        $data['DateFlag'] = $_POST['DateFlag'];
        $data['BeginDate'] = trim($_POST['BeginDate']);
        $data['EndDate'] = trim($_POST['EndDate']);
        $data['saler'] = $_POST['saler'];
        $data['suffix'] = implode(',', $_POST['suffix']);
        $data['StoreName'] = implode(',', $_POST['StoreName']);
        $data['GoodsCode'] = $_POST['goodsCode'];
        $username = session('username');
        $Model = M();
        //获取角色
        $role = $Model->query("SELECT roleName FROM Y_user u
                            LEFT JOIN Y_user_role ur ON ur.Uid=u.Uid
                            LEFT JOIN Y_role r ON r.roleid=ur.roleid
                            WHERE username='$username' ");

        $manger = $Model->query("select * from Y_manger where manger='$username'");
        if ($manger) {
            if (empty($data['suffix'])) {
                $sql = "select ss.suffix from Y_SuffixSalerman ss 
                        LEFT JOIN Y_suffixPingtai sp on sp.suffix= ss.suffix
                        WHERE mangerid in (SELECT mid FROM Y_manger WHERE manger='$username')";
                $result = $Model->query($sql);
                foreach ($result as $val) {
                    foreach ($val as $v)
                        $res[] = $v;
                }
                $data['suffix'] = implode(',', $res);
            }

            if (!empty($data['suffix'])) {
                $data['suffix'] = "''$data[suffix] ''";
            }
            if (!empty($data['saler'])) {
                $data['saler'] = "''$data[saler] ''";
            }
            if (!empty($data['StoreName'])) {
                $data['StoreName'] = "''$data[StoreName] ''";
            }
        } elseif ($username === 'admin') {
            //如果部门是空的并且销售为空的  就是全部 的销售数据
            if (empty($data['saler']) || $data['saler'] == 'All默认') {
                if (empty($data['department'])) {
                    $data['saler'] = '';
                } else {
                    $sales = $this->getDepartmentSaler($data['department']);
                    $data['saler'] = "''$sales''";
                }
            } else {
                $data['saler'] = "''$data[saler] ''";
            }

            if (!empty($data['suffix'])) {
                $data['suffix'] = "''$data[suffix] ''";
            }

            if (!empty($data['StoreName'])) {
                $data['StoreName'] = "''$data[StoreName] ''";
            }
        } elseif ($role && $role[0]['rolename'] == "客服") {
            //如果部门是空的并且销售为空的  就是全部的eBay销售数据
            if (empty($data['saler']) || $data['saler'] == 'All默认') {
                if (empty($data['department'])) {
                    $data['saler'] = '';
                } else {
                    $sales = $this->getDepartmentSaler($data['department']);
                    $data['saler'] = "''$sales''";
                }
            } else {
                $data['saler'] = "''$data[saler] ''";
            }

            //如果账号为空  就是全部的eBay账号                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       销售数据
            if (empty($data['suffix'])) {
                $sql = "select ss.suffix from Y_SuffixSalerman ss 
                        LEFT JOIN Y_suffixPingtai sp on sp.suffix= ss.suffix
                        WHERE pingtai='eBay'";
                $result = $Model->query($sql);
                foreach ($result as $val) {
                    foreach ($val as $v)
                        $res[] = $v;
                }
                $data['suffix'] = implode(',', $res);
            }

            if (!empty($data['suffix'])) {
                $data['suffix'] = "''$data[suffix] ''";
            }

            if (!empty($data['StoreName'])) {
                $data['StoreName'] = "''$data[StoreName] ''";
            }
        } else {
            if (!empty($data['suffix'])) {
                $data['suffix'] = "''$data[suffix] ''";
            } else {
                $res = $Model->query("select * from Y_SuffixSalerman WHERE salesman= '$username' ");
                foreach ($res as $val) {
                    $arr_suffix[] = $val['suffix'];
                }
                $str_suffix = implode(',', $arr_suffix);
                $data['suffix'] = "''$str_suffix''";

            }
            if (!empty($data['saler'])) {
                $data['saler'] = "''$data[saler] ''";
            }
            if (!empty($data['StoreName'])) {
                $data['StoreName'] = "''$data[StoreName] ''";
            }
        }
        $tsql_callSP = "EXEC Z_P_AccountProductProfit '$data[pingtai]','$data[DateFlag]',
                        '$data[BeginDate]','$data[EndDate]','$data[suffix]',
                        '$data[saler]','$data[StoreName]','$data[GoodsCode]'";
        $result = $Model->query($tsql_callSP);
        //var_dump($result);exit;
        session('result', $result);
        $this->assign('result', $result);
        $this->display('list');
    }

    /** 根据部门 获取对应的销售
     *
     */
    public function getDepartmentSaler($departmentid){
        $userstr = '';
        $Model = M();
        $sql ="select u.username from Y_userDepartment d
            left JOIN Y_user  u ON u.uid=d.uid
            LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
            LEFT JOIN Y_role r ON r.roleid=ur.roleid
             WHERE d.did='$departmentid' and r.roleName='销售员' 
             order by u.username asc";
        $res = $Model->query($sql);
        foreach($res as $val) {
            $userstr .= $val['username'].',';

        }
        return $userstr;

    }


    /**导出数据
     *
     */
    public function export()
    {
        $result = session('result');
        session('result', null);

        foreach ($result as $field => $v) {
            if ($field == 'possessMan2') {
                $headArr[] = '责任人2';
            }
            if ($field == 'salemoneyrmbus') {
                $headArr[] = '成交价$';
            }
            if ($field == 'salemoneyrmbzn') {
                $headArr[] = '成交价￥';
            }
            if ($field == 'ppebayus') {
                $headArr[] = '交易费汇总$';
            }
            if ($field == 'ppebayzn') {
                $headArr[] = '交易费汇总￥';
            }
            if ($field == 'costmoneyrmb') {
                $headArr[] = '商品成本￥';
            }
            if ($field == 'expressfarermb') {
                $headArr[] = '运费成本￥';
            }
            if ($field == 'inpackagefeermb') {
                $headArr[] = '包装成本￥';
            }
            if ($field == 'diefee') {
                $headArr[] = '死库处理￥';
            }
            if ($field == 'refund') {
                $headArr[] = '退款金额￥';
            }
            if ($field == 'refundrate') {
                $headArr[] = '退款率%';
            }
            if ($field == 'shopMultifee') {
                $headArr[] = '店铺杂费￥';
            }
            if ($field == 'opfee') {
                $headArr[] = '运营杂费￥';
            }
            if ($field == 'netprofit') {
                $headArr[] = '毛利￥';
            }
            if ($field == 'netrate') {
                $headArr[] = '毛利率%';
            }


        }

        $filename = "eBay销售毛利润报表";

        $this->exportexcel($filename, $headArr, $result);
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
    function exportexcel($filename, $headArr, $data)
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
        $key2 = ord("@");//@--64
        //print_r($headArr);exit;
        foreach ($headArr as $v) {
            if ($key > ord("Z")) {
                $key2 += 1;
                $key = ord("A");
                $colum = chr($key2) . chr($key);//超过26个字母时才会启用
            } else {
                if ($key2 >= ord("A")) {
                    $colum = chr($key2) . chr($key);//超过26个字母时才会启用
                } else {
                    $colum = chr($key);
                }
            }
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $key += 1;
        }

        $col = 2;

        foreach ($data as $ke => $rows) {
            $span = ord("A");
            $span2 = ord("@");//@--64
            //行写入
            foreach ($rows as $v) {
                if ($span > ord("Z")) {
                    $span2 += 1;
                    $span = ord("A");
                    $column = chr($span2) . chr($span);//超过26个字母时才会启用
                } else {
                    if ($span2 >= ord("A")) {
                        $column = chr($span2) . chr($span);//超过26个字母时才会启用
                    } else {
                        $column = chr($span);
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column . $col, $v);
                $span += 1;

            }
            $col++;

        }
        $fileName = iconv("utf-8", "gb2312", $filename);

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
