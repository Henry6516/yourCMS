<?php
namespace Demo\Controller;
use Home\Controller\ParentController;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class IndexController extends ParentController
{






    //主方法 判断 不同角色的 人员
    public function index()
    {
        $con = M();
        $username = session('username');

        $Model =M();
        if($username=='admin'){
            $sql = "select u.username from Y_userDepartment d
                    LEFT JOIN Y_user u ON u.uid=d.Uid
                    WHERE role='开发员'";

        }elseif($username=='毕郑强'||
                $username=='韩珍'||
                $username=='张葵'||
                $username=='吴志超'||
                $username=='方蓓'||
                $username=='尚显贝'
                ){
            $sql ="SELECT d.developer as username from Y_manger m
                    LEFT JOIN Y_masterDev d ON m.mid=d.masterid
                    WHERE m.manger='$username'";
        }elseif($username=='黄烽殷'||
            $username=='姜娅文'||
            $username=='李莎莎'||
            $username=='吕圆'||
            $username=='彭冉冉'||
            $username=='张葵'||
            $username=='章兰君'||
            $username=='周源'||
            $username=='朱海洋'){
            $sql = "select username FROM Y_user where username='$username'";

        }else{
            $sql ="select developer as username FROM Y_masterDev where developer='$username' ";


        }
        $result = $Model->query($sql);
        echo json_encode($result);
    }



    public  function test()
    {
        $username = session('username');
        $Model = M();
        $data['DateFlag'] = I('post.DateFlag');
        $data['salername'] = I('post.salername');
        $data['BeginDate'] = I('post.BeginDate');
        $data['EndDate'] = I('post.EndDate');
        $res = $Model->query("select mid from Y_manger WHERE manger='$username'");


        if(empty($_POST['salername'])){

            if (empty($_POST['department'])) {
                if ($res) {
                    $developer = "SELECT d.developer as username from Y_manger m
                    LEFT JOIN Y_masterDev d ON m.mid=d.masterid
                    WHERE m.manger='$username'";
                    $devres = $Model->query($developer);
                    foreach ($devres as $vv) {
                        $value .= $vv['username'] . ',';
                    }

                    $data['salername'] = substr($value, 0, -1);

                } elseif ($username == 'admin') {
                    $developer = "select u.username from Y_userDepartment d
                    LEFT JOIN Y_user u ON u.uid=d.Uid
                    WHERE role='开发员'";
                    $devres = $Model->query($developer);
                    foreach ($devres as $vv) {
                        $value .= $vv['username'] . ',';
                    }
                    $data['salername'] = $value;

                } else {
                    $developer = "select developer as username FROM Y_masterDev where developer='$username' ";
                    $devres = $Model->query($developer);
                    foreach ($devres as $vv) {
                        $value .= $vv['username'] . ',';
                    }
                    $data['salername'] = substr($value, 0, -1);
                }

            } else {

                if($res){
                    $devdepart = "select developer from Y_masterDev v
                                LEFT JOIN Y_masterDepartment t  ON v.masterid= t.mangerid
                                WHERE departmentid='$_POST[department]'";
                }else{
                    $devdepart = "select developer as username FROM Y_masterDev where developer='$username'";
                }


                $developer = $Model->query($devdepart);

                $data['salername'] = $developer[0]['username'];
            }

        }

        $sql = "exec Z_P_DevNetprofit '$data[DateFlag]','$data[BeginDate]','$data[EndDate]','','$data[salername]','','','','','','','0','0','0','0'";

        $recoder =$Model->query($sql);
        session('recoder',$recoder);
        $this->display('test');
    }


//业绩归属人1 table data
    public function salername()
    {
        $recoder = session('recoder');
        foreach ($recoder as $value){
            if($value['tabletype']=='归属1人表'){
                $res[] = array_slice($value,1);
            }

        }
        $ret = json_decode($res);
        echo    json_encode($res);
    }

    public function introducer()
    {
        $recoder = session('introducer');
        foreach ($recoder as $value){
            if($value['tabletype']=='推荐人表'){
                $res[] = array_slice($value,1);
            }
        }
        echo    json_encode($res);
    }


//业绩归属人2的表数据
    public function SalerName2()
    {

        $recoder = session('recoder');
        foreach ($recoder as $value){
            if($value['tabletype']=='归属2人表'){
                $res[] = array_slice($value,1);
            }

        }

        echo    json_encode($res);

    }



    //导出数据

    public function export_dev(){
        $recoder = session('recoder');
//        session('recoder',null);
        foreach ($recoder as $field=>$v){
            if($field == 'tableType'){
                $headArr[]='表类型';
            }
            if($field == 'timegroupzero'){
                $headArr[]='时间分组';
            }
            if($field == 'salernameZero'){
                $headArr[]='业绩归属人';
            }

            if($field == 'salemoneyrmbuszero'){
                $headArr[]='销售额$(0-6月)';
            }
            if($field == 'salemoneyrmbznzero'){
                $headArr[]='销售额￥(0-6月)';
            }
            if($field == 'costmoneyrmbzero'){
                $headArr[]='商品成本￥(0-6月)';
            }
            if($field == 'ppebayuszero'){
                $headArr[]='交易费汇总$(0-6月)';
            }
            if($field == 'ppebayznzero'){
                $headArr[]='交易费汇总￥(0-6月)';
            }
            if($field == 'inpackagefeermbzero'){
                $headArr[]='内包装成本￥(0-6月)';
            }
            if($field == 'expressfarermbzero'){
                $headArr[]='运费成本￥(0-6月)';
            }
            if($field == 'devofflinefeezero'){
                $headArr[]='死库费用￥(0-6月)';
            }
            if($field == 'devOpeFeeZero'){
                $headArr[]='运营杂费(0-6月)';//	devOpeFeeSix	devOpeFeeTwe
            }
            if($field == 'netprofitzero'){
                $headArr[]='毛利润￥(0-6月)';

            }
            if($field == 'netratezero'){
                $headArr[]='毛利润率%(0-6月)';
            }


            if($field == 'timegroupSix'){
                $headArr[]='时间分组';
            }

            if($field == 'salemoneyrmbusSix'){
                $headArr[]='销售额$(6-12月)';
            }
            if($field == 'salemoneyrmbznSix'){
                $headArr[]='销售额￥(6-12月)';
            }
            if($field == 'costmoneyrmbSix'){
                $headArr[]='商品成本￥(6-12月)';
            }
            if($field == 'ppebayusSix'){
                $headArr[]='交易费汇总$(6-12月)';
            }
            if($field == 'ppebayznSix'){
                $headArr[]='交易费汇总￥(6-12月)';
            }
            if($field == 'inpackagefeermbSix'){
                $headArr[]='内包装成本￥(6-12月)';
            }
            if($field == 'expressfarermbSix'){
                $headArr[]='运费成本￥(6-12月)';
            }
            if($field == 'devofflinefeeSix'){
                $headArr[]='死库费用￥(6-12月)';
            }
            if($field == 'devOpeFeeSix'){
                $headArr[]='运营杂费(6-12月)';//		devOpeFeeTwe
            }
            if($field == 'netprofitSix'){
                $headArr[]='毛利润￥(6-12月)';
            }
            if($field == 'netrateSix'){
                $headArr[]='毛利润率%(6-12月)';
            }

            if($field == 'timegroupTwe'){
                $headArr[]='时间分组';
            }

            if($field == 'salemoneyrmbusTwe'){
                $headArr[]='销售额$(12月以上)';
            }
            if($field == 'salemoneyrmbznTwe'){
                $headArr[]='销售额￥(12月以上)';
            }
            if($field == 'costmoneyrmbTwe'){
                $headArr[]='商品成本￥(12月以上)';
            }
            if($field == 'ppebayusTwe'){
                $headArr[]='交易费汇总$(12月以上)';
            }
            if($field == 'ppebayznTwe'){
                $headArr[]='交易费汇总￥(12月以上)';
            }
            if($field == 'inpackagefeermbTwe'){
                $headArr[]='内包装成本￥(12月以上)';
            }
            if($field == 'expressfarermbTwe'){
                $headArr[]='运费成本￥(12月以上)';
            }
            if($field == 'devofflinefeeTwe'){
                $headArr[]='死库费用￥(12月以上)';
            }
            if($field == 'devOpeFeeTwe'){
                $headArr[]='运营杂费(12月以上)';
            }
            if($field == 'netprofitTwe'){
                $headArr[]='毛利润￥(12月以上)';
            }
            if($field == 'netrateTwe'){
                $headArr[]='毛利润率%(12月以上)';
            }

            if($field == 'salemoneyrmbtotal'){
                $headArr[]='销售额￥(汇总)';
            }
            if($field == 'netprofittotal'){
                $headArr[]='毛利润￥(汇总)';
            }
            if($field == 'netratetotal'){
                $headArr[]='毛利润率%(汇总)';
            }

        }
        $filename="开发毛利润报表";
        $this->exportexcel_dev($filename,$headArr,$recoder);
    }

    public function exportexcel_dev($filename,$headArr,$data){
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