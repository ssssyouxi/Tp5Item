<?php
namespace app\admin\model;

use think\Model;
use \think\model\concern\SoftDelete; //引入软删除

class Archives extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
}