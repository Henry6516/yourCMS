<?php
namespace Possess\Controller;
use Home\Controller\ParentController;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class PossessManController extends ParentController
{

    public function __construct($check=true){
        parent::__construct($check);
    }

//仅仅渲染页面
    public function show_ui_page(){
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
        $result = $M->query($sql);          //nav侧边栏

        $res = $M->query("select mid from Y_manger WHERE manger='$username'");
        $mangerid =$res[0]['mid'];
        $var = [];

        if($res){
            $possess = $M->query("SELECT DISTINCT u.username,d.Did,d.Dname FROM Y_userDepartment ud
                                        LEFT JOIN Y_user u on  u.uid=ud.uid
                                        LEFT JOIN Y_Department d on ud.Did=d.Did
                                        LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                                        LEFT JOIN Y_role r  on r.roleid=ur.roleid
                                        LEFT JOIN Y_masterDepartment md on md.departmentid=d.Did
                                        LEFT JOIN Y_manger m ON md.mangerid=m.mid
                                        WHERE  roleName='美工'
                                        AND mangerid='$mangerid'
                                        ORDER BY d.Did");


        }elseif($username == "admin"){
            $sql = "SELECT DISTINCT d.Dname, u.username,d.Did FROM Y_userDepartment ud
                    LEFT JOIN Y_user u on  u.uid=ud.uid
                    LEFT JOIN Y_Department d on ud.Did=d.Did
                    LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                    LEFT JOIN Y_role r  on r.roleid=ur.roleid
                    WHERE  roleName='美工'
                    ORDER BY d.Did";
            $possess =  $M->query($sql);

        }else{
            $possess = $M->query("SELECT DISTINCT u.username,d.Did,d.Dname FROM Y_userDepartment ud
                                        LEFT JOIN Y_user u on  u.uid=ud.uid
                                        LEFT JOIN Y_Department d on ud.Did=d.Did
                                        LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                                        LEFT JOIN Y_role r  on r.roleid=ur.roleid
                                        WHERE roleName='美工' AND u.username='$username'
                                        ORDER BY d.Did");


        }


        $pos = $this->department_possess($possess);
        $this->assign('department',$pos['department']);
        $this->assign('possess',$pos['possess']);
        $this->assign('result',$result);
        $this->assign('username',$username);
        $this->display('ui_condition_page');
    }


    //处理数据
    public function department_possess($possess){

        foreach ($possess as $k=>$v){
            $did[] = $v['did'];
            $dname[]= $v['dname'];
            $pos[]  = $v['username'];

        }
        $data['did'] = array_unique($did);
        $data['dname'] = array_unique($dname);
        $data['possess'] =array_unique($pos);
        $data['department'] = array_combine($data['did'], $data['dname']);
        ksort($data['department']);
        return $data;
    }



    //根据部门找对应的美工
     public function fetch_possess(){
       $Did = $_GET['departmentid'];
        $Model = M();
        $sql = "select u.username from Y_userDepartment d
            left JOIN Y_user  u ON u.uid=d.uid
            LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
            LEFT JOIN Y_role r ON r.roleid=ur.roleid
             WHERE d.did='$Did' and r.roleName='美工' 
             order by u.username asc";
       $res = $Model->query($sql);
       echo json_encode($res,true);
     }




    //查询数据

    public function possessman_netprofit(){
        $Model = M();
        $username = session('username');
        $data['departmentid'] = $_POST['department'];
        $data['DateFlag'] = $_POST['DateFlag'];
        $data['BeginDate'] = $_POST['BeginDate'];
        $data['EndDate'] = $_POST['EndDate'];
        $data['possessMan1'] = $_POST['possessMan1'];

        if(empty($_POST['possessMan1'])){
            if (empty($_POST['department'])) {
                $res = $Model->query("select * from Y_manger WHERE manger='$username'");                                 //是主管 则查询主管下对应的美工,admin则看所有的美工,
                $mangerid = $res[0]['mid'];
                if ($res) {
                    $possess = "SELECT DISTINCT u.username FROM Y_userDepartment ud
                                  LEFT JOIN Y_user u on  u.uid=ud.uid
                                  LEFT JOIN Y_Department d on ud.Did=d.Did
                                  LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                                  LEFT JOIN Y_role r  on r.roleid=ur.roleid
                                  LEFT JOIN Y_masterDepartment md on md.departmentid=d.Did
                                  LEFT JOIN Y_manger m ON md.mangerid=m.mid
                                  WHERE  r.roleName='美工'
                                  AND mangerid='$mangerid'";

                    $posres = $Model->query($possess);

                    foreach ($posres as $v) {
                        $da .= $v['username'] . ',';

                    }
                    $data['possessMan1'] = substr($da, 0, -1);


                } elseif ($username == 'admin') {
                    $possess = "select u.username from Y_userDepartment d
                    LEFT JOIN Y_user u ON u.uid=d.Uid
                    LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                    LEFT JOIN Y_role r ON r.roleid=ur.roleid
                    WHERE r.roleName='美工'";
                    $posres = $Model->query($possess);
                    foreach ($posres as $vv) {
                        $value .= $vv['username'] . ',';
                    }
                    $data['possessMan1'] = $value;

                } else {
                    $developer = "select u.username from Y_userDepartment d
                    LEFT JOIN Y_user u ON u.uid=d.Uid
                    LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                    LEFT JOIN Y_role r ON r.roleid=ur.roleid
                    WHERE r.roleName='美工'  and u.username ='$username' ";

                    $devsale = $Model->query($developer);

                    foreach ($devsale as $v) {
                        $da .= $v['username'] . ',';

                    }
                    $data['possessMan1'] = substr($da, 0, -1);


                }

            } else {
                $salerdepart = "select u.username from Y_userDepartment d
                    LEFT JOIN Y_user u ON u.uid=d.Uid
                    LEFT JOIN Y_user_role ur ON ur.uid=u.Uid
                    LEFT JOIN Y_role r ON r.roleid=ur.roleid
                    WHERE r.roleName='美工' and d.Did ='$data[departmentid]'";
                $devsale = $Model->query($salerdepart);
                foreach ($devsale as $v) {
                    $da .= $v['username'] . ',';

                }
                $data['possessMan1'] = substr($da, 0, -1);

            }

        }

        $sql = "exec Z_P_PossessNetProfit '$data[DateFlag]','$data[BeginDate]','$data[EndDate]','$data[possessMan1]'";

        $recoder =$Model->query($sql);

        session('recoder',$recoder);

        $this->display('possessman_netprofit');
    }


//责任人 table data
    public function possess()
    {
        $recoder = session('recoder');

        foreach ($recoder as $value){
            if($value['tabletype']=='责任人表'){
                $res[] = array_slice($value,1);
            }

        }

        echo    json_encode($res);

    }


    //导出数据

    public function export(){
        $recoder = session('recoder');
        session('recoder',null);
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
        $filename="美工毛利润报表";

        $this->exportexcel($filename,$headArr,$recoder);
    }

    /**
     * 导出数据为excel表格
     *@param $data    一个二维数组,结构如同从数据库查出来的数组
     *@param $title   excel的第一行标题,一个数组,如果为空则没有标题
     *@param $filename 下载的文件名
     *@examlpe
    $stu = M ('User');
    $arr = $stu -> select();
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