<?php
namespace app\admin\validate;

use think\Validate;

class Adminu extends Validate
{
    protected $rule = [
        
        'uname' => 'require|chsDash|max:16|min:1'
    ];

    protected $message  =   [
        'uname.require' => '用户名不能为空',
        'uname.max' => '用户名长度不能超过16个字符',
        'uname.min' => '用户名长度不能低于1个字符',
        'uname.alphaDash' => '用户名只能是汉字、字母、数字、下划线',
    ];
}