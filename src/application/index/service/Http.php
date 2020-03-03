<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020 2020NCOV All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangqixun <zhangqx@ss.pku.edu.cn>
// +----------------------------------------------------------------------

namespace app\index\service;

use think\Request;
use think\Db;
use think\Session;

class Http 
{

   	public static function get_request($url)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, false); //返回response头部信息
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }

    /**
     * 用于进行请求的参数检查，输入为一系列参数，格式为 'post:xxx' 或 'get:xxx'
     * 统一使用 input 进行检查，错误时返回 1003 错误信息，某则返回参数数组，可用 is_array 判断是否有错
     */
    public static function checkParams(...$paramStr)
    {
        $res = [];
        foreach ($paramStr as $p) {
            $param = input($p);
            if (empty($param)) {
                return json([
                    'errcode'   => 1003,
                    'msg'       => '参数错误:' . $p
                ]);
            }
            array_push($res, $param);
        }
        return $res;
    }

}
