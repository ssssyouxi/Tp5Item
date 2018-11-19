<?php
namespace app\admin\validate;

use think\Validate;

class Adminp extends Validate
{
    protected $rule = [

        'pwd' => 'require|alphaDash|max:16|min:4',

    ];

    protected $message  =   [
        'pwd.require' => '密码不能为空',
        'pwd.max' => '密码长度不能超过16个字符',
        'pwd.min' => '密码长度不能低于4个字符',
        'pwd.alphaDash' => '密码只能是数字、字母、下划线',
    ];
}