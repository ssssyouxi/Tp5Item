<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use \think\Request;
use \app\admin\model\Archives; //查表archives
use \app\admin\model\Arctype;//查表arctype
use \app\admin\model\Arcatt; //查表Arcatt
#use think\facede\Env;
use think\facade\Env;

class Spec extends Base
{
    //专题-列表页
    public function index()
    {
        $res = Db::name('Archives')
                    ->alias('a')
                    ->field('a.title,a.click,a.writer,a.senddate')
                    ->join('sh_arctype s','a.typeid=s.id')
                    ->field('s.typename')
                    ->join('sh_addonspec d','a.id=d.aid')
                    ->field('templet,d.aid')
                    ->where('a.channel',"-1")
                    ->where('a.arcrank','neq','-2')
                    ->where(['delete_time'=>null])
                    ->order("senddate","desc")
                    ->paginate(10);
        $count = $res->total();
        
        $this->assign("spec",$res);
        $this->assign("count",$count);
        return $this->fetch();
    }

    //专题-详细页
    public function article (Request $request){
        $res = Archives::where('id',input('get.id'))
                        ->alias('a')
                        ->field('*')
                        ->join("sh_addonspec s","s.aid=a.id")
                        ->field("templet,s.aid")
                        ->find();
        $type = Arctype::view('Arctype','id,reid,topid,typename,channeltype')->select()->toArray();
        $list = $this->getTree($type);
        
        $wxj= '';
        foreach($list as $value){
                $select = $res['typeid'] == $value['id']? 'selected': '';
                $wxj.= "<option value='".$value['id']."'".$select.">".str_repeat('—', $value['level']).$value['typename']."</option>";
            
        }
        $this->assign("wxj",$wxj);
        $arcatt = Arcatt::select();
        $this->assign("spec",$res);
        $this->assign("arcatt",$arcatt);
        return $this->fetch();

    }

     //专题的软删除
     public function delSpec($id){
        $id = json_decode($id);
        $res1 = Archives::where(['id'=>$id])
        ->update(['arcrank'=>'-2']);
        $res= Archives::destroy($id);
        if($res!==false && $res1 !==false){
            return ['code'=>1,'msg'=>'删除成功！','id'=>$id];
        }else{
            return ['code'=>0,'msg'=>'删除失败！'];
        }
    }

    //修改专题详细内容
    public function updatespec(Request $request){
        if($request ==''){
            return "未接收到任何数据！";
            exit();
        }
        $data = input('post.');
        $imglist =json_decode(input('post.imglist'));
        foreach($imglist as $key => $value){
            if(strripos($value,'temp')!==false){
                $path = str_replace('temp','uploads',$value);
                $old =Env::get('root_path') . 'public' . $value;
                
                $old = str_replace(["\\","/"],DIRECTORY_SEPARATOR,$old);
                
                $new = Env::get('root_path') . 'public' . $path;
                $new = str_replace(["\\","/"],DIRECTORY_SEPARATOR,$new);
                $newdir=dirname($new);
                if(!is_dir($newdir)){
                    mkdir($newdir,0777,true);
                };
                if(file_exists($old)){               
                    if(!rename($old,$new)){
                        return ['code'=>0,'msg'=>'图片移动出错，修改失败'];
                    }
                }else{
                    return ['code'=>0,'msg'=>'图片不存在，修改失败'];
                }
            }
            $data[$key] = $path;
        }
        $flag = input('post.flag');
        $flag1 = isset($flag) ? implode(",",$flag) : '';
        
        $data['flag'] = $flag1;
        $data['pubdate'] = time();
        $res = Archives::where('id',input('post.id'))
                        ->strict(false)
                        ->update($data);
        $res1 = Db::name('addonspec')->where('aid',input('post.id'))
                            ->strict(false)
                            ->data(input("post."))
                            ->update();
        $res2 = Db::name('arctiny')->where('id',input('post.id'))
                        ->strict(false)
                        ->update($data);

        if($res !== false && $res1 !== false && $res2 !== false){
            return  ['code'=>1,'msg'=>'修改成功'];
            exit();
        }else{
            return  ['code'=>0,'msg'=>'修改失败'];
        }
    }

    //增加专题-展示页
    public function add(){
        $arcatt = Arcatt::select();
        $type = Arctype::view('Arctype','id,reid,topid,typename,channeltype')->select()->toArray();
        $list = $this->getTree($type);
        
        $wxj= '';
        foreach($list as $value){
                $wxj.= "<option value='".$value['id']."'>".str_repeat('—', $value['level']).$value['typename']."</option>";
            
        }
        $this->assign("arcatt",$arcatt);
        $this->assign("wxj",$wxj);
        return $this->fetch();
    }

    //增加专题-接收
    public function addspec(Request $request){
        if($request ==''){
            $this->error("未接收到任何数据！");
            exit();
        }
        if(input('post.flag')){
            $flag = implode(",",input('post.flag'));
        }else{
            $flag = '';
        }
        $dat = input("post.");
        $dat['flag'] = $flag;
        $dat['senddate'] = time();
        $dat['pubdate'] = time();
        $dat['sortrank'] = time();
        $dat['channel']="-1";
        $imglist =json_decode(input('post.imglist'));
        foreach($imglist as $key => $value){
            if(strripos($value,'temp')!==false){
                $path = str_replace('temp','uploads',$value);
                $old =Env::get('root_path') . 'public' . $value;
                
                $old = str_replace(["\\","/"],DIRECTORY_SEPARATOR,$old);
                
                $new = Env::get('root_path') . 'public' . $path;
                $new = str_replace(["\\","/"],DIRECTORY_SEPARATOR,$new);
                $newdir=dirname($new);
                if(!is_dir($newdir)){
                    mkdir($newdir,0777,true);
                };
                if(file_exists($old)){               
                    if(!rename($old,$new)){
                        return ['code'=>0,'msg'=>'图片移动出错，修改失败'];
                    }
                }else{
                    return ['code'=>0,'msg'=>'图片不存在，修改失败'];
                }
                $dat[$key]=$path;
            }
            
        }
        $res = Db::name("arctiny")->strict(false)->insertGetId($dat);
        
        $dat['id']=$res;
        $dat['aid']=$res;
        $res2 = Archives::strict(false)->insert($dat);
        
        $res1 = Db::name("addonspec")
                    ->strict(false)
                    ->insert($dat);
              
        if($res!==false && $res1!==false && $res2!==false){
            return  ['code'=>1,'msg'=>'增加成功'];
        }else{
            return  ['code'=>0,'msg'=>'增加失败'];
        }
    }
}
