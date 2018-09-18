<?php
/**
 * Created by PhpStorm.
 * Publisher: Administrator
 * Date: 2018/9/10
 * Time: 17:33
 */
namespace app\admin_server\model;
use app\common\controller\Common;
use think\Db;
use think\Model;

class Admin extends Model {
    /**
     * 管理员登录
     * @param
     * @return array
     * @throws
     * */
    public function adminLogin($data){
        $common = new Common();
        $find = db('ads_admin.ads_admin')
            ->field('password')
            ->where(['name'=>$data['admin_name']])
            ->find();
        if(empty($find)){
            return $common->_return(1002,'账号不存在');
        }
        if(md5($data['password'])!=$find['password']){
            return $common->_return(1003,'密码不正确');
        }
//        $access_token = [
//            'admin_name'=>$data['name']
//        ];
//        $token = $common->createToken($access_token);
//        $redis = new Redis();
//        $redis->set($find['id'],$token);
        return $common->_return(1000,'登录成功');

    }
    /**
     * 获取管理员列表
     * @param $field['user.id,user_name,email,phone,status,real_name,role_id']
     * @return array
     * @throws
     * */
    public function getAdmins(){
        $common = new Common();
        //查询用户信息
        $select = db('ads_admin.ads_admin')
            ->alias('admin')
            ->join('ads_admin.ads_admins_roles a_r','a_r.admin_id=admin.id')
            ->join('ads_admin.ads_roles r','r.id=a_r.role_id')
            ->field('admin.id,admin_name,email,phone,status,role_name,real_name')
            ->order('admin.id desc')
            ->select();
        $user = [];
        foreach ($select as $vo){
            $vo['status'] = $vo['status']==0?'正常':'封禁';
            $user[] = $vo;
        }
        //如果用户查询为空，返回账号不存在
        if(empty($user)){
            return $common->_return(1002,'账号不存在');
        }
        $success = $common->_return(1000,'获取账号成功');
        $success['result']['accounts']=$user;
        return $success;
    }
    /**
     * 添加管理员
     * @param $data['user_name','role_id','password','real_name','phone','email']
     * @return array
     * @throws
     * */
    public function addAdmin($data){
        $common = new Common();
        //查询用户是否存在
        $arr = db('ads_admin.ads_admin')
            ->where(['phone'=>$data['phone']])
            ->whereOr(['admin_name'=>$data['admin_name']])
            ->find();
        //不为空则已存在
        if(!empty($arr)){
            return $common->_return(1005,'管理员已存在');
        }
        //提出role_id
        $role_id = $data['role_id'];
        unset($data['role_id']);
        //添加账号
        Db::startTrans();
        try{
            db('ads_admin.ads_admin')->insert($data);
            //给用户添加角色
            $getLastInsID = db('ads_admin.ads_admin')->getLastInsID();
            $role_data = [
                'admin_id'=>$getLastInsID,
                'role_id'=>$role_id
            ];
            db('ads_admin.ads_admins_roles')->insert($role_data);
            $success = $common->_return(1000,'管理员添加成功');
            Db::commit();
            return $success;
        }catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $common->_return(1001,'管理员添加失败');
        }
    }
    /**
     * 修改管理员
     * @param $data
     * @return array
     * @throws
     * */
    public function editAdmin($data){
        $common = new Common();
        //查询用户是否存在
        $arr = db('ads_admin.ads_admin')
            ->where(['id'=>$data['id']])
            ->find();
        dump($arr);die;
        //为空则不存在
        if(empty($arr)){
            return $common->_return(1002,'管理员不存在');
        }
        //修改账号
        Db::startTrans();
        try{
            db('ads_admin.ads_admins_roles')
                ->where(['admin_id'=>$data['id']])
                ->update(['role_id'=>$data['role_id']]);
            $id = $data['id'];
            unset($data['role_id']);
            unset($data['id']);
            db('ads_admin.ads_admin')->where(['id'=>$id])->update($data);
            return $common->_return(1000,'管理员添加成功');
        }catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $common->_return(1001,'管理员添加失败');
        }
    }
    /**
     * 角色判断
     * @param
     * @return array
     * @throws
     * */
    public function role_judge($role_id){

    }
    /**
     * 密码判断
     * @param
     * @return array
     * @throws
     * */
    public function pwd_judge($pwd){

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
        $pwd = md5($data['password']);
        $arr = db('ads_admin.ads_admin')->where(['id'=>$data['admin_id']])->update(['password'=>$pwd]);
        //改变为空则返回失败
        if(empty($arr)){
            return $common->_return(1001,'编辑失败');
        }
        return $common->_return(1000,'编辑成功');
    }
    /**
     * 验证码判断
     * @param
     * @return array
     * @throws
     * */
    public function captche_judge($captche){

    }
}