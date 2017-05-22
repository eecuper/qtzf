<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class AdminController extends Controller {

    /**
     * 空处理
     */
    public function _empty(){
        E('访问地址错误,请重试');
    }
    
    /**
     * 后台控制器初始化
     */
    protected function _initialize(){
    }
    
    
    //是否登录
    protected function isLogin($flag=false){
        if(session('?user_auth')){
            return session('user_auth');
        }else{
            if($flag){
                echo '<script>alert("提示：当前登录会话退出或已经超时请重新登录!");</script>';                
            }else{
                return $flag;
            }           
        }
    } 
    
    //分页sql
    protected function listsBySql ($sql='',$page_size=10){
        $options    =   array();
        $REQUEST    =   (array)array_merge(I('request.'),I('post.'));
         
        $m = new Model();
        $total = $m->query("select count(*) as count from (".$sql.") as z");
        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $listRows = $page_size>0?$page_size:$listRows;
         
        $page = new \Think\Page($total[0]['count'], $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
         
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        return $m->query("select z.* from (".$sql.") as z limit ".$page->firstRow.",".$page->listRows);
  
    }
    
    //oracle sql 分页查询
    protected function listsSql($sql,$name,$countyName){
        $REQUEST    =   (array)array_merge(I('request.'),I('post.'));
            $m = new Model();
            $total = $m->query("select count(*) as count from (".$sql.")");
            /*$r  = I('r');
            if( isset($r)){
                $listRows = (int)$r;
            }else{
                $listRows = C('LIST_ROWS') > 10 ? C('LIST_ROWS') : 10;
            }*/

            if( isset($REQUEST['r']) ){
                $listRows = (int)$REQUEST['r'];
            }else{
                $listRows = C('LIST_ROWS') > 10 ? C('LIST_ROWS') : 20;
            }
           
            $page = new \Think\Page($total[0]['COUNT'], $listRows, $REQUEST);
            if($total>$listRows){
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            }
            $p =$page->show();
            $this->assign('_page', $p? $p: '');
            $this->assign('_total',$total[0]['COUNT']);
 
            
            return $m->query("select * from (select a.* , rownum row_num_id from (".$sql.") a) where row_num_id>".$page->firstRow." and row_num_id<=".($page->firstRow+$page->listRows));
    }

    protected function listsSqlByls($sql,$page_size=0){
            $REQUEST    =   (array)array_merge(I('request.'),I('post.'));
            $m = new Model();
            $total = $m->query("select count(*) as count from (".$sql.")");
            
            if($page_size>0){
                $listRows = (int)$page_size;
            }else{
                if( isset($REQUEST['r']) ){
                    $listRows = (int)$REQUEST['r'];
                }else{
                    $listRows = C('LIST_ROWS') > 10 ? C('LIST_ROWS') : 12;
                }
            }
           
            $page = new \Think\Page($total[0]['COUNT'], $listRows, $REQUEST);
            if($total>$listRows){
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            }
            $p =$page->show();
            $this->assign('_page', $p? $p: '');
            $this->assign('_total',$total[0]['COUNT']);
 
            
            return $m->query("select * from (select a.* , rownum row_num_id from (".$sql.") a) where row_num_id>".$page->firstRow." and row_num_id<=".($page->firstRow+$page->listRows));
    }

    //分页查询
    protected function lists ($model,$table='',$where=array(),$order='',$field=true,$base='1=1'){
        $options    =   array();
        $REQUEST    =   (array)array_merge(I('request.'),I('post.'));
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        $options['where'] = array_filter(array_merge( (array)$base, /*$REQUEST,*/ (array)$where ),function($val){
            if($val===''||$val===null){
                return false;
            }else{
                return true;
            }
        });
        if( empty($options['where'])){
            unset($options['where']);
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        
        if(is_string($table) && isset($table)){
            $total  =   $model->table($table)->where($options['where'])->count();
        }else{
            $total  =   $model->where($options['where'])->count();
        }
        

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);

        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('_num',$page->firstRow);
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;
        
        $model->setProperty('options',$options);
        
        if(is_string($table) && isset($table)){
            return $model->table($table)->field($field)->select();
        }
        return $model->field($field)->select();
    }

    //网页提交参数设置
    public function intiParams(){
        $arr = I();
        if(is_array($arr)){
            foreach ($arr as $key => $value) {
                $this->assign($key,$value);
            }
        }
    }

    //网页提交参数设置并且拼接结果URL 
    public function intiParamsURL(){
        $url = $this->get_url();
        $arr = I();
        if(is_array($arr)){
            foreach ($arr as $key => $value) {
                $index  =  strpos($url,'?');
                if($index>=0){
                    $url.="&".$key."=".$value;
                }else{
                    $url.="?".$key."=".$value;
                }
            }
        }
        $this->assign('initURL',$url);
    }
 
    /**
     * 获取当前页面完整URL地址
     */
    function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }

    public function downLoad(){
        $filePath = I('filePath');
        $showname = I('showname');
        $filename = str_replace('_', '/', $filePath);  
        // 调用类  
        $http = new \Org\Net\Http;  
        
        $http->download($filename, $showname); 
    }  

    //nav_sub
    public function nav_sub($pid=0 , $arr=array() , &$dest_arr=array()){
        foreach ($arr as $key => $a) {
           if($a['pid']==$pid){
             $dest_arr[]=$a;
             $this->nav_sub($a['id'],$arr,$dest_arr);
           }
        }
        return $dest_arr;
    } 

}
