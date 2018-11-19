<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use \think\Request;
use think\facade\Config;
use \app\admin\model\Admin as AdminModel; //查表admin
use think\model\concern\SoftDelete;
#use think\facede\Env;

class Admin extends Controller
{
    public function index()
    {
        if(input("get.kw")){
            $keyword = input("get.kw");
            $AdminList = AdminModel::alias('a')
                                    ->join(Config::get('database.prefix')."admintype s",'a.usertype=s.rank')
                                    ->where('userid','like',"%$keyword%")
                                    ->paginate(10,false,['query'=>['kw'=>$keyword]]);
        }else{
            $AdminList = AdminModel::alias('a')
                                ->join(Config::get('database.prefix')."admintype s",'a.usertype=s.rank')
                                ->paginate(10);
        }
        $keyword = isset($keyword) ? $keyword:'';
        $this->assign("kw",$keyword);
        $count = $AdminList->total();
        $this->assign("total",$count);
        $this->assign("adminlist",$AdminList);

        return $this->fetch();
    }
    //添加一个用户
    public function add(Request $request){
        if(session('usertype')<10){
            return $this->error("权限不足");
        }
        if(input('post.')){
            $pwd= input("post.pwd");
            $data = input("post.");
            
            $validate = new \app\admin\validate\Admin;
            if(!$validate->check(input('post.'))){
                return $this->error($validate->getError());
            }else{
                $data['pwd']=substr(md5(input("post.pwd")),5,20);
                $res = Db::name('member')->strict(false)->insertGetId($data);
                if($res){
                    $data['id'] = $res;
                    $re = AdminModel::strict(false)->insert($data);
                    if($re){
                        return $this->success("添加成功");
                    }else{
                        return $this->error("添加到主表时失败");
                    }
                }else{
                    return $this->error("添加到副表时失败");
                }

            }
            
            
        }else{
            
            $res = Db::name("admintype")->select();

            $this->assign("purviews",$res);
            
            return $this->fetch();
        }
    }


    //删除一个用户
    public function del($id){
        if(session('usertype')<10){
            return $this->error("权限不足");
        }
        $id = input('id');
        
        $res= AdminModel::destroy($id);
        // where('id',$id)
        //     ->update(['delete_time'=>time()]);
        
        
        
        // dump(Archives::getLastSql());
        if($res){
            return ['code'=>1,'msg'=>'删除成功！','id'=>$id];
        }else{
            return ['code'=>0,'msg'=>'删除失败！'];
        }
    }

    //展示、修改一个用户
    public function article(){
        if(session('usertype')<10){
            return $this->error("权限不足");
        }
        if(input("post.id")){
            $data = input("post.");
            $pwd = $data['pwd'];
            unset($data['pwd']);
            $validate = new \app\admin\validate\Adminu;
            
            if(!$validate->check($data)){
                return $this->error($validate->getError());
            }
            
            if($pwd!=""){
                $validate = new \app\admin\validate\Adminp;
                if(!$validate->check(["pwd"=>$pwd])){
                    return $this->error($validate->getError());
                }else{
                    $data['pwd'] = substr(md5(trim($pwd)),5,20);
                }
            }
            $res = AdminModel::where("id",$data['id'])
                                    ->strict(false)
                                    ->update($data);
                    if($res){
                        return $this->success("修改成功！");
                    }else{
                        return $this->error("修改失败！未做任何改动/找不到对应账号");
                    }
        }else{

    
        
        $id = input("id");
        $res = AdminModel::where('id',$id)->find();
        $type = Db::name("admintype")->select();
        $this->assign("purviews",$type);
        $this->assign("adminuser",$res);
        return $this->fetch();}
    }
    public function _empty(){
        header("HTTP/1.0 404 Not Found");
        $this -> display("admin:404");
    }
}
