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
            dump(input('post.'));
            foreach(input('post.itemname') as $k =>$v){
                if($v==''){
                    echo "你没填";
                    exit;
                }
            }
            echo "OK";
            
            // dump(input('post.'));
            // dump(input('post.field'));
            $type = input('post.type');
            $data = input('post.field');
            $arr = [];
            foreach($data as $k =>$v){
                if($v!=""){
                    $sql="select COLUMN_TYPE from information_schema.columns where table_schema = 'tp5item' AND table_name='".input('post.addtable')."' and column_name='".$v."';";
                    $res = Db::query($sql);
                    if($res){
                        echo "不是空";
                        if($res[0]["COLUMN_TYPE"]==$this->seltype($type[$k])){
                            echo $this->seltype($type[$k])."不用改<br/>";
                        }else{
                            $sql = "alter table ".input('post.addtable')." modify column ".$v." ".$this->seltype($type[$k]);
                            $res1 = Db::query($sql);
                            
                            if($res1!==false){
                                echo '修改成功<br/>';
                            }else{
                                echo '修改失败<br/>';
                                echo $sql."<br/>";
                            }
                        }
                    }else{
                        $res = Db::query("alter table ".input('post.addtable')." add ".$v." ".$this->seltype($type[$k]));
                        if($res){
                            echo "新增成功";
                        }else{
                            echo "新增失败";
                        }
                    }
                    echo "真棒！".$v."<br/>";
                    $arr[]=$v;
                  
                }
            }
            dump(input('post.'));
            $data='';
            $validate = new \app\admin\validate\Model;
            for($i=0;$i<count($arr);$i++){
                $field['field'] = $arr[$i];
                $field['itemname'] = input('post.itemname')[$i];
                // dump($arr[$i]);
                if (!$validate->check($field)) {
                    $this->error($validate->getError());
               }
                $data .="<field:".$arr[$i]." itemname=\"".input('post.itemname')[$i]."\" type=\"".input('post.type')[$i]."\"> </field:".$arr[$i].">\n";
            }
            // dump(input('post.'));
            

            
            $res= Db::name('channeltype')
                    ->where('id',input('post.id'))
                    ->update(
                        [
                            'typename'=>input('post.typename'),
                            'fieldset'=>$data,
                            'addtable'=>input('post.addtable')
                            ]
                        );
            
            if($res){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }
    }


    //模型-根据选择类型判断字段类型
    public function seltype($type){
        switch($type){
            case 'imgfile':
            case 'addon':
            case 'text':
            case 'img':
                $res = 'varchar(255)';
                break;
            case 'multitext':
            case 'htmltext':
                $res = 'mediumtext';
                break;
            case 'int':
            case 'datetime':
                $res = 'int(11)';
                break;
            case 'softlinks':
                $res = 'text';
                break;
            case 'checkbox':
                $res = 'set';
                break;
            case 'number':
                $res = 'smallint(6)';
                break;
            case 'stepselect':
                $res = 'char(15)';
                break;
            case 'float':
                $res = 'float';
            default:
                $res = 'varchar(255)';
        }
        return $res;
    }
    //模型-新增展示
    public function add(){
        $id = (Db::name('channeltype')->max('id')) + 1;
        $this->assign('id',$id);
        return $this->fetch();
    }

    //模型-新增提交
    public function addmodel(Request $request){
        if($request){
            $data=[
                'id'=>input('post.id'),
                'nid'=>input('post.nid'),
                'typename'=>input('post.typename'),
                'addtable'=>input('post.addtable')
            ];
            $res = Db::query('SELECT * FROM sh_channeltype WHERE id=\''.$data['id'].'\' OR nid LIKE \''.$data['nid'].'\' OR addtable LIKE \''.$data['addtable'].'\' LIMIT 0,1');
            $res1 = Db::query('SHOW TABLES LIKE \''.$data['addtable'].'\'');
            if($res!=false || $res1!=false){
                echo "ID、名字标识或附加表名可能已经存在，请修改";
            }else{
                $res = Db::query("DROP TABLE IF EXISTS `".$data['addtable']."`");
                $res1 = Db::query("
                CREATE TABLE `".$data['addtable']."`(
                    `aid` int(11) NOT NULL default '0',
                  `typeid` int(11) NOT NULL default '0',
                  `redirecturl` varchar(255) NOT NULL default '',
                  `templet` varchar(30) NOT NULL default '',
                  `userip` char(15) NOT NULL default '',
             PRIMARY KEY  (`aid`), KEY `typeid` (`typeid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
                
                ");
                $res2 = Db::query("
                INSERT INTO `sh_channeltype`(id,nid,typename,addtable,addcon,mancon,editcon,useraddcon,usermancon,usereditcon,fieldset,listfields,issystem,issend,arcsta,usertype,sendrank,needdes,needpic,titlename,onlyone,dfcid)
    VALUES ('".$data['id']."','".$data['nid']."','".$data['typename']."','".$data['addtable']."','archives_add.php','content_list.php','archives_edit.php','archives_add.php','content_list.php','archives_edit.php','','','0','0','-1','','0','1','1','标题','0','0')
                
                ");
                if($res && $res1 && $res2){
                    return ['code'=>1,'msg'=>'添加模型成功！'];
                }
            }
            // $a = new MyNewMigration("table1","v1.0");
            // $a->add1();
            
            
            
            // $aaa = new MyNewMigration("table1","v1.0");
            // //把传过来的input东西放到方法里
            
            // dump($aaa->add1($data));
        }else{
            return ['code'=>0,'msg'=>'请提交内容！'];
        }
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
        $aaa = new MyNewMigration("tp5item","v1.0");
        $bbb = $aaa->add1();
        dump($bbb);
    }
}
class MyNewMigration extends AbstractMigration {
    public function add1(){
        $exists = $this->hasTable('sh_archives');
        if ($exists) {
            return  "这个表存在";
        }
    }
}