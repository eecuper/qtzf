<?php

namespace Home\Controller;

class IndexController extends BaseController {

	protected function _initialize(){
		parent::intiParams();
	}

	public function index(){
		redirect(U('mobile/index'));
	}
}

?>