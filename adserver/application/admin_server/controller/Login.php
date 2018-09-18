<?php
/**
 * Created by PhpStorm.
 * Publisher: Administrator
 * Date: 2018/9/10
 * Time: 17:32
 */
namespace app\admin_server\controller;

use app\admin_server\model\Admin;
use think\cache\driver\Redis;

class Login {
    public function _initialize(){

    }
    /**
     * 登陆
     * @param
     * @return array
     * @throws
     * */
    public function login(){
        $model = new Admin();
        $data  = request()->post();
        $model->adminLogin($data);
    }
    /**
     * 退出登陆
     * @param
     * @return array
     * @throws
     * */
    public function logout(){
        $user_id = request()->post('admin_id');
        $redis = new Redis();
        $redis->set($user_id,null);
    }
}