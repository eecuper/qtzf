<?php
error_reporting(0);
//隐藏警告信息
error_reporting(E_ALL^E_NOTICE);
//隐藏错误信息
ini_set("display_errors", "Off");

function ihtmlspecialchars($var) {
	if (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[htmlspecialchars($key)] = ihtmlspecialchars($value);
		}
	} else {
		$var = str_replace('&amp;', '&', htmlspecialchars($var, ENT_QUOTES));
	}
	return $var;
}

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
$cb = $_GET['callback'];
curl_close($ch);
echo $cb."({msg:".json_encode($data)."})";

?>