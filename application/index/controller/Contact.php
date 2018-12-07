<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\index\controller\Base;
class Contact extends Base
{
     public function contact_index(){
        $this->casetype(); 
        $this->newstype(); 
        return $this->menu(); 
        return $this->fetch();                       
    }





}

