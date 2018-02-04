<?php
namespace Home\Controller;
use Think\Controller;
class DepartmentController extends Controller {

	public function fetch_userde(){

		$Model = M();
		$sql ="select u.username from Y_userDepartment d
                left JOIN Y_user  u ON u.uid=d.uid
                 WHERE d.did='$_GET[opration]' and d.role='销售员' 
                 order by u.username asc";
		$result = $Model->query($sql);

        if(isset($result)){
            echo json_encode($result,true);
        }
	}

	public function dev_department(){
//	    dump($_GET);die;

        $Model = M();
        $sql ="select u.username from Y_userDepartment d
                left JOIN Y_user  u ON u.uid=d.uid
                 WHERE d.did='$_GET[opration]' and d.role='开发员' 
                 order by u.username asc";
        $result = $Model->query($sql);

        if(isset($result)){
            echo json_encode($result,true);
        }
    }


}