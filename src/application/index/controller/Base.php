<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020 2020NCOV All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangqixun <zhangqx@ss.pku.edu.cn>
// +----------------------------------------------------------------------

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\index\service\Http;

class Base extends Controller
{

    public function _initialize()
    {
        if ((Request::instance()->module() == 'index' &&
                Request::instance()->controller() == 'Login' &&
                Request::instance()->action() == "getcode") ||
            (Request::instance()->module() == 'index' &&
                Request::instance()->controller() == 'Report' &&
                Request::instance()->action() == "get_district_path")) {
            return;
        } else {
            //检测token
            $paramCheckRes = Http::checkParams('post.uid', 'post.token');
            if (!is_array($paramCheckRes)) {
                return $paramCheckRes;
            }
            list($uid, $token) = $paramCheckRes;

            //记录用户访问日志
            Db::table('wx_mp_log')->insert([
                'uid' => $uid,
                'token' => $token,
                'url' => Request::instance()->module() . "/" . Request::instance()->controller() . "/" . Request::instance()->action(),
                'time' => date('Y-m-d H:i:s'),
            ]);

            if (strlen($token) < 20) {
                json(array("errcode" => 1107, "msg" => "token为空"))->send();
                exit();
            }
            $user = Db::table('wx_mp_user')->where(['wid' => $uid, 'token' => $token])->find();
            if (empty($user)) {
                json(array("errcode" => 1106, "msg" => "token过期，请尝试重新扫码激活并绑定个人信息"))->send();
                exit();
            }

        }
    }

}
