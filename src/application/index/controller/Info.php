<?php
namespace app\index\controller;
use think\Request;
use think\Db;
use think\Session;
use think\Controller;
use \think\Config;

class Info extends Base{
  
    public function getbindinfo()
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
      
          $user_bind = Db::table('wx_mp_bind_info')
            ->alias("u") 
            ->join('organization o', 'u.org_id = o.id','LEFT')
            ->field('u.org_id,o.corpname,o.corp_code')
            ->where(['u.wx_uid' => $uid,'u.isbind' => 1])
            ->find();
          if(count($user_bind)>0){
             $bind_corp_code = $user_bind['corp_code'];
             return json([
                          'errcode'        => 0,
                          'is_bind'	     => 1,
                          'corp_code'	=> $bind_corp_code,
                      ]);
          }else{
            return json([
                          'errcode'        => 0,
                          'is_bind'	     => 0,
                          'corp_code'	=> '',
                      ]);
          }     
    }
  
    public function getmyinfo()
    {
           $uid =input('post.uid');
           $corpid =input('post.corpid');
           $token =input('post.token'); 
       
          if( empty($uid) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:uid'
           		]);
            }
          if( empty($corpid) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:corpid'
           		]);
            }
          if( empty($token) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:token'
           		]);
            }
      
           $dep_id =0;
         // 获取传递参数的企业信息
           $corp = Db::table('organization')->where(['corp_code' => $corpid ])->find();
           if(!empty($corp)){
           		$dep_id = $corp['id'];
                $dep_name = $corp['corpname'];
           }else{
                return json([
                            'errcode'        => 10006,
                            'msg'         =>"获取机构信息失败"
                    ]);
           }
           

          $bind_corp_id =0;
          $bind_corp_code =0;
          $user_bind = Db::table('wx_mp_bind_info')
            ->alias("u") //取一个别名
            ->join('organization o', 'u.org_id = o.id','LEFT')
            ->field('u.org_id,o.corpname,o.corp_code')
            ->where(['u.wx_uid' => $uid,'u.isbind' => 1])
            ->find();
          if(count($user_bind)>0){
             $bind_corp_id = $user_bind['org_id'];
             $bind_corp_code = $user_bind['corp_code'];
             $bind_corp_name = $user_bind['corpname'];
          }else{
             return json([
                  'errcode'        => 1005,
                  'msg'       => "获取用户绑定信息失败，未绑定任何机构!"
                ]);
          
          }
      
          
         if($dep_id != $bind_corp_id){
               return json([
                    'errcode'        => 1099,
                    'corp_code'  => $bind_corp_code,
                    'bind_corp_name'  => $bind_corp_name,
                    'cur_corp_name'  => $dep_name,
                    'msg'            => "您尚未绑定".$dep_name."\n请先从".$bind_corp_name."解绑后再绑定"
               ]);
             	
           }
          
          
          $user = Db::table('wx_mp_user')
            ->where(['wid' => $uid])
            ->find();
          if(count($user)>0){
                // 获取企业信息
            	$corp = Db::table('organization')->where(['corp_code' => $corpid ])->find();
                if(!empty($corp)){
                     return json([
                        'errcode'        => 0,
                        'userid'	     => $user['userid'],
                        'name'		     => $user['name'],
                        'corpname'	     => $corp['corpname'],
                        'phone_num'      => $user['phone_num'],
                        'type_corpname'  => $corp['type_corpname'],
                        'type_username'  => $corp['type_username']
                    ]);
                }else{
                    return json([
                            'errcode'        => 10006,
                            'msg'         =>"获取企业信息失败"
                    ]);
                }      
          }else{
          	return json([
                  'errcode'        => 1005,
                  'msg'       => "获取用户信息失败"
                ]);
          
          }  
      
     }
  
    
}
