<?php
namespace Purchase\Controller;
use Home\Controller\ParentController;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class PurchaseController extends ParentController
{

    public function __construct($check=true){
        parent::__construct($check);
    }

    public function show_purchase_page(){
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
        $mangerid =$res[0]['mid'];
        $var = [];
        if($res){
            $purchase_sql = "SELECT DISTINCT u.username FROM Y_user u
                            LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                            LEFT JOIN Y_role r  on r.roleid=ur.roleid
                            LEFT JOIN Y_userDepartment ud ON ud.Uid=u.Uid
                            LEFT JOIN Y_Department d ON d.Did=ud.Did
                            LEFT JOIN Y_masterDepartment md on md.departmentid=d.Did
                            LEFT JOIN Y_manger m ON md.mangerid=m.mid
                            WHERE r.roleName='采购员'
                            AND mangerid='$mangerid'";
        }elseif($username == "admin"){
            $purchase_sql = "SELECT DISTINCT u.username FROM Y_user u
                              LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                              LEFT JOIN Y_role r  on r.roleid=ur.roleid
                              WHERE r.roleName='采购员'";

        }else{
            $purchase_sql = "SELECT DISTINCT u.username FROM Y_user u
                              LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                              LEFT JOIN Y_role r  on r.roleid=ur.roleid
                              WHERE r.roleName='采购员' AND u.username='$username'";


        }
        $purchase_res = $M->query($purchase_sql);
        $this->assign('purchase',$purchase_res);
        $this->assign('result',$result);
        $this->assign('username',$username);
        $this->display('purchase_con');
    }


    public function purchase_netprofit(){
        $username = session('username');
        $M=M();
        $data['Purchase']  = $_POST['Purchase'];
        $data['DateFlag']  = $_POST['DateFlag'];
        $data['BeginDate'] = $_POST['BeginDate'];
        $data['EndDate']   = $_POST['EndDate'];

        if(empty($_POST['Purchase'])){            
                $res = $M->query("select * from Y_manger WHERE manger='$username'");                                 //是主管 则查询主管下对应的采购,admin则看所有的采购,
                $mangerid = $res[0]['mid'];
                if ($res) {
                    $possess = "SELECT DISTINCT u.username FROM Y_user u
                                LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                                LEFT JOIN Y_role r  on r.roleid=ur.roleid
                                LEFT JOIN Y_userDepartment ud ON ud.Uid=u.Uid
                                LEFT JOIN Y_Department d ON d.Did=ud.Did
                                LEFT JOIN Y_masterDepartment md on md.departmentid=d.Did
                                LEFT JOIN Y_manger m ON md.mangerid=m.mid
                                WHERE r.roleName='采购员'
                                AND mangerid='$mangerid'";

                    $purres = $M->query($possess);

                    if(empty($purres)){
                        $data['Purchase'] = '没有采购员';
                    }else{
                        foreach ($purres as $v) {
                            $da .= $v['username'] . ',';

                        }
                        $data['Purchase'] = substr($da, 0, -1);
                    }

                } elseif ($username == 'admin') {
                    $possess = "SELECT DISTINCT u.username FROM Y_user u
                                LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                                LEFT JOIN Y_role r  on r.roleid=ur.roleid
                                LEFT JOIN Y_userDepartment ud ON ud.Uid=u.Uid
                                LEFT JOIN Y_Department d ON d.Did=ud.Did
                                LEFT JOIN Y_masterDepartment md on md.departmentid=d.Did
                                LEFT JOIN Y_manger m ON md.mangerid=m.mid
                                WHERE r.roleName='采购员'";
                    $purres = $M->query($possess);
                    foreach ($purres as $vv) {
                        $value .= $vv['username'] . ',';
                    }
                    $data['Purchase'] = $value;

                } else {
                    $purchase = "SELECT DISTINCT u.username FROM Y_user u
                                    LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                                    LEFT JOIN Y_role r  on r.roleid=ur.roleid
                                    LEFT JOIN Y_userDepartment ud ON ud.Uid=u.Uid
                                    LEFT JOIN Y_Department d ON d.Did=ud.Did
                                    LEFT JOIN Y_masterDepartment md on md.departmentid=d.Did
                                    LEFT JOIN Y_manger m ON md.mangerid=m.mid
                                    WHERE r.roleName='采购员'
                                    AND u.username='$username'";

                    $pur = $M->query($purchase);

                    foreach ($pur as $v) {
                        $da .= $v['username'] . ',';

                    }
                    $data['Purchase'] = substr($da, 0, -1);


                }

       

        }

        $sql = "exec z_p_purchaserProfit '1','$data[BeginDate]','$data[EndDate]','','','0','','','','','','$data[Purchase]','0','0','0',0";
        $result = $M->query($sql);
        //echo $sql;die;
        session('result',$result);
        $this->display('purchase_netprofit');

    }

    public function echo_purchase(){
        $result =session('result');
        echo json_encode($result,true);
    }

    //导出数据

    public function export(){
        $result = session('result');
        session('$result',null);

        foreach ($result as $field=>$v){
            if($field == 'purchaser'){
                $headArr[]='采购员';
            }
            if($field == 'salemoneyrmbus'){
                $headArr[]='成交价$';
            }
            if($field == 'salemoneyrmbzn'){
                $headArr[]='成交价￥';
            }
            if($field == 'ppebayus'){
                $headArr[]='交易费汇总$';
            }
            if($field == 'ppebayzn'){
                $headArr[]='交易费汇总￥';
            }
            if($field == 'costmoneyrmb'){
                $headArr[]='商品成本￥';
            }
            if($field == 'expressfarermb'){
                $headArr[]='运费成本￥';
            }
            if($field == 'inpackagefeermb'){
                $headArr[]='包装成本￥';
            }
            if($field == 'devofflinefee'){
                $headArr[]='死库处理￥';
            }
            if($field == 'devopefee'){
                $headArr[]='运营杂费￥';
            }
            if($field == 'netprofit'){
                $headArr[]='毛利￥';
            }
            if($field == 'netrate'){
                $headArr[]='毛利率%';
            }
            if($field == 'totalamount'){
                $headArr[]='采购差额￥';
            }

        }

        $filename="采购毛利润报表";

        $this->exportexcel($filename,$headArr,$result);
    }

    /**
     * 导出数据为excel表格
     *@param $data    一个二维数组,结构如同从数据库查出来的数组
     *@param $title   excel的第一行标题,一个数组,如果为空则没有标题
     *@param $filename 下载的文件名
     *@examlpe
    $stu = M ('User');
    $arr = $stu -> select();
    exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
     */
    function exportexcel($filename,$headArr,$data){
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
        foreach($headArr as $v){
            if($key>ord("Z")){
                $key2 += 1;
                $key = ord("A");
                $colum = chr($key2).chr($key);//超过26个字母时才会启用
            }else{
                if($key2>=ord("A")){
                    $colum = chr($key2).chr($key);//超过26个字母时才会启用
                }else{
                    $colum = chr($key);
                }
            }
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $col = 2;

        foreach($data as $ke => $rows){
            $span = ord("A");
            $span2 = ord("@");//@--64
            //行写入
            foreach($rows as $v){
                if($span>ord("Z")){
                    $span2 += 1;
                    $span = ord("A");
                    $column = chr($span2).chr($span);//超过26个字母时才会启用
                }else{
                    if($span2>=ord("A")){
                        $column = chr($span2).chr($span);//超过26个字母时才会启用
                    }else{
                        $column = chr($span);
                    }
                }
                $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($column.$col, $v);
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