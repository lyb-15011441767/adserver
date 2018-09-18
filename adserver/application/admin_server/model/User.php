<?php
/**
 * Created by PhpStorm.
 * Publisher: Administrator
 * Date: 2018/9/13
 * Time: 16:24
 */
namespace app\admin_server\model;
use app\common\controller\Common;
use think\Db;
use think\Model;

class User extends Model{
    //账户类型
    protected $type = ['企业','个人'];
    /**
     * 获取账号列表
     * @param $field
     * @return array
     * @throws
     * */
    public function getAccounts($where=1){
        $common = new Common();
        //查询用户信息
        $select = db('ads_user.ads_users')
            ->alias('user')
            ->join('ads_user.ads_users_roles u_r','u_r.user_id=user.id')
            ->field('user.id,user_name,phone,status,type,created_at,login_at')
            ->where($where)
            ->order('user.id desc')
            ->select();
        $user = [];
        foreach ($select as $vo){
            $vo['status'] = $vo['status']==0?'正常':'封禁';
            $vo['type'] = $this->type[$vo['type']];
            $user[] = $vo;
        }
        //如果用户查询为空，返回账号不存在
        if(empty($user)){
            return $common->_return(1002,'没有用户');
        }
        $success = $common->_return(1000,'获取账号成功');
        $success['result']['accounts']=$user;
        return $success;
    }
    /**
     * 添加账号
     * @param $data['user_name','role_id','password','real_name','phone','email']
     * @return array
     * @throws
     * */
    public function addAccount($data){
        $data['created_at'] = time();
        $data['screen_name'] = $data['phone'];
        $data['password'] = md5($data['password']);
        $common = new Common();
        //查询手机号是否存在
        $arr = db('ads_user.ads_users')
            ->where(['phone'=>$data['phone']])
            ->find();
        //不为空则已存在
        if(!empty($arr)){
            return $common->_return(1005,'账号已存在');
        }
        //提出role_id
        $role_id = $data['role_id'];
        unset($data['role_id']);
        //添加账号
        Db::startTrans();
        try{
            db('ads_user.ads_users')->insert($data);
            //给用户添加角色
            $getLastInsID = db('ads_user.ads_users')->getLastInsID();
            $role_data = [
                'user_id'=>$getLastInsID,
                'role_id'=>$role_id
            ];
            db('ads_user.ads_users_roles')->insert($role_data);
            $role = db('ads_user.ads_users_roles')->getLastInsID();
            Db::commit();
        }catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        //角色添加成功，则账号添加成功
        if(0==$role){
            return $common->_return(1001,'账号添加失败');
        }
        $success = $common->_return(1000,'账号添加成功');
        return $success;
    }
    /**
     * 获取审核信息
     * @param $field
     * @return array
     * @throws
     * */
    public function get_audit_account($user_id){
        $common = new Common();
        $field = 'user.id,user_name,type,phone,id_fore_image_url,id_back_image_url,license_image_url,enterprise_name,bank_name,bank_branch,bank_account,license_number,invoice_type,tax_point';
        $data = db('ads_user.ads_users')
            ->alias('user')
            ->join('ads_user.ads_personal_details p_d','user.id=p_d.id')
            ->field($field)
            ->where(['user.id'=>$user_id])
            ->find();
        if(empty($data)){
            return $common->_return(1002,'数据不存在');
        }
        $success = $common->_return(1000,'数据获取成功');
        $success['result']['media'] = $data;
        echo json_encode($success);
        die;
    }
    /**
     * 修改审核信息
     * @param $field
     * @return array
     * @throws
     * */
    public function edit_audit_account($user_id){
        $common = new Common();
        $field = 'user.id,user_name,type,phone,id_fore_image_url,id_back_image_url,license_image_url,enterprise_name,bank_name,bank_branch,bank_account,license_number,invoice_type,tax_point';
        $data = db('ads_user.ads_users')
            ->alias('user')
            ->join('ads_user.ads_personal_details p_d','user.id=p_d.id')
            ->field($field)
            ->where(['user.id'=>$user_id])
            ->find();
        if(empty($data)){
            return $common->_return(1002,'数据不存在');
        }
        $success = $common->_return(1000,'数据获取成功');
        $success['result']['media'] = $data;
        echo json_encode($success);
        die;
    }
    /**
     * 重置密码
     * @param $data['id','password']
     * @return array
     * @throws
     * */
    public function reset_password($data){
        $common = new Common();
        $data['password'] = md5($data['password']);
        $arr = db('ads_admin.ads_admin')->update($data);
        //改变为空则返回失败
        if(empty($arr)){
            return $common->_return(1001,'编辑失败');
        }
        return $common->_return(1000,'编辑成功');
    }
}