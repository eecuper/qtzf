<?php

namespace Admin\Controller;
use Think\Controller; 

class UtilController extends Controller {	

	//公用的select 标签生成
	public function select($array , $default){
		if(!isset($default)){
			$default='';
		}
		if(is_array($array)){
			foreach ($array as $key => $val) {
				if($default==$val){
					$str.='<option selected value="'.$val.'">'.$key.'</option>';	
				}else{
					$str.='<option value="'.$val.'">'.$key.'</option>';
				}				
			}
		}
		echo $str;
	}

	//生成验证码
	Public function verify(){
		ob_clean();
		$config = array(
		 'fontSize' => 16, // 验证码字体大小
		 'length' => 4, // 验证码位数
		 'useNoise' => false, // 关闭验证码杂点
		 'useCurve' => false,
		 'imageW' => 168,
		 'imageH' => 35
		 );

	     $Verify = new \Think\Verify($config);
	     $Verify->entry();
    }

    //图形验证码匹配
	public function verCheck($code=''){
		$json['flag']=false;
		if(!empty($code)){
			$verify = new \Think\Verify();
			$flag = $verify->check($code, '');
			$json['flag']=$flag;
		}
		$this->ajaxReturn($json,'json');
	}

	//短信验证码
    public function yzm(){
    	$_GPC = array();
    	$_GPC = array_merge($_GET, $_POST, $_GPC);
    	$_GPC = ihtmlspecialchars($_GPC);
    	
    	$loginBill=empty($_GPC['loginBill'])?$_COOKIE['loginBill']:$_GPC['loginBill'];
    	$curlPost ="mobile=".$loginBill;
    	 
    	$ch = curl_init();
    	curl_setopt($ch,CURLOPT_URL,"http://jl.lscity.net/wap/hw/mysl/sendCode.jsp");
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch,CURLOPT_POST, 1);
    	curl_setopt($ch,CURLOPT_POSTFIELDS, $curlPost);
    	
    	$data =curl_exec($ch);
    	$data =str_replace("\r\n\r\n","",$data);
    	//传递过来的callback 作为返回
    	curl_close($ch);
    	$json['success']=true;
    	$json['msg']=$data;
    	$this->ajaxReturn($json);
    } 

}

?>