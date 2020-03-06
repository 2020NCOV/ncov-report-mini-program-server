<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020 2020NCOV All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangqixun <zhangqx@ss.pku.edu.cn>
// +----------------------------------------------------------------------

namespace app\index\controller;

use think\Request;
use think\Db;
use think\Session;
use think\Controller;
use \think\Config;
use app\index\service\Http;

class Login extends Base
{

    public function index()
    {
        return;
    }

    /**
     * 该函数在小程序端应用启动时，获取code。换取openid，以确定用户身份。每个微信在一个应用内openid是唯一的
     * 并将openid存入wx_mp_user，并生成用户uid，每个微信一个，可以绑定不同的机构
     */
    public function getcode()
    {
        $paramCheckRes = Http::checkParams('post.code');
        if (!is_array($paramCheckRes)) {
            return $paramCheckRes;
        }
        list($code) = $paramCheckRes;

        $type = input('post.type');

        //根据code 获取openid和session_key
        $appid = Config::get('wechat_appid');
        $secret = Config::get('wechat_secret');

        //type调试时启用，可以接入不同的小程序进行测试，需要在配置文件中配置相应的$appid和$secret
        if ($type == 1) {
            $appid = Config::get('wechat_appid1');
            $secret = Config::get('wechat_secret1');
        }

        if ($type == 2) {
            $appid = Config::get('wechat_appid2');
            $secret = Config::get('wechat_secret2');
        }

        if ($type == 3) {
            $appid = Config::get('wechat_appid3');
            $secret = Config::get('wechat_secret3');
        }

        if ($type == 4) {
            $appid = Config::get('wechat_appid4');
            $secret = Config::get('wechat_secret4');
        }

        $curl = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $secret . "&js_code=" . $code . "&grant_type=authorization_code";
        //echo $curl;
        $HttpService = new \app\index\service\Http();
        $res = $HttpService->get_request($curl);
        $res = json_decode($res, true);
        //var_dump($res);

        if (isset($res['openid'])) {
            $openid = $res['openid'];
            $user = Db::table('wx_mp_user')->where(['openid' => $openid])->find();
            $token = $this->makeToken();
            $time_out = strtotime("+1 days");
            $ifelse = '';
            //用户信息入库
            if (!empty($user)) {
                $token = $user['token'];
                $wid = $user['wid'];
                $ifelse = 'if';
            } else {
                //echo "else";
                //echo $res['openid'];
                $wid = Db::table('wx_mp_user')->insert([
                    'openid' => $res['openid'],
                    'token' => $token,
                    'time_out' => $time_out,
                    'login_time' => date('Y-m-d H:i:s'),
                ]);
                $user2 = Db::table('wx_mp_user')->where(['openid' => $openid])->find();
                if (!empty($user2)) {
                    $wid = $user2['wid'];
                } else {
                    return json([
                        'errcode' => 1008,
                        'msg' => "获取请求失败，请退出重试"
                    ]);

                }

                //var_dump($wid);
                $ifelse = 'else';
            }


            return json([
                'errcode' => 0,
                'token' => $token,
                'uid' => $wid,
                'login_status' => 1,
                'login_time' => date('Y-m-d H:i:s')
            ]);
        } else {

            return json([
                'errcode' => 1001,
                'msg' => "获取openid失败"
            ]);
        }
    }

    /**
     * 该函数用于通过corpcode，获取机构的详细信息
     * 在organization表中查询
     */
    public function getcorpname()
    {
        if (empty($form_template)) {
            $template_code = "company";
        }

        $paramCheckRes = Http::checkParams('post.uid', 'post.token', 'post.corpid');
        if (!is_array($paramCheckRes)) {
            return $paramCheckRes;
        }
        list($uid, $token, $corpid) = $paramCheckRes;

        // 获取企业信息
        $corp = Db::table('organization')->where(['corp_code' => $corpid])->find();

        if (!empty($corp)) {
            return json([
                'errcode' => 0,
                'corpid' => $corpid,
                'corpname' => $corp['corpname'],
                'template_code' => $corp['template_code'],
                'type_corpname' => $corp['type_corpname'],
                'type_username' => $corp['type_username'],
                'depid' => $corp['id']

            ]);
        } else {
            return json([
                'errcode' => 10006,
                'msg' => "获取企业信息失败"
            ]);
        }
    }

    public function check_is_registered()
    {
        $paramCheckRes = Http::checkParams('post.uid', 'post.token', 'post.corpid');
        if (!is_array($paramCheckRes)) {
            return $paramCheckRes;
        }
        list($uid, $token, $corpid) = $paramCheckRes;

        //此处需要到用户表里面检查是否有这个企业编号和这个用户名
        $corp = Db::table('organization')->where(['corp_code' => $corpid])->find();
        if (!empty($corp)) {
            $depid = $corp['id'];
        } else {
            return json([
                'errcode' => 10006,
                'msg' => "获取企业信息失败"
            ]);
        }

        $corp_bind = Db::table('wx_mp_bind_info')->where(['org_id' => $depid, 'wx_uid' => $uid, 'isbind' => 1])->find();
        if (!empty($corp_bind)) {
            return json([
                'errcode' => 0,
                'is_registered' => 1
            ]);

        } else {
            return json([
                'errcode' => 0,
                'is_registered' => 0
            ]);
        }
    }

    /**
     * 该函数用于检测用户标识，是否已经被绑定
     * 如果没绑定，可以进行注册(绑定)身份
     */
    public function check_user()
    {
        $paramCheckRes = Http::checkParams('post.uid', 'post.token', 'post.corpid', 'post.userid');
        if (!is_array($paramCheckRes)) {
            return $paramCheckRes;
        }
        list($uid, $token, $corpid, $userid) = $paramCheckRes;

        $corp = Db::table('organization')->where(['corp_code' => $corpid])->find();
        if (!empty($corp)) {
            $depid = $corp['id'];
        } else {
            return json([
                'errcode' => 10006,
                'msg' => "获取企业信息失败"
            ]);
        }

        //此处需要到用户表里面检查是否有这个企业编号和这个用户名
        $corp_bind = Db::table('wx_mp_bind_info')->where(['org_id' => $depid, 'username' => $userid, 'isbind' => 1])->find();
        if (!empty($corp_bind)) {
            //echo "999";
            if ($uid == $corp_bind['wx_uid']) {
                return json([
                    'errcode' => 0,
                    'corpid' => $corpid,
                    'userid' => $userid,
                    'is_exist' => 0
                ]);
            } else {
                return json([
                    'errcode' => 100020,
                    'msg' => "该用户已被其他微信绑定，每个用户只能被一个微信绑定"
                ]);
            }
        }

        return json([
            'errcode' => 0,
            'corpid' => $corpid,
            'userid' => $userid,
            'is_exist' => 0
        ]);
    }

    /**
     * 该函数用于用户信息绑定，并将信息注册到相应的机构表中
     *
     */
    public function register()
    {
        $paramCheckRes = Http::checkParams('post.uid', 'post.token', 'post.corpid', 'post.userid', 'post.name', 'post.phone_num');
        if (!is_array($paramCheckRes)) {
            return $paramCheckRes;
        }
        list($uid, $token, $corpid, $userid, $name, $phone_num) = $paramCheckRes;

        // 获取企业信息
        $corp = Db::table('organization')->where(['corp_code' => $corpid])->find();
        $depid = 0;
        if (!empty($corp)) {
            $depid = $corp['id'];
        } else {
            return json([
                'errcode' => 10006,
                'msg' => "获取企业信息失败"
            ]);
        }

        //检查是否已经注册
        $corp_bind = Db::table('wx_mp_bind_info')->where(['wx_uid' => $uid, 'org_id' => $depid, 'username' => $userid, 'isbind' => 1])->find();

        //如果在这个企业绑定了，返回已绑定
        if (!empty($corp_bind)) {
            return json([
                'errcode' => 0,
                'is_registered' => 1
            ]);
        } else {
            //此处检查一个微信，只能绑定一个企业
            // $corp_bind = Db::table('wx_mp_bind_info')->where(['wx_uid' => $uid,'username'=> $userid ,'isbind'=> 1 ])->find();
            /* if(!empty($corp_bind) ){
                  return json([
                            'errcode'        => 100020,
                            'msg'            =>"该用户已被其他微信绑定，一个用户只能绑定一个微信"
                  ]);

             }
             */
            Db::table('wx_mp_bind_info')->where(['wx_uid' => $uid])->update([
                'isbind' => 0
            ]);


            Db::table('wx_mp_bind_info')->insert([
                'wx_uid' => $uid,
                'org_id' => $depid,
                'username' => $userid,
                'isbind' => 1,
            ]);
            Db::table('wx_mp_user')->where(['wid' => $uid])->update([
                'userid' => $userid,
                'name' => $name,
                'phone_num' => $phone_num,
            ]);
            return json([
                'errcode' => 0,
                'is_registered' => 1
            ]);

        }

    }

    public function unbind()
    {
        $paramCheckRes = Http::checkParams('post.uid', 'post.token');
        if (!is_array($paramCheckRes)) {
            return $paramCheckRes;
        }
        list($uid, $token) = $paramCheckRes;

        //先解除er_wx_department_bind_user那个表中的绑定
        $corp_bind = Db::table('wx_mp_bind_info')->where(['wx_uid' => $uid])->find();
        if (!empty($corp_bind)) {
            Db::table('wx_mp_bind_info')->where(['wx_uid' => $uid])->update([
                'isbind' => 0,
                'unbind_date' => date('Y-m-d H:i:s'),
            ]);


            return json([
                'errcode' => 0,
                'is_registered' => 0
            ]);
            //以上是仅仅重新更新，防止er_user_student_weixin表中的字段数据不一致导致的返回值异常的问题
        } else {


            return json([
                'errcode' => 0,
                'is_registered' => 0
            ]);

        }

    }

    //此函数需要优化，需要再token中携带用户信息，以减少参数传递
    private function makeToken()
    {
        $str = md5(uniqid(md5(microtime(true)), true)); //生成一个不会重复的字符串
        $str = sha1($str); //加密
        return $str;
    }

}
