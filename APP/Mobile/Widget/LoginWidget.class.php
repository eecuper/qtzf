<?php

namespace Home\Widget;
use Home\Controller\BaseController;

class LoginWidget extends BaseController  {

	protected function _initialize(){
		parent::intiParamsURL();
	}

	public function login(){
		$this->display('Sys/widget_login');
	}
}

?>