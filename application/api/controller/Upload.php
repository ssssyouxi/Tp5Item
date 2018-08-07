<?php
namespace app\api\controller;
use think\Controller;
use	think\Request;
use think\File;
use think\facade\App;
use think\facade\Env;

class Upload extends Controller{
	public function index(Request $request){
        $file = $this->request->file('file');
        if(!empty($file)){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->validate(['size'=>1048576,'ext'=>'jpg,png,gif'])->rule('uniqid')->move(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . 'uploads'.DIRECTORY_SEPARATOR.substr(date('Ymd',time()),-6));
            $error = $file->getError();
            if($info){
                // 成功上传后 获取上传信息
                $path = $info->getrealpath();
                return ['code'=>1,'msg'=>'成功','res'=>substr($path,strripos($path, 'public')+6)];
            }else{
                // 上传失败获取错误信息
                $file->getError();
                return ['code'=>0,'msg'=>'失败','res'=>$file->getError()];
            }
        }
    }
}