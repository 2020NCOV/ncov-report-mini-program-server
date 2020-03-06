<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use app\index\service\Http;
class Template extends Controller{
   public function index()
   {
       $paramCheckRes = Http::checkParams('post.uid', 'post.token');
       if (!is_array($paramCheckRes)) {
           return $paramCheckRes;
       }
       list($uid, $token) = $paramCheckRes;

       $uid = trim(Request::instance()->param('uid'));
       $token = trim(Request::instance()->param('token'));
       $user = Db::table('wx_mp_user')->where(['wid' => $uid,'token'=> $token ])->find(); 
       if(empty($user)){
           $this->error("token无效");    	
       }
       return;
       //可以通过view增加新的模板文件
       //return $this->fetch();
   }

}