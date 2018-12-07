<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\index\controller\Base;
class Service extends Base
{
    public function service_index(){
        $this->casetype(); 
        $this->newstype(); 
        return $this->menu(); 
        return $this->fetch();   
    }

}

