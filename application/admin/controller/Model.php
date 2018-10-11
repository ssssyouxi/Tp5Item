<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Request;
use Phinx\Migration\AbstractMigration;

class Model extends Controller
{
    //模型-列表
    public function index()
    {
        $res = Db::name("channeltype")->field("id,nid,typename,addtable")->where('isshow',1)->order('id','desc')->paginate(15);

        $this->assign("channeltype",$res);
        $count = $res->total();
        
        
        $this->assign("count",$count);
        return $this->fetch();
    }

    //模型-详细
    public function article(){
        $res = Db::name("channeltype")->field("id,nid,typename,addtable,fieldset")
                                      ->where('id',input('get.id'))
                                      ->find();
        $arr = explode("\n",$res['fieldset']);
        dump($arr);
        $res_arr = [];
        $list=$this->type();
        foreach ($arr as $key => $value) {
            preg_match('/<field:(\w+)\s.*itemname="(\S+)"\s.*type="(\S+)"/i',$value,$r);
            // dump($r);
            if($r){
                $res_arr[] = [
                    'field'=>$r[1],
                    'itemname'=>$r[2],
                    'type'=>$r[3],
                    'list'=>$list
                ];
                // $res_arr['list'] = $list;
            }
        }
        
        //  $xml = simplexml_load_string($res['fieldset'],null,null,"field");
        // // foreach($xml->childen() as $child){
        // //     dump($child->getName());
        // // }
        // dump($res['fieldset']);
        // dump($xml);
        $this->assign("arr",$res_arr);
        $this->assign("channel",$res);
        return $this->fetch();
    }

    //模型-提交修改
    public function updatechannel(Request $request){
        if($request){
            $data='';
            $validate = new \app\admin\validate\Model;
            for($i=0;$i<count(input("post.field"));$i++){
                $field['field'] = input('post.field')[$i];
                $field['itemname'] = input('post.itemname')[$i];
                dump(input('post.field')[$i]);
                if (!$validate->check($field)) {
                    $this->error($validate->getError());
               }
                $data .="<field:".input('post.field')[$i]." itemname=\"".input('post.itemname')[$i]."\" type=\"".input('post.type')[$i]."\"> </field:".input('post.field')[$i].">\n";
            }
            dump($data);
            

            
            $res= Db::name('channeltype')
                    ->where('id',input('post.id'))
                    ->update(
                        [
                            'typename'=>input('post.typename'),
                            'fieldset'=>$data
                            ]
                        );
            if($res){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }
    }

    //模型-新增展示
    public function add(){
        $id = (Db::name('channeltype')->max('id')) + 1;
        $this->assign('id',$id);
        return $this->fetch();
    }




    //模型-软删除
    public function delmodel(){
        $id = input('post.id');
        $res = Db::name('channeltype')->where(['id'=>$id])
                        ->update(['isshow'=>'0']);
        if($res!==false){
            return ['code'=>1,'msg'=>'删除成功！','id'=>$id];
        }else{
            return ['code'=>0,'msg'=>'删除失败！'];
        }
    }
    public function type(){
        return [
            'htmltext',
            'text',
            'imgfile',
            'multitext',
            'number',
            'img',
            'addon',
            'float',
            'stepselect',
            'datetime',
            'int',
            'softlinks',
            'checkbox',
            'textdata'
        ];
    }
    public function mmmm(){
        $aaa = new MyNewMigration("table1","v1.0");
        $bbb = $aaa->add1("sh_archives");
        dump($bbb);
    }
}
class MyNewMigration extends AbstractMigration {
    public function add1($table){
        
        return $this->table($table);

    }
}