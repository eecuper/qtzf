<?php

error_reporting(0);
//隐藏警告信息
error_reporting(E_ALL^E_NOTICE);
//隐藏错误信息
ini_set("display_errors", "Off");

include "Addons/phpqrcode/phpqrcode.php";
 
$_GPC = array();
$_GPC = array_merge($_GET, $_POST, $_GPC);
$value=$_GPC["link"];

if(empty($value)){
	
	echo '二维码生成地址不准备，请重试';
	exit;
	
}

$errorCorrectionLevel = "L";
$matrixPointSize = "4";
QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize);
exit;

?>