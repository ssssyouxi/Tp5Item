<?php
namespace app\admin\validate;

use think\Validate;

class Model extends Validate
{
    protected $rule = [
        'field'  =>  'require|max:25|alphaDash',
        'itemname' => 'require|chsDash|max:25'
    ];

    protected $message  =   [
        'field.require' => '数据字段名不能为空',
        'field.max' => '数据字段名长度不能超过25个字符',
        'field.alphaDash' => '数据字段名只能是数字、字母、下划线及破折号',
        'itemname.require' => '数据字段名不能为空',
        'itemname.max' => '数据字段名长度不能超过25个字符',
        'itemname.chsDash' => '数据字段名只能是汉字、数字、字母、下划线及破折号'
    ];
}