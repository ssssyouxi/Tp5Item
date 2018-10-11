<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\admin\model\Addonarticle; //查表addonarticle
use \app\admin\model\Archives; //查表archives
use \app\admin\model\Admin; //查表admin
use \app\admin\model\Arctype;//查表arctype
use \app\admin\model\Arcatt; //查表Arcatt
#use think\facede\Env;

class Category extends Controller
{
    public function index(){
        $type = Arctype::view('Arctype','id,reid,topid,typename')->select()->toArray();
        $this->assign('type',$type);
        return $this->fetch();
    }

    //栏目-修改展示
    public function article(Request $request)
    {
        $res = Db::name("arctype")->field("id,reid,typename,channeltype")->where("id",input("get.id"))->find();
        $type = Db::name('arctype')->field('id,reid,typename')->select();
        $channel = Db::name('channeltype')->field("id,typename")->select();
        $list = $this->getTree($type);
        $wxj= '';
        foreach($list as $value){
            $select = $res['reid']==$value['id']? "selected":'';
            $wxj.= "<option value='".$value['id']."'".$select.">".str_repeat('——', $value['level']).$value['typename']."</option>";}
        $this->assign("channel",$channel);
        $this->assign("res",$res);
        $this->assign("wxj",html_entity_decode($wxj));
        return $this->fetch();
        
    }
    function getTree($array, $reid =0, $level = 0){

        //声明静态数组,避免递归调用时,多次声明导致数组覆盖
        static $list = [];
        foreach ($array as $key => $value){
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['reid'] == $reid){
                //父节点为根节点的节点,级别为0，也就是第一级
                $value['level'] = $level;
                //把数组放到list中
                $list[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($array[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->getTree($array, $value['id'], $level+1);

            }
        }
        return $list;
    }

    //栏目-修改
    public function changeCate(Request $request){
        if($request){
            if(input('post.typename')=="请选择"){
                $this->error("请选择要移动的分组");
            }
            $res = Db::name('arctype')->data(['typename'=>input('post.typename'),'reid'=>input('post.reid'),'channeltype'=>input('post.channeltype')])->where('id',input('post.id'))->update();
            if($res){
                $this->success("修改成功");
            }else{
                $this->error("请提交正确的数据");
            }
        }else{
            $this->error("您未提交任何数据");
        }
    }

    //栏目-显示添加
    public function showadd (){
        $type = Db::name('arctype')->field('id,reid,typename')->select();
        $channel = Db::name('channeltype')->field("id,typename")->select();
        $list = $this->getTree($type);
        $wxj= '';
        foreach($list as $value){
            $wxj.= "<option value='".$value['id']."'>".str_repeat('——', $value['level']).$value['typename']."</option>";}
        $this->assign("wxj",html_entity_decode($wxj));
        $this->assign("channel",$channel);
        return $this->fetch();
    }

    //栏目-增加
    public function addcate(Request $request){
        if($request){
            $data = ["typename"=>input('post.typename'),"reid"=>input('post.reid'),'channeltype'=>input('post.channeltype')];
            $res = Arctype::insert($data);
            if($res){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }
    }

    //栏目-删除（及文章软删除）
    public function catedel(Request $request){
        if($request){
            
            $res = Archives::where('typeid',input('post.id'))->update(["delete_time"=>time()]);
            // dump(2);
            // $channel = Arctype::where('id',input('post.id'))->field('channeltype')->find();
            // dump(3);
            // $db = Db::name('channeltype')->where('id',$channel['channeltype'])->field('addtable')->find();
            // dump(4);
            // $res = Db::table($db['addtable'])->where('typeid',input('post.id'))->update(["delete_time"=>time()]);
            
            $res1 = Arctype::where('id',input('post.id'))->delete();
            if(isset($res) && $res1){
                return ['code'=>1,'msg'=>'删除成功！'];
            }else{
                return ['code'=>0,'msg'=>'删除失败！'];
            }
        }else{
            $this->error("删除失败");
        }
    }
}