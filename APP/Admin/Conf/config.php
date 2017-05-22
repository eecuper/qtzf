<?php
/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
	// 'DEFAULT_MODULE'     => 'Home',
    /* 模板相关配置 */
	'TMPL_PARSE_STRING' => array(
	'__UPLOAD__' => __ROOT__ . '/Uploads/Picture/',
	'__PUBLIC__' => __ROOT__ . '/Public',
	'__STATIC__' => __ROOT__ . '/Public/static',
	'__PUBIMG__' => __ROOT__ . '/Public/images',
	'__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
	'__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
	'__IMG_TOP__'=> __ROOT__ . '/Public/' . MODULE_NAME . '/images_top',
	'__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
	'__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
	'__MOBILE__' => __ROOT__ . '/Public/Mobile'
	),
	'WEB_NAME'=>'青田县政府采购招标网'
);
