<?php

namespace Mobile\Controller;

class IndexController extends BaseController {

	protected function _initialize(){
		parent::intiParams();

		$config = self::config();
        $this->assign('config',$config);

        $n = M('nav');
        $where['status']=1;
        $navs_tmp  = $n->where($where)->order('ord,id')->select(); 
        $navs = parent::nav_sub(0,$navs_tmp);     
		//$this->assign('navs',$navs);
		$this->assign('global_navs',$navs);
		$this->assign('global_subs',$navs_tmp);
	}

	
	public function index(){
		// $n = M('nav');
  //       $where['status']=1;
  //       $where['pid']=0;
  //       $navs_tmp  = $n->where($where)->order('ord,id')->select(); 
  //       $navs = parent::nav_sub(0,$navs_tmp);       
		// $this->assign('navs',$navs);
		$this->display('main');
	}

	public function index2(){
		$menus = parent::menuList();
		$this->assign('menus',$menus);
		$this->display('index');
	}

	//config
    public function config(){
        $c = M('attr');
        $where['id']=1;
        $config = $c->where($where)->find();
        return $config;
    }

	public function sub_link($pid=0){
		$m = M('nav');
		$where['status']=1;
		$where['pid']=$pid;
		$navs = $m->where($where)->order('ord ,id')->select();
		$this->assign('navs',$navs);
		$this->display('index_sub');
	}

	public function newsList($rowSize=10000){
		$url = I('url');
		$news = parent::newsList($url,$rowSize);
		$this->assign('news',$news);
		$this->display();
	}

	public function newsList_local($id=0){
		 
        $sql="select a.*,b.name as type_name,c.user_name 
                    from fn_pro a 
                    left join fn_nav b on a.type_id=b.id 
                    left join fn_user c on a.oper_id=c.id 
                    where a.status=1 and a.type_id=".$id;
        $sql.=" order by a.valid_date desc,a.id desc";  
        $p = M('pro');
        $news = $p->query($sql);
		$this->assign('news',$news);
		$this->display();
	}


	public function newsList_json($rowSize=10000){
		$url = I('url');
		$news = parent::newsList($url,$rowSize);
		$this->ajaxReturn($news);
	}

	public function newsInfo(){
		$url = I('url');
		$newsInfo = parent::newsInfo($url);
		$this->assign('newsInfo',$newsInfo);
		$this->display();
	}

	public function yue(){
		$m = M('yue');
		$sql = "SELECT * FROM fn_yue WHERE  STATUS=1 and yue_date>='".date('Y-m-d',time())."'";
		$res = $m->query($sql);
		if(is_array($res)){
			foreach ($res as $key => $re) {
				$mark['bill_id']=$re['bill_id'];
				$mark['user_name']=$re['user_name'];
				$mark['year']=substr($re['yue_date'], 0,4);
				$mark['mon']=intval(substr($re['yue_date'], 5,2));
				$mark['d']=intval(substr($re['yue_date'], 8,2));
				$mark['ap']=$re['yue_ap'];
				$mark['remark']=$re['remark'];
				$mark['content']=json_encode($re);
				$marks[] = $mark;
			}
		}
		$this->assign('marks',json_encode($marks));
		$this->display();
	}

	//预约控件表格中制定日期的预约详情
	public function yue_list($yue_date=''){
		$m = M('yue');
		$sql = "SELECT a.*,case when yue_ap='am' then '上午' else '下午' end yue_wu FROM fn_yue a WHERE  STATUS=1 and yue_date='".$yue_date."' order by yue_ap";
		$res = $m->query($sql);
		$this->ajaxReturn($res);
	}

	public function yue_my($bill_id=''){
		$m = M('yue');		
		$sql = "SELECT * FROM fn_yue WHERE  STATUS=1 and bill_id='".$bill_id."' and yue_date>='".date('Y-m-d',time())."' order by yue_date desc";
		$re = $m->query($sql);
		$this->ajaxReturn($re);
	}

	public function yue_my_del($id=0){
		$j['flag']=false;
		$m = M('yue');
		$re = $m->delete($id);
		if($re){
			$j['flag']=true;
		}
		$this->ajaxReturn($j);
	}

	public function yue_add(){
		if(IS_POST){
			$json['flag']=false;
			$json['msg']='预约失败';
			$m = M('yue');
			$d['user_name']=$_POST['user_name'];
			$d['bill_id']=$_POST['bill_id'];
			$d['comp_name']=$_POST['comp_name'];
			$d['yue_date']=$_POST['yue_date'];
			$d['yue_ap']=$_POST['ap'];
			$d['room']=$_POST['room'];
			$d['room2']=$_POST['room2'];
			$d['remark']=$_POST['remark'];
			$d['status']=1;

			$u = M('user');
			$uw['bill_id']=$d['bill_id'];
			$uw['status']=1;
			$uc = $u->where($uw)->count();
		 
			if($uc<=0){
				$json['msg']='您的用户名暂时不能预约';
			}else{ 
				$room1 = (empty($d['room'])?'room1':$d['room']);
				$room2 = (empty($d['room2'])?'room2':$d['room2']);
				$where="status=1 and yue_date='".$d['yue_date']."' and yue_ap='".$d['yue_ap']."' and (room='".$room1."' or room2='".$room2."')";				
				$yues = $m->where($where)->count();
				// echo $m->getLastSql();
				if($yues>0){
					$json['msg']='已经被预约,请选择其他时间';
				}else{
					$re = $m->add($d);
					if($re){
						 $json['flag']=true;
						 $json['msg']='预约成功';
					}
				}
			}
			
			$this->ajaxReturn($json);
		}else{
			$this->display();
		}
	}

	public function yue_bdd(){
		if(IS_POST){
			$json['flag']=false;
			$json['msg']='预约失败';
			$m = M('yueBh');
			$d['bh'] = $_POST['bh'];
			$d['user_name']=$_POST['user_name'];
			$d['bill_id']=$_POST['bill_id'];
			$d['comp_name']=$_POST['comp_name'];
			$d['pro_name']=$_POST['pro_name'];
			$d['yue_date']=$_POST['yue_date'];
			$d['yue_ap']=$_POST['yue_ap'];
			$d['remark']=$_POST['remark'];
			$d['status']=1;

			$u = M('user');
			$uw['bill_id']=$d['bill_id'];
			$uw['status']=1;
			$uc = $u->where($uw)->count();
		 
			if($uc<=0){
				$json['msg']='您的手机号暂时不能预约';
			}else{
				$where['bh']=$d['bh'];
				$where['status']=1;
				$yues = $m->where($where)->count();
				if($yues>0){
					$json['msg']='编号已经被预约';
				}else{
					$re = $m->add($d);
					if($re){
						 $tj['bh']=$where['bh'];
						 $tj['status']=0;
						 $m->where($tj)->delete();
						 $json['flag']=true;
						 $json['msg']='预约成功';
					}
				}
			}
			
			$this->ajaxReturn($json);
		}else{
			//查询之前有取消的编号
			$y = M('yueBh');
			$sql = "select bh from fn_yue_bh where status=0 and left(yue_date,4)='".date('Y',time())."' order by bh";
			$bhs =$y->query($sql);
			// dump($bhs);
			if($bhs){
				$this->assign('bh',$bhs[0]['bh']);
			}else{
				$sql = "select count(*) as cnt from fn_yue_bh where left(yue_date,4)='".date('Y',time())."'";			
				$cnt =$y->query($sql);
				// dump($cnt);
				//had been order least ID
				$curr_cnt = intval($cnt[0]['cnt']);
				$id = '';
				if(($curr_cnt+1)<10){
					$id='00'.($curr_cnt+1);
				}
				if(($curr_cnt+1)>=10 && ($curr_cnt+1)<100){
					$id='0'.($curr_cnt+1);
				}
				if(($curr_cnt+1)>=100 && ($curr_cnt+1)<1000){
					$id=($curr_cnt+1);
				}
				$bh = "QTFSCG".date('Y',time())."-".$id;
				$this->assign('bh',$bh);
			}
			$this->display();
		}
	}

	//编号预约清单
	public function yue_his(){
		$m = M('yueBh');
        $sql = "select * from fn_yue_bh order by bh desc";
        $yues = parent::listsBySql($sql,10);
        $this->assign('yues',$yues);
        $this->assign('nav_name','编号预约详情');
        $this->display();
	}

	//留言
	public function lyb_add(){
		if(IS_POST){
			$json['flag']=false;
			$json['msg']='留言失败';
			$m = M('msg');
			$d['user_name']=$_POST['user_name'];
			$d['bill_id']=$_POST['bill_id'];			 
			$d['text']=$_POST['text'];
			$d['status']=1;
			$d['type']=$_POST['type'];
			$d['create_date']=time();
			$re = $m->add($d);
			if($re){
				 $json['flag']=true;
				 $json['msg']='留言成功';
			}			
			$this->ajaxReturn($json);
		}else{
			$this->display();
		}
	}

	public function xieyi(){
		$this->display();
	}

	public function xieyiInfo($id=0){
		$p = M('pro');
		$newsInfo = $p->find($id);
		$this->assign('newsInfo',$newsInfo);
		// echo htmlspecialchars($newsInfo['content']);
		// die;
		$this->display();
	}

	public function newsQuery($title='',$mon='',$date='',$content=''){
		$nav_name='检索';
		$this->assign('nav_name',$nav_name);
		if(strlen($title)>0) $kwd['title']=trim($title);
		if(strlen($content)>0) $kwd['content']=trim($content);
		if(strlen($mon)>0) $kwd['mon']=trim($mon);
		if(strlen($date)>0) $kwd['date']=trim($date);
		$news = parent::newsQuery($kwd);
		$this->assign('news',$news);
		$this->display();
	}
}

?>