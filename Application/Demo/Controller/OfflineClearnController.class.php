<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-03
 * Time: 9:46
 */

namespace Demo\Controller;
use Home\Controller\ParentController;
use Think\Controller;

class OfflineClearnController extends ParentController
{
    //美工死库 费用上传  Y_PossessOfflineClearn
    public function upload_posclearn(){

        if (IS_POST) {

            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('xlsx', 'xls');// 设置附件上传类型
            $upload->savePath = './Uploads/'; // 设置附件上传目录
            $upload->replace = true; // 覆盖文件
            // 单文件上传
            $info = $upload->uploadOne($_FILES['file']);

            if(!$info) {
                // 上传错误提示错误信息
                $this->show($upload->getError());
            }else{
                // 上传成功 获取上传文件信息
                $filediebase = $info['savepath'].$info['savename'];
                $this->import_posdiebase($filediebase);
                $txt = file_get_contents('Mylogs/fare_log.txt');
//                $this->show(nl2br($txt,'utf-8'),'text/html');
                exit;
            }
        }


    }

    public function  import_posdiebase($filediebase){
        error_reporting(E_ALL);
        date_default_timezone_set('Asia/ShangHai');
        /** PHPExcel_IOFactory */
        import('ORG.Util.PHPExcel');//手动加载第三方插件
        import("ORG.Util.PHPExcel.IOFactory.php");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.Writer.Excel2007");

        // Check prerequisites
        $filediebase = './Uploads/' . $filediebase;//创建上面文件的文件夹

        if (!file_exists($filediebase)) { //检查目或文件是否存在
            $this->show('文件不存在');
            exit;
        }
        $extension=strtolower(substr(strrchr($filediebase,"."),1));

        if( $extension =='xlsx' )
        {

            $reader = \PHPExcel_IOFactory::createReader('Excel2007');
        }
        else
        {
            $reader = \PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)

        }
        $PHPExcel = $reader->load($filediebase); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数



        for ($i=2; $i<=$highestRow; ++$i) {
//            Possess	TimeGroup	Amount	PossessClearnTime


            $data['Possess']  = $sheet->getCell("A".$i)->getValue();
            $data['TimeGroup']  = $sheet->getCell("B".$i)->getValue();
            $data['Amount']     = $sheet->getCell("C".$i)->getValue();
            $date               =  (string)$sheet->getCell("D".$i)->getValue();
            $stamp_date         = \PHPExcel_Shared_Date::ExcelToPHP($date);//将获取的奇怪数字转成时间戳，该时间戳会自动带上当前日期
            $data['PossessClearnTime'] = gmdate("Y-m-d ",$stamp_date);//这个就是excel表中的数据了，棒棒的！

            $Model = M();
            $sql = "select * from Y_PossessOfflineClearn WHERE Possess='$data[Possess]' AND  PossessClearnTime='$data[PossessClearnTime]'";
            $res = $Model->execute($sql);


            if(!$res){
                $sql = "INSERT INTO Y_PossessOfflineClearn (Possess,TimeGroup,Amount,PossessClearnTime) VALUES('$data[Possess]','$data[TimeGroup]','$data[Amount]','$data[PossessClearnTime]')";
                //$result = M('saleOpeFee')->add($data);                                                               //不存在做插入
            }else{

                $sql = "UPDATE Y_PossessOfflineClearn SET  Possess='$data[Possess]',TimeGroup='$data[TimeGroup]',Amount='$data[Amount]', PossessClearnTime='$data[PossessClearnTime]'
                        WHERE Possess='$data[Possess]' AND  PossessClearnTime='$data[PossessClearnTime]'";
            }

            $result =$Model->execute($sql);

            if($result){
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                $this->success('导入成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
                $this->error('导入失败');
            }
        }
    }






    //采购死库费用上传  Y_purOfflineClearn
    public function upload_purclearn(){

        if (IS_POST) {

            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('xlsx', 'xls');// 设置附件上传类型
            $upload->savePath = './Uploads/'; // 设置附件上传目录
            $upload->replace = true; // 覆盖文件
            // 单文件上传
            $info = $upload->uploadOne($_FILES['file']);

            if(!$info) {
                // 上传错误提示错误信息
                $this->show($upload->getError());
            }else{
                // 上传成功 获取上传文件信息
                $filediebase = $info['savepath'].$info['savename'];
                $this->import_purdiebase($filediebase);
                $txt = file_get_contents('Mylogs/fare_log.txt');
//                $this->show(nl2br($txt,'utf-8'),'text/html');
                exit;
            }
        }


    }

    public function  import_purdiebase($filediebase){
        error_reporting(E_ALL);
        date_default_timezone_set('Asia/ShangHai');
        /** PHPExcel_IOFactory */
        import('ORG.Util.PHPExcel');//手动加载第三方插件
        import("ORG.Util.PHPExcel.IOFactory.php");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.Writer.Excel2007");

        // Check prerequisites
        $filediebase = './Uploads/' . $filediebase;//创建上面文件的文件夹

        if (!file_exists($filediebase)) { //检查目或文件是否存在
            $this->show('文件不存在');
            exit;
        }
        $extension=strtolower(substr(strrchr($filediebase,"."),1));

        if( $extension =='xlsx' )
        {

            $reader = \PHPExcel_IOFactory::createReader('Excel2007');
        }
        else
        {
            $reader = \PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)

        }
        $PHPExcel = $reader->load($filediebase); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数



        for ($i=2; $i<=$highestRow; ++$i) {
//            purchaser	amount	createdDate
            $data['purchaser']  = $sheet->getCell("A".$i)->getValue();
            $data['amount'] = $sheet->getCell("B".$i)->getValue();
            $date               =  (string)$sheet->getCell("C".$i)->getValue();
            $stamp_date         = \PHPExcel_Shared_Date::ExcelToPHP($date);//将获取的奇怪数字转成时间戳，该时间戳会自动带上当前日期
            $data['createdDate'] = gmdate("Y-m-d ",$stamp_date);//这个就是excel表中的数据了，棒棒的！

            $Model = M();
            $sql = "select * from Y_purOfflineClearn WHERE purchaser='$data[purchaser]' AND  createdDate='$data[createdDate]'";
            $res = $Model->execute($sql);


            if(!$res){
                $sql = "INSERT INTO Y_purOfflineClearn (purchaser,amount,createdDate) 
                        VALUES('$data[purchaser]','$data[amount]','$data[createdDate]')";
                //$result = M('saleOpeFee')->add($data);                                                               //不存在做插入
            }else{

                $sql = "UPDATE Y_purOfflineClearn SET purchaser='$data[purchaser]',amount='$data[amount]', createdDate='$data[createdDate]'
                        WHERE purchaser='$data[purchaser]'  AND  createdDate='$data[createdDate]'";
            }

            $result =$Model->execute($sql);

            if($result){
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                $this->success('导入成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
                $this->error('导入失败');
            }
        }
    }



}