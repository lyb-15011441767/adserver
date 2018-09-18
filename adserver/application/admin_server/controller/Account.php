<?php
/**
 * Created by PhpStorm.
 * Publisher: Administrator
 * Date: 2018/9/13
 * Time: 16:18
 */
namespace app\admin_server\controller;

use app\admin_server\model\User;
use app\common\controller\Common;

class Account extends Base{
    /**
     * 获取开发者信息
     * */
    public function getPublishers(){
        $user = new \app\admin_server\model\User();
        $get_accounts = $user->getAccounts(['u_r.role_id'=>1]);
        echo json_encode($get_accounts);die;
    }
    /**
     * 添加开发者信息
     * */
    public function addPublisher(){
        $data   = request()->post();
        $data['role_id'] = 1;
        $user   = new \app\admin_server\model\User();
        $add_account = $user->addAccount($data);
        echo json_encode($add_account);
        die;
    }
    /**
     * 修改开发者信息
     * */
    public function editPublisher(){
        $user = new User();
    }
    /**
     * 获取广告主信息
     * */
    public function getAdvertisers(){
        $user           = new User();
        $get_accounts   = $user->getAccounts(['u_r.role_id'=>2]);
        echo json_encode($get_accounts);die;
    }
    /**
     * 添加广告主信息
     * */
    public function addAdvertiser(){
        $data               = request()->post();
        $data['role_id']    = 2;
        $user               = new User();
        $add_account        = $user->addAccount($data);

        echo json_encode($add_account);
        die;
    }
    /**
     * 修改广告主信息
     * */
    public function editAdvertiser(){

    }
    /**
     * 重置密码
     * */
    public function reset_password(){
        $user = new \app\user_server\model\User();
        $data = request()->post();
        echo json_encode($user->reset_password($data));die;
    }
    /**
     * 审核用户
     * */
    public function audit_account(){
        $user = new User();
        $act = input('act');
        $account_id = request()->post('id');
        if($act == 'getAudit'){
            echo json_encode($user->get_audit_account($account_id));die;
        }else{
            echo json_encode($user->edit_audit_account(request()->post()));
        }
    }
}