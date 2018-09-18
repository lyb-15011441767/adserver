<?php
/**
 * Created by PhpStorm.
 * Publisher: Administrator
 * Date: 2018/9/13
 * Time: 16:18
 */
namespace app\admin_server\controller;

class Admin extends Base{
    /**
     * 获取管理员信息
     * */
    public function getAdmins(){
        $admin = new \app\admin_server\model\Admin();
        $get_admins = $admin->getAdmins();
        echo json_encode($get_admins);die;
    }
    /**
     * 添加管理员信息
     * */
    public function addAdmin(){
        $data   = request()->post();
        $admin   = new \app\admin_server\model\Admin();

        $data['created_at'] = time();
        $data['password'] = md5($data['password']);
        $add_account = $admin->addAdmin($data);
        echo json_encode($add_account);
        die;
    }
    /**
     * 修改管理员信息
     * */
    public function editAdmin(){
        $data   = request()->post();
        $admin   = new \app\admin_server\model\Admin();
        $data['created_at'] = time();
        $add_admin = $admin->editAdmin($data);
        echo json_encode($add_admin);
        die;
    }
    /**
     * 重置密码
     * */
    public function reset_password(){
        $data = request()->post();
        $admin = new \app\admin_server\model\Admin();
        echo json_encode($admin->reset_password($data));die;
    }
}