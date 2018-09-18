<?php
/**
 * Created by PhpStorm.
 * Publisher: Administrator
 * Date: 2018/9/10
 * Time: 17:32
 */
namespace app\user_server\controller;

use think\cache\driver\Redis;
use \app\user_server\model\User;

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
        //接收参数
        $act    = input('act');//判断用户角色
        $data   = request()->post();
        $Login  = new User();
        $login = $Login->$act($data);
        echo json_encode($login);die;
    }
    /**
     * 退出登陆
     * @param
     * @return array
     * @throws
     * */
    public function logout(){
        $user_id = request()->post('user_id');
        $redis = new Redis();
        $redis->set($user_id,null);
    }
}