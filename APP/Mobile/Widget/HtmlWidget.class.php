<?php

namespace Home\Widget;
use Home\Controller\BaseController;

class HtmlWidget extends BaseController  {
	
	protected function _initialize(){
		parent::intiParamsURL();
	}

	public function nl_rpt($bill_id=''){
		$hrInfo = M('hrInfo');
		$hr = $hrInfo->where('bill_id='.$bill_id)->cache(true)->find();
		$this->assign('hr',$hr);

		$sql="select count(*) a from rank_hr_info";
		$info=$hrInfo->query($sql);
		$this->assign('info',$info);
		

		$sqls="SELECT a.* FROM rank_hr_train a,rank_hr_info b WHERE a.user_name=b.pos_name AND bill_id=".$bill_id."";
		$train = $hrInfo->query($sqls);
		$this->assign('train',$train);
		
		$s="select * from rank_hr_course";
		$cour = $hrInfo->query($s);
		$this->assign('cour',$cour);

		if(empty($hr)){
			$this->assign('errMsg','未获取到'.$bill_id.'用户信息');
		}

		$this->display('Group/nl_rpt_page');
	}

	public function nl_rpt_list($bill_id=''){
		  $hrRole = M('hrRole');
		  $name = I('name');
		  $countyName = I('countyName');
		  $billId = I('billId');
		  $userId = I('userId');
		  $pro = I('pro');
		  $xl = I('xl');
		  $dat = I('dat');
		  $dat1 = I('dat1');
		  $age = I('age');
		  $age1 = I('age1');
		  
		  $role   = $hrRole->where("bill_id='".$bill_id."'")->find();
		  if(empty($role)){
		  	$this->assign('errMsg',$bill_id.'暂无权限访问，请联系管理员');
		  }

		  $hrInfo = M('hrInfo');
		  $where = ' status=1 ';

		 
		  if($role['COUNTY_CODE']!='5780' && !empty($role['COUNTY_CODE'])){
		  	$where.=" and county_code='".$role['COUNTY_CODE']."'";
		  }
 
		  if(!empty($name)){
		  	$where.="and pos_name like '%".$name."%'";
		  }
		  if(!empty($countyName) && !empty($role['COUNTY_CODE'])){
		  	$where.="and county_name='".$countyName."'";
		  }
		  if(!empty($billId)){
		  	$where.="and bill_id like '%".$billId."%'";
		  }
		  if(!empty($userId)){
		  	$where.="and user_id like '%".$userId."%'";
		  }
		  if(!empty($pro)){
		  	$where.="and 用工性质 like '%".$pro."%'";
		  }
		  if(!empty($xl)){
		  	$where.="and 最高学历 like '%".$xl."%'";
		  }
		  if(!empty($dat)){
		  	$where.="and to_char(入职时间,'yyyy-mm-dd') >= '".$dat."'";
		  }
		  if(!empty($dat1)){
		  	$where.="and to_char(入职时间,'yyyy-mm-dd') <= '".$dat1."'";
		  }
		  if (!empty($age1)) {
		  	$where.="and 岗位工龄 <= ".$age1."";
		  }
		  if(!empty($age)) {
		  	$where.="and 岗位工龄 >= ".$age."";
		  }

		  $order = I('order');
		  $orders = 'COUNTY_NAME';
		  if(!empty($order)){
		  	$orders = $order;
		  }
		  //$hrs = $hrInfo->where($where)->order($orders)->select();
		  $hrs = parent::lists($hrInfo,'',$where,$orders);
 		  
		  $this->assign('hrs',$hrs);
		  $this->display('Group/nl_rpt_list');
	}

	public function widget_kjh_my(){
		$this->display('Group/widget_kjh_my');
	}

	public function widget_kjh_jj($bill_id=''){
		$sql = "SELECT to_char(更新时间,'yyyy-mm-dd') 更新时间_char ,a.* FROM LS_PANCM.mz_客户经理计件汇总 a WHERE 更新时间 =(SELECT MAX(更新时间) FROM LS_PANCM.mz_客户经理计件汇总) and 手机号码='".$bill_id."'";
		$m = M();
		$jj = $m->query($sql);
		$this->assign('jj',$jj[0]);

		$hrInfo=M('hrInfo');
		$hr = $hrInfo->where('bill_id='.$bill_id)->cache(true)->find();
		$this->assign('hr',$hr);

		$this->display('Group/widget_kjh_jj');
	}
	
}

?>