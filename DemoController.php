<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @title 测试demo
 * @group 移动端接口
 * @description 接口说明
 * @param  public  require|int|default:1|desc:当前类中公共参数
 */
class DemoController extends Controller
{
    /**
     * @title 测试demo接口
     * @description 接口说明
     * @author 开发者
     *
     * @header device require|int|default:1|desc:自增ID
     *
     * @param  id  require|int|default:1|desc:自增ID
     *
     * @return name:名称
     * @return mobile:手机号
     * @return list_messages:消息列表@
     * @list_messages message_id:消息ID content:消息内容
     * @return object:对象信息@!
     * @object attribute1:对象属性1 attribute2:对象属性2
     * @return array:数组值#
     * @return list_user:用户列表@
     * @list_user name:名称 mobile:手机号 list_follow:关注列表@
     * @list_follow user_id:用户id name:名称
     */
    public function index(Request $request)
    {
        //接口代码
        $device = $request->header('device');
        echo json_encode(["code"=>200, "message"=>"success", "data"=>['device'=>$device]]);
    }

    /**
     * @title 登录接口
     * @description 接口说明
     * @author 开发者
     * @module 用户模块

     * @param name require|int|default:1|desc:用户名
     * @param pass require|string|default:123|desc:密码
     *
     * @return name:名称
     * @return mobile:手机号
     *
     */
    public function login(Request $request)
    {
        //接口代码
        $device = $request->header('device');
        echo json_encode(["code"=>200, "message"=>"success", "data"=>['device'=>$device]]);
    }
}
