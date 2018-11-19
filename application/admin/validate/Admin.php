<?php
namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'userid'  =>  'require|alphaDash|max:16|min:4',
        'pwd' => 'require|alphaDash|max:16|min:4',
        'uname' => 'require|chsDash|max:16|min:1'
    ];

    protected $message  =   [
        'userid.require' => '账号不能为空',
        'userid.max' => '账号长度不能超过16个字符',
        'userid.min' => '账号长度不能低于4个字符',
        'userid.alphaDash' => '账号只能是数字、字母、下划线',
        'pwd.require' => '密码不能为空',
        'pwd.max' => '密码长度不能超过16个字符',
        'pwd.min' => '密码长度不能低于4个字符',
        'pwd.alphaDash' => '密码只能是数字、字母、下划线',
        'uname.require' => '用户名不能为空',
        'uname.max' => '用户名长度不能超过16个字符',
        'uname.min' => '用户名长度不能低于1个字符',
        'uname.alphaDash' => '用户名只能是汉字、字母、数字、下划线',
    ];
}