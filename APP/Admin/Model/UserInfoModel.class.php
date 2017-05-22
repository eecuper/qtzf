<?php

namespace Home\Model;

use Think\Model;

class UserInfoModel extends Model {
	
	protected $_validate = array(
			array('nc','require','账号必须填写！'),
			array('phone','require','手机必须填写！')
	);
	
	protected $_auto 	 = array(
			array('status','1'),
			array('create_date','time',1,'function')
	);
	
	
	
}

?>