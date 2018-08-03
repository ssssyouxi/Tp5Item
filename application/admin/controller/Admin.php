<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\admin\model\Admin as AdminModel; //查表admin
#use think\facede\Env;

class Admin extends Controller
{
    public function index()
    {
        $AdminList = AdminModel::select();
        $this->assign("adminlist",$AdminList);
        
        return $this->fetch();
    }

    
}
