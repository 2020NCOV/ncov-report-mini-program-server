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

//该功能为测试功能用。
class Reportweb extends Controller
{

    //通过web方式传递的数据，参数为get
    public function index()
    {
        $paramCheckRes = Http::checkParams('param.uid', 'param:token', 'get.corp_code');
        if (!is_array($paramCheckRes)) {
            return $paramCheckRes;
        }
        list($uid, $token, $corp_code) = $paramCheckRes;
        echo $uid;
        echo $token;
        echo $corp_code;
        echo "999";
    }

}
