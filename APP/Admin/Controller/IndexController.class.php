<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
 
class IndexController extends BaseController { 

    protected function _initialize(){        
        parent::_initialize();
        parent::intiParams();
       
        if($Think.ACTION_NAME!='login'){
             $user = session('user_auth');
             $this->assign('loginUser',$user);
            if(empty($user)){
                $this->display('index/login');
                die;
            }    
        }
       
        $c = M('attr');
        $where['id']=1;
        $config = $c->where($where)->find();
        $this->assign('config',$config);
    }

    //登录
    public function login(){
        if(IS_POST){
            $bill_id=$_POST['bill_id'];
            $pwd    =$_POST['pwd'];
            $m = M('admin');
            $d['bill_id']=$bill_id;
            $d['pwd']    =md5($pwd);
            $d['status'] = 1;
            $d['is_admin']= array('egt',1);
            $re = $m->where($d)->find();
            if($re){
                session('user_auth',null);
                session('user_auth',$re);
                $this->redirect('index/user',null,2,'登录成功，正在跳转..');
            }else{
                $this->error('登录失败,请核对您的账号密码');
            }
        }
        $this->display();
    }

    public function loginOut(){
        session('user_auth',null);
        $this->display('login');
    }

    //首页 
    public function index(){
        $this->display();
    }

    //nav
    public function nav(){
        $n = M('nav');
        //$where['status']=1;
        $navs_tmp  = $n->where($where)->order('ord,id')->select(); 
        $navs = parent::nav_sub(0,$navs_tmp);       
        $this->assign('navs',$navs);
        $this->display();
    }

    public function nav_list(){
        $n = M('nav');
        //$where['status']=1;
        $navs_tmp  = $n->where($where)->order('ord,id')->select(); 
        $navs = parent::nav_sub(0,$navs_tmp);       
        $this->assign('navs',$navs);
        $this->display();
    }
 
    public function nav_delete($id=0){
        $json['flag']=false;
        $m = M('nav');
        $where['id']=$id;
        $re = $m->delete($id);
        if($re){
            $json['flag']=true;
        }
        $this->ajaxReturn($json);
    }

    public function nav_edit($id=0){
        $json['flag']=false;
        $m = M('nav');
        if(IS_POST){
            $d['name']=$_POST['name'];
            $d['url']=$_POST['url'];
            $d['icon']=$_POST['icon'];
            $d['cid']=$_POST['cid'];
            $d['status']=$_POST['status'];
            $d['pid']=intval($_POST['pid']);
            $d['ord']=$_POST['ord'];
            $d['url_type']=$_POST['url_type'];
            $d['lvl']=$_POST['lvl'];
            $d['modify_date']=time();
            if(intval($id>0)){
                $where['id']=$id;
                $re = $m->where($where)->save($d);
            }else{
                $re = $m->add($d);
            }
            if($re){
                $json['flag']=true;
                $this->ajaxReturn($json);
            }
        }else{
            if(intval($id)>0){
                $nav = $m->find($id);
                $this->assign('nav',$nav);
            }

            
            $where['status']=1;
            $navs_tmp  = $m->where($where)->order('id')->select(); 
            $navs = $this->nav_sub(0,$navs_tmp);       
            $this->assign('navs',$navs);
            $this->display();
        }
    }

    public function nav_select(){
        $where['status']=1;
        $n=M('nav');
        $navs_tmp  = $n->where($where)->order('id')->select();    
        $navs = parent::nav_sub(0,$navs_tmp);    
        $this->assign('navs',$navs);
    }

    //admin
    public function admin($page=1){
        $u = M('admin');
        $sql = "select * from fn_admin where status=1 order by modify_date desc";
        $users = parent::listsBySql($sql,10);
        $this->assign('users',$users);
        $this->display();
    }

    public function admin_delete($id=0){
        $json['flag']=false;
        $m = M('admin');
        $where['id']=$id;
        $re = $m->delete($id);
        if($re){
            $json['flag']=true;
        }
        $this->ajaxReturn($json);
    }

    public function admin_edit($id=0){
        $json['flag']=false;
        $m = M('admin');
        if(IS_POST){
            $d['user_name']=$_POST['user_name'];
            $d['is_admin']=$_POST['is_admin'];
            $d['bill_id']=$_POST['bill_id'];
            $d['card_id']=$_POST['card_id'];
            $d['sex']=$_POST['sex'];
            $d['age']=$_POST['age'];
            $d['address']=$_POST['address'];
            $d['email']=$_POST['email'];
            $d['remark']=$_POST['remark'];
            $d['status']=$_POST['status'];
            $d['modify_date']=time();
            if(!empty($_POST['pwd'])){
                $d['pwd']=md5($_POST['pwd']);
            }
            if(intval($id>0)){
                $where['id']=$id;
                $re = $m->where($where)->save($d);
            }else{
                if(empty($_POST['pwd'])){
                    $d['pwd']=md5('123qwe');
                }
                $re = $m->add($d);
            }
            if($re){
                $json['flag']=true;
                $this->ajaxReturn($json);
            }
        }else{
            if(intval($id)>0){
                $user = $m->find($id);
                $this->assign('user',$user);
            } 
            $this->display();
        }
    }

    //user
    public function user($page=1){
        $u = M('user');
        $sql = "select * from fn_user where status=1 order by modify_date desc";
        $users = parent::listsBySql($sql,10);
        $this->assign('users',$users);
        $this->display();
    }

    public function user_delete($id=0){
        $json['flag']=false;
        $m = M('user');
        $where['id']=$id;
        $re = $m->delete($id);
        if($re){
            $json['flag']=true;
        }
        $this->ajaxReturn($json);
    }

    public function user_edit($id=0){
        $json['flag']=false;
        $m = M('user');
        if(IS_POST){
            $d['user_name']=$_POST['user_name'];
            $d['is_admin']=$_POST['is_admin'];
            $d['bill_id']=$_POST['bill_id'];
            $d['card_id']=$_POST['card_id'];
            $d['sex']=$_POST['sex'];
            $d['age']=$_POST['age'];
            $d['comp']=$_POST['comp'];
            $d['type']=$_POST['type'];
            $d['address']=$_POST['address'];
            $d['email']=$_POST['email'];
            $d['remark']=$_POST['remark'];
            $d['status']=$_POST['status'];
            $d['modify_date']=time();
             
            if(intval($id>0)){
                $where['id']=$id;
                $re = $m->where($where)->save($d);
            }else{
                $re = $m->add($d);
            }
            if($re){
                $json['flag']=true;
                $this->ajaxReturn($json);
            }
        }else{
            if(intval($id)>0){
                $user = $m->find($id);
                $this->assign('user',$user);
            } 
            $this->display();
        }
    }

    //config
    public function config(){
        $c = M('attr');
        $where['id']=1;
        $config = $c->where($where)->find();
        $this->assign('config',$config);
        $this->display();
    }

    //yuyue
    public function yuyue(){
        $m = M('yue');
        $sql = "select * from fn_yue where status=1 order by yue_date desc";
        $yues = parent::listsBySql($sql,10);
        $this->assign('yues',$yues);
        $this->display();
    }

    public function yue_delete($id=0){
        $json['flag']=false;
        $m = M('yue');
        $where['id']=$id;
        $re = $m->delete($id);
        if($re){
            $json['flag']=true;
        }
        $this->ajaxReturn($json);
    }

    public function yuyueb(){
        $m = M('yueBh');
        $sql = "select * from fn_yue_bh order by bh desc";
        $yues = parent::listsBySql($sql,10);
        $this->assign('yues',$yues);
        $this->display();
    }

    public function yueb_delete($id=0){
        $json['flag']=false;
        $m = M('yueBh');
        $where['id']=$id;
        $data['status']=0;
        $re = $m->where($where)->save($data);
        if($re){
            $json['flag']=true;
        }
        $this->ajaxReturn($json);
    }

    public function yueb_delete_data($id=0){
        $json['flag']=false;
        $m = M('yueBh');      
        $re = $m->delete($id);
        if($re){
            $json['flag']=true;
        }
        $this->ajaxReturn($json);
    }

    //留言板
    public function lyb(){
        $u = M('msg');
        $sql = "select * from fn_msg where status=1 order by create_date desc";
        $lys = parent::listsBySql($sql,10);
        $this->assign('lys',$lys);
        $this->display();
    }

    public function lyb_delete($id=0){
        $json['flag']=false;
        $m = M('msg');
        $where['id']=$id;
        $re = $m->delete($id);
        if($re){
            $json['flag']=true;
        }
        $this->ajaxReturn($json);
    }

    //供货协议
    public function pro(){
        $title = I('title');
        $type_id = I('type_id');
        $sql="select a.*,b.name as type_name,c.user_name 
                    from fn_pro a 
                    left join fn_nav b on a.type_id=b.id 
                    left join fn_user c on a.oper_id=c.id 
                    where a.status=1 
                    ";
        if(!empty($title)){
            $sql.=" and a.title like '%".$title."%'";
        }
        if(!empty($type_id)){
            $sql.=" and a.type_id=".$type_id;
        }
        $sql.=" order by a.create_date desc,a.id desc";                
        $p = M('pro');
        $pros = parent::listsBySql($sql,10);
        $this->assign('pros',$pros);
        self::nav_select();
        $this->display();
    }

    public function pro_query(){
        $title = I('title');
        $sql="select a.*,b.name as type_name,c.user_name 
                    from fn_pro a 
                    left join fn_nav b on a.type_id=b.id 
                    left join fn_user c on a.oper_id=c.id 
                    where a.status=1 
                    ";
        if(!empty($title)){
            $sql.=" and a.title like '%".$title."%'";
        }
        $sql.=" order by a.create_date desc,a.id desc";                
        $p = M('pro');
        $pros = parent::listsBySql($sql,10);
        $this->assign('pros',$pros); 
        $this->display(); 
    }

    public function pro_edit($id=0){
        $json['flag']=false;
        $m = M('pro');
        if(IS_POST){
            $d['title']=$_POST['title'];
            $d['type_id']=$_POST['type_id'];
            $d['icon']=$_POST['icon'];
            $d['status']=$_POST['status'];
            $d['content']=$_POST['content'];
            $d['valid_date']=$_POST['valid_date'];
            $d['expire_date']=$_POST['expire_date'];      
            $d['oper_id']=$_SESSION['user_auth']['id'];        
            if(intval($id>0)){
                $where['id']=$id;
                $re = $m->where($where)->save($d);
            }else{
                $d['create_date']=date('Y-m-d',time());
                $re = $m->add($d);
            }
            if($re){
                $json['flag']=true;
                $this->ajaxReturn($json);
            }
        }else{      
            if(intval($id)>0){
                $pro = $m->find($id);
                $this->assign('pro',$pro);
            } 

            self::nav_select();
            $this->display();
        }
    }

    public function pro_delete($id=0){
        $json['flag']=false;
        $m = M('pro');
        $where['id']=$id;
        $data['status']=0;
        $re = $m->where($where)->save($data);
        if($re){
            $json['flag']=true;
        }
        $this->ajaxReturn($json);
    }

}
