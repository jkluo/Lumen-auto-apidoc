# Lumen-auto-apidoc
Lumen auto  apidoc && testing just like swagger

### 使用方法
#### 1、安装扩展
```bash
composer  require jkluo/lumen-auto-apidoc
```

#### 2、注册服务提供者

```php
    添加（可区分evn） $app->register(Jkluo\LumenApiDoc\ApiDocServiceProvider::class);到bootstrap/app.php
```
#### 3、复制前端资源文件及配置文件
```bash
   1.复制插件目录下面的lumen-apidoc\assets里面的文件到(public|www)/apidoc文件下
   2.复制插件目录下面的lumen-apidoc\src\config\doc.php文件到config\doc.php,没有config目录自己创建一个
```
#### 4、在config/doc.php文件中，配置需要生成文档的接口类
```php
return [
    'title' => "APi接口文档",  //文档title
    'version'=>'1.0.0', //文档版本
    'copyright'=>'Powered By Jkluo', //版权信息
    'controller' => [
        //需要生成文档的类
	'App\\Http\\Controllers\\Api\\DemoController'//此控制器demo文件请看下一个步凑中的源码，或者在包根目录下面DemoController.php
    ],
    'filter_method' => [
        //过滤 不解析的方法名称
        '_empty'
    ],
    'return_format' => [
        //数据格式
        'status' => "200/300/301/302",
        'message' => "提示信息",
    ],
    'public_header' => [
        //全局公共头部参数
        //如：['name'=>'version', 'require'=>1, 'default'=>'', 'desc'=>'版本号(全局)']
    ],
    'public_param' => [
        //全局公共请求参数，设置了所以的接口会自动增加次参数
        //如：['name'=>'token', 'type'=>'string', 'require'=>1, 'default'=>'', 'other'=>'' ,'desc'=>'验证（全局）')']
    ],
];
```
#### 5、在相关接口类中增加注释参数( group 参数将接口分组，可选。param 参数格式为 Validate)
方法如下：返回参数支持数组及多维数组，Validate 规则详见 https://laravel.com/docs/8.x/validation

##### 1、重写父类validate方法
```php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Jkluo\LumenApiDoc\DocParserForValidate;

class Controller extends BaseController
{

    /**
     * 重写父类validate方法
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array|void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(Request $request, array $rules = [], array $messages = [], array $customAttributes = [])
    {
        if (empty($rules)){
            $rules = $this->getRulesArr($request);
        }
        if (empty($rules)){
            return;
        }
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        return $this->extractInputFromRules($request, $rules);
    }

    /**
     * 获取注解校验规则。
     * @param $request
     * @return array|void
     * @throws \ReflectionException
     */
    public function getRulesArr($request){
        if (empty($request->route()[1]['uses'])){
            return;
        };
        list($class,$action) = explode("@",$request->route()[1]['uses']);
        $reflection = new \ReflectionClass($class);
        if($reflection->hasMethod($action)) {
            $method = $reflection->getMethod($action);
            $doc = new DocParserForValidate();
            $action_doc = $doc->parse($method->getDocComment());
            return $action_doc['validate'];
        }
        return;
    }
}
```

##### 2、类和方法添加注解

```php
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
```
#### 6、在浏览器访问http://你的域名/doc 查看接口文档

#### 7、预览
![](https://static.oschina.net/uploads/img/201704/17101409_tAgD.png)
![](https://static.oschina.net/uploads/img/201704/17101348_XuUz.png)
![](https://static.oschina.net/uploads/img/201704/17101306_KePe.png)
