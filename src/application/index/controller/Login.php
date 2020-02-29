<?php
namespace app\index\controller;
use think\Request;
use think\Db;
use think\Session;
use think\Controller;
use \think\Config;

class Login extends Base{
  
    public function index()
    {
        return  ;
    
    }
  
  	 /**
      * 该函数在小程序端应用启动时，获取code。换取openid，以确定用户身份。每个微信在一个应用内openid是唯一的
      * 并将openid存入wx_mp_user，并生成用户uid，每个微信一个，可以绑定不同的机构
      */
    public function getcode()
    {
            if( empty(Request::instance()->post('code'))){
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:code'
           		]);
            }

      
            $code = Request::instance()->post('code');
            $type = Request::instance()->post('type');
   
      
            //根据code 获取openid和session_key
            $appid  = Config::get('wechat_appid');
            $secret = Config::get('wechat_secret');
          
            //type调试时启用，可以接入不同的小程序进行测试，需要在配置文件中配置相应的$appid和$secret
            if($type ==1){
                $appid  = Config::get('wechat_appid1');
            	$secret = Config::get('wechat_secret1');
            }
        
            if($type ==2){
                $appid  = Config::get('wechat_appid2');
            	$secret = Config::get('wechat_secret2');
            }
            
      		
            $curl   =  "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code";
            //echo $curl;
            $HttpService = new \app\index\service\Http();
            $res =$HttpService->get_request($curl);
            $res    = json_decode($res,true);
           
            
            if(isset($res['openid']) )
            {
               $openid    =$res['openid'];
               $user = Db::table('wx_mp_user')->where(['openid' => $openid ])->find();
               
               $time_out = strtotime("+1 days");
               $ifelse ='';
               //用户信息记录wx_mp_user表中
               //将token存储。目前没有考虑token，之后会考虑token过期及重发.token中需要含有用户的基本信息如uid、过期时间等
               $token = $this->makeToken();
               if(!empty($user)){
                    $token = $user['token'];
                    $wid = $user['wid'];
                }else{
                    $wid =Db::table('wx_mp_user')->insert([
                        'openid'      => $res['openid'],
                        'token' => $token,
                        'time_out'    => $time_out,
                        'login_time'  => date('Y-m-d H:i:s'),
                    ]);
                    $user2 = Db::table('wx_mp_user')->where(['openid' => $openid ])->find();
                    if(!empty($user2)){
                        $wid = $user2['wid'];
                    }else{
                       return json([
                          'errcode'        => 1008,
                          'msg'       => "获取请求失败，请退出重试"
                        ]);
                    }
                    $ifelse ='else'; 
                }
                return json([
                    'errcode'        => 0,
                    'token'        => $token,
                    'uid'		   => $wid, 
                    'login_status' => 1,
                    'login_time'   => date('Y-m-d H:i:s')
                ]);
            }else{
            	return json([
                  'errcode'        => 1001,
                  'msg'       => "获取openid失败"
                ]);
            }
     }
  
  /**
      * 该函数用于通过corpcode，获取机构的详细信息
      * 在organization表中查询
      */
  	public function getcorpname()
    {
      	$uid =input('post.uid');
        $token =input('post.token'); 
        $corpid =input('post.corpid');

           if( empty($form_template) )
            {
                $template_code="company";
            }
       
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
            if( empty($corpid) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:corpid'
           		]);
            }
      
            // 获取企业信息
            $corp = Db::table('organization')->where(['corp_code' => $corpid ])->find();
          
            if(!empty($corp)){
                return json([
                        'errcode'        => 0,
                        'corpid'         =>$corpid,
                        'corpname'       =>$corp['corpname'],
                        'template_code'  =>$corp['template_code'],
                        'type_corpname'  =>$corp['type_corpname'],
                        'type_username'  =>$corp['type_username']
                ]);
            }else{
                return json([
                        'errcode'        => 10006,
                        'msg'         =>"获取企业信息失败"
                ]);
            }     
     }
  
    public function check_is_registered()
    {
      	
      
           $uid =input('post.uid');
           $token =input('post.token'); 
           $corpid =input('post.corpid');
           

       
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
            if( empty($corpid) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:corpid'
           		]);
            }
           
      
           //此处需要到用户表里面检查是否有这个企业编号和这个用户名
      

      
            $corp = Db::table('organization')->where(['corp_code' => $corpid ])->find();
                if(!empty($corp)){
                	$orgid = $corp['id'];
                }else{
                    return json([
                            'errcode'        => 10006,
                            'msg'         =>"获取企业信息失败"
                    ]);
           }
            
           $corp_bind = Db::table('wx_mp_bind_info')->where(['org_id' => $orgid,'wx_uid'=> $uid ,'isbind'=> 1 ])->find();
           if(!empty($corp_bind) ){
               return json([
                    'errcode'        => 0,
                    'is_registered'        => 1
               ]);
             	
           }else{
               return json([
                        'errcode'        => 0,
                        'is_registered'        => 0
               ]);
           }
           
     }
  
  /**
      * 该函数用于检测用户标识，是否已经被绑定
      * 如果没绑定，可以进行注册(绑定)身份
      */
    
    public function check_user()
    {
           $uid =input('post.uid');
           $token =input('post.token'); 
           $corpid =input('post.corpid');
           $userid =input('post.userid');
       
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
            if( empty($corpid) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:corpid'
           		]);
            }
            if( empty($userid) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:userid'
           		]);
            }
      
           
         
           $corp = Db::table('organization')->where(['corp_code' => $corpid ])->find();
           if(!empty($corp)){
                	$corpid = $corp['id'];
           }else{
               return json([
                            'errcode'        => 10006,
                            'msg'         =>"获取企业信息失败"
                 ]);
            }
              
      	   //此处需要到用户表里面检查是否有这个企业编号和这个用户名
           $corp_bind = Db::table('wx_mp_bind_info')->where(['org_id' => $corpid,'username'=> $userid ,'isbind'=> 1 ])->find();
           if(!empty($corp_bind) ){
             	if(0 == $corp_bind['isbind']){
                      return json([
                          'errcode'        => 0,
                          'corpid'         =>$corpid,
                          'userid'         =>$userid,
                          'is_exist'        => 0
                      ]);
                }else{
                	 return json([
                          'errcode'        => 100020,
                          'msg'            =>"该用户已被其他微信绑定，每个用户只能被一个微信绑定"
                      ]);
                }
           }
      
           return json([
                    'errcode'        => 0,
                    'corpid'         =>$corpid,
                    'userid'         =>$userid,
                    'is_exist'        => 0
           ]);
           
     }
   
     /**
      * 该函数用于用户信息绑定，并将信息注册到相应的机构表中
      * 
      */
    
     public function register()
    {
      	
           $uid =input('post.uid');
           $token =input('post.token'); 
           $org_id =input('post.corpid');
           $userid =input('post.userid');
           $name =input('post.name');
           $phone_num =input('post.phone_num');

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
            if( empty($org_id) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:corpid'
           		]);
            }
            if( empty($userid) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:userid'
           		]);
            }
            if( empty($name) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:name'
           		]);
            }
            if( empty($phone_num) )
            {
                return json([
                    'errcode'        => 1003,
                    'msg'        => '参数错误:phone_num'
           		]);
            }
       
       
            //检查是否已经注册
            $corp_bind = Db::table('wx_mp_bind_info')->where(['wx_uid' => $uid,'org_id' => $org_id,'username'=> $userid,'isbind' => 1])->find();
       
            //如果在这个企业绑定了，返回已绑定
            if(!empty($corp_bind) ){
                 return json([
                    'errcode'        => 0,
                    'is_registered'  =>1
           		]);
            }else{
                 //此处检查一个微信，只能绑定一个企业
                 $corp_bind = Db::table('wx_mp_bind_info')->where(['wx_uid' => $uid,'username'=> $userid ,'isbind'=> 1 ])->find();
                 if(!empty($corp_bind) ){
                      return json([
                                'errcode'        => 100020,
                                'msg'            =>"本微信已经绑定其他机构，不能重复绑定"
                      ]);
                 
                 }
                 Db::table('wx_mp_bind_info')->insert([
                        'wx_uid' => $uid,
                        'org_id' => $org_id,
                        'username' => $userid,
                        'isbind' => 1,
                    ]);
                 Db::table('wx_mp_user')->where(['wid' => $uid])->update([
                        'userid' => $userid,
                        'name' => $name,
                        'phone_num' => $phone_num,
                    ]);
                return json([
                    'errcode'        => 0,
                    'is_registered'  =>1
           		]);
                 
            }
           
     }
  
     
 	public function unbind()
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
           
            $corp_bind = Db::table('wx_mp_bind_info')->where(['wx_uid' => $uid])->find();
            if(!empty($corp_bind) ){
               Db::table('wx_mp_bind_info')->where(['wx_uid' => $uid])->update([
                        'isbind' => 0,
                        'unbind_date' => date('Y-m-d H:i:s'),
                    ]);
                 return json([
                    'errcode'        => 0,
                    'is_registered'  =>0
           		]);
            }else{
                 return json([
                          'errcode'        => 0,
                          'is_registered'  => 0
                 ]);
            }
     }
  
  
  	private function makeToken()
    {
        $str = md5(uniqid(md5(microtime(true)), true)); //生成一个不会重复的字符串
        $str = sha1($str); //加密
        return $str;
    }

}
