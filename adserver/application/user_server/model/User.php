<?php
/**
 * Created by PhpStorm.
 * Publisher: Administrator
 * Date: 2018/9/10
 * Time: 17:33
 */
namespace app\user_server\model;
use app\common\controller\Common;
use think\cache\driver\Redis;
use think\Model;

class User extends Model {
    /**
     * 开发者登录
     * @param
     * @return array
     * @throws
     * */
    public function sspLogin($data){
        $common = new Common();
        $find = db('ads_user.ads_users')
            ->alias('user')
            ->field('user.id,phone,email,user_name,screen_name,role_id,password')
            ->where(['phone'=>$data['phone']])
            ->join('ads_user.ads_users_roles u_r','user.id=u_r.user_id')
            ->find();
        if(empty($find)){
            return $common->_return(1002,'账号不存在');
        }
        if($find['password'] != md5($data['password'])){
            return $common->_return(1003,'密码不正确');
        }
        if($find['role_id']!=1){
            return $common->_return(1004,'请切换开发者账号');
        }
        $access_token = [
            'phone'=>$data['phone'],
            'user_id'=>$find['id']
        ];
        $token = $common->createToken($access_token);

        $redis = new Redis();
        $redis->set($find['id'],$token);
        unset($find['role_id']);
        unset($find['password']);
        $success = $common->_return(1000,'登录成功');
        $success['result']['user_profile']=$find;
        return $success;
    }
    /**
     * 广告主登录
     * @param
     * @return array
     * @throws
     * */
    public function dspLogin($data){
        $common = new Common();
        $find = db('ads_user.ads_users')
            ->alias('user')
            ->field('user.id,phone,email,user_name,screen_name,role_id,password')
            ->where(['phone'=>$data['phone']])
            ->join('ads_user.ads_users_roles u_r','user.id=u_r.user_id')
            ->find();
        if(empty($find)){
            return $common->_return(1002,'账号不存在');
        }
        if($find['password'] != md5($data['password'])){
            return $common->_return(1003,'密码不正确');
        }
        if($find['role_id']!=2){
            return $common->_return(1004,'请切换广告主账号');
        }
        $access_token = [
            'phone'=>$data['phone'],
            'user_id'=>$find['id']
        ];
        $token = $common->createToken($access_token);

        $redis = new Redis();
        $redis->set($find['id'],$token);
        unset($find['role_id']);
        unset($find['password']);
        $success = $common->_return(1000,'登录成功');
        $success['result']['user_profile']=$find;
        return $success;
    }
    /**
     * 用户注册
     * */
    public function register(){

    }
    /**
     * 角色判断
     * */
    public function role_judge(){

    }
    /**
     * 密码判断
     * */
    public function pwd_judge($pwd){

    }
    /**
     * 验证码判断
     * */
    public function captche_judge($captche){

    }
    /**
     * 重置密码
     * @param $data['id','password']
     * @return array
     * @throws
     * */
    public function reset_password($data){
        $common = new Common();
//        $salt = $this->common->_rand(6);
//        $data['salt'] = $salt;
        $data['password'] = md5($data['password']);
        $arr = db('ads_user.ads_users')->update($data);
        //改变为空则返回失败
        if(empty($arr)){
            return $common->_return(1001,'编辑失败');
        }
        return $common->_return(1000,'编辑成功');
    }
}