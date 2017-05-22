<?php

namespace Common\Behavior;

class testBehavior{
	/* (non-PHPdoc)
	 * @see \Think\Behavior::run()
	 */
	public function run($params) {
		
		echo '行为开始 ........wqw...........<br>';
		
		if(!empty($params)){
			var_dump($params);
		}else{
			echo '参数列表为空<br>';
		}
		
		
		if(C('TEST_PARAM')) {
			echo 'RUNTEST BEHAVIOR '.$params;
		}
		
		echo '行为结束 ...................<br>';
		
		
	}

}

?>