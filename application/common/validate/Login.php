<?php

namespace app\common\validate;

use think\Validate;

class Login extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
	protected $rule = [
        'mobile' => 'require|length:11',
        'password' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'mobile.require' => '手机号码不能为空',
        'mobile.length' => '手机号码不正确',
        'password.require' => '登录密码不能为空',
    ];
}
