<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\index\controller\Base;
class About extends Base
{
    public function about_index(){
        $this->casetype(); 
        $this->newstype(); 
        return $this->menu(); 
        return $this->fetch();                       
    }
    public function about_technology(){
        $this->casetype(); 
        $this->newstype(); 
        return $this->menu(); 
        return $this->fetch();                       
    }
    public function about_honor(){
        $this->casetype(); 
        $this->newstype(); 
        return $this->menu(); 
        return $this->fetch();                       
    }
    public function about_agent(){
        $this->casetype(); 
        $this->newstype(); 
        return $this->menu(); 
        return $this->fetch();                       
    }

}

