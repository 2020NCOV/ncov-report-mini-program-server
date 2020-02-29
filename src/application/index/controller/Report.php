<?php
namespace app\index\controller;
use think\Request;
use think\Db;
use think\Session;
use think\Controller;
use \think\Config;

class Report extends Base{
  
   
    public function save()
    {
           $uid =input('post.uid');
           $token =input('post.token'); 
           $data =input('post.data');
           $template_code =input('post.template_code');
           $data    = json_decode($data,true);
           
      
          if( empty($uid) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:uid'
           		]);
            }
            if( empty($token) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:token'
           		]);
            }
            if( empty($data['current_district_value']) || strlen($data['current_district_value']) <3 )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:data'
           		]);
            }
 
            //获取用户所属的机构
            $user_bind = Db::table('wx_mp_bind_info')
            ->alias("u") 
            ->join('organization o', 'u.org_id = o.id','LEFT')
            ->field('u.org_id,o.corpname,o.corp_code,o.template_code')
            ->where(['u.wx_uid' => $uid,'u.isbind' => 1])
            ->find();
            if(!empty($user_bind)){
                 $record_table = "report_record_".$user_bind['template_code'];
            }else{
                return json([
                        'errcode'        => 10006,
                        'msg'         =>"获取用户绑定信息失败"
                ]);
            }     
      
            $user = Db::table('wx_mp_user')->where(['wid' => $uid ])->find();
          
            if(empty($user)){
                return json([
                        'errcode'        => 10006,
                        'msg'         =>"获取用户信息失败"
                ]);
            }     
      
           $data['wxuid'] = $uid;
           $data['template_code'] = $template_code;
           $data['org_id'] = $user_bind['org_id'];
           $data['org_name'] = $user_bind['corpname']; 
           $data['userID'] = $user['userid'];
           $data['name'] = $user['name'];
           //此处未检测数据的合法性及是否为空
           $res = Db::table($record_table)->insert($data);
           if($res !== false){
                return json([
                    'errcode'        => 0,
                    'msg'        => '数据提交成功'
                ]);
           }
     }
  
     
     public function getlastdata()
     {
           $uid =input('post.uid');
           $token =input('post.token'); 

           if( empty($uid) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:uid'
           		]);
            }
            if( empty($token) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:token'
           		]);
            }
       
       
          //获取用户所属的机构
            $user_bind = Db::table('wx_mp_bind_info')
            ->alias("u") 
            ->join('organization o', 'u.org_id = o.id','LEFT')
            ->field('u.org_id,o.corpname,o.corp_code,o.template_code')
            ->where(['u.wx_uid' => $uid,'u.isbind' => 1])
            ->find();
            if(!empty($user_bind)){
                 $record_table = "report_record_".$user_bind['template_code'];
            }else{
                return json([
                        'errcode'        => 10006,
                        'msg'         =>"获取用户绑定信息失败"
                ]);
            }     
      
           $res_list = Db::table($record_table)
                    ->where('wxuid','=',$uid)
                    ->select(); 
           
       
           $count_num = count($res_list)-1;
           if(count($res_list) >0){
                $data = $res_list[$count_num]; 
                $data['return_district_path'] = $this->get_district_path($data['return_district_value']);
           		$data['current_district_path'] = $this->get_district_path($data['current_district_value']);
               return json([
                        'errcode'        => 0,
                 		'isEmpty'        => 0,
                        'data'        => $data
               ]);
           }else{
               return json([
                        'errcode'        => 0,
                        'isEmpty'        => 1,
                        'data'        => ''
               ]);
           }
     }
  
    
  
  
     public function get_district_path($city_code){
        $pathstr= '';
     	$data = Db::table('com_district')
          ->where('value','=',$city_code)
          ->select();
        if(count($data)>0){
        	if($data[0]['level_id'] == 1){
            	$pathstr = $data[0]['name'];
            }else{
            	$pathstr = $data[0]['name'];
                $data = Db::table('com_district')
                        ->where('value','=',$data[0]['parent_id'])
                        ->select();
                if(count($data)>0){
                	$pathstr = $data[0]['name'].",".$pathstr;
                }
            }	
        }
     	return $pathstr;
     }
}
