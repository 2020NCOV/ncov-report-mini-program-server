<?php
namespace app\index\controller;
use think\Request;
use think\Db;
use think\Session;
use think\Controller;
use \think\Config;

//该功能为测试功能用。
class Reportweb extends Controller{
  
    //通过web方式传递的数据，参数为get
    public function index(){
     	   $uid =input('param.uid');
           $corp_code =input('get.corp_code');
           $token = Request::instance()->param('token');
           echo $uid;
      	   echo $token;
           echo $corp_code;
           echo "999";
    }
   

}
