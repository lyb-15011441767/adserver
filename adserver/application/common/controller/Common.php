<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace app\common\controller;
// 应用公共文件


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use think\cache\driver\Redis;
use think\console\command\make\Controller;

class Common extends Controller {
    /**
     * 返回数据
     * @param
     * @return
     **/
    public function _return($code,$message){
        return [
            'return_code'=>$code,
            'result'=>[
                'message'=>$message
            ]
        ];
    }
    /**
     * 创建Token
     * @param $uid 需要保存的用户身份标识
     * @return String
     **/
    public static function createToken($token = null)
    {
        $signer = new Sha256();
        $token = (new Builder())->setIssuer('http://www.litemob.com')
            ->setAudience('http://www.litemob.com')
            ->setId('sxs-4f1g23a12aa', true) //自定义标识
            ->setIssuedAt(time()) //当前时间
            ->setExpiration(time() + (86400 * 30)) //token有效期时长
            ->set('token', $token)
            ->sign($signer, 'litemob')
            ->getToken();
        //这里可以做一些其它的操作，例如把Token放入到Redis内存里面缓存起来。
        /**
         * ......
         * ......
         **/
        return (String) $token;
    }
    /**
     * 检测Token是否过期与篡改
     * @param Tokens
     * @return boolean
     **/
    public static function validateToken($token = null)
    {
        $token = (new Parser())->parse((String) $token);
        $signer =new Sha256();
        if (!$token->verify($signer, 'litemob')) {
            return [
                'return_code'=>1009,
                'result'=>[
                    'message'=>'token过期，请重新登录！'
                ]
            ]; //签名不正确
        }

        $validationData = new ValidationData();
        $validationData->setIssuer('http://www.litemob.com');
        $validationData->setAudience('http://www.litemob.com');
        $validationData->setId('sxs-4f1g23a12aa');//自字义标识

        return $token->validate($validationData);
    }
    /**
     * token解密
     * */
    public static function decodeToken($token){

        $c = new Parser();
        $a = $c->parse($token)->getClaim('token');
        return $a;
    }
    /**
     * 随机数生成
     * @param
     * @return
     **/
    public function _rand($i){
        $rand = '';
        $captche = [1,2,3,4,5,6,7,8,9,0];
        for($a = 1;$a<=$i;$a++){
            $n = rand(0,9);
            $rand .= $captche[$n];
        }
        return $rand;
    }
    /**
     * 获取验证码
     * */
    public function getcatche(){
        $sms = new Sms( array('api_key' => 'faff3b0aa6082f2239f1def54e731cfe' , 'use_ssl' => FALSE ) );
    //send 单发接口，签名需在后台报备
        $catche = $this->_rand(6);
        $res = $sms->send( request()->post('phone'), '验证码：'.$catche.'【铁壳测试】');
        if( $res ){
            if( isset( $res['error'] ) &&  $res['error'] == 0 ){
                $redis = new Redis();
                $redis->set(request()->post('phone'),$catche);
                if($redis->get(request()->post('phone'))!=$catche){
                    echo json_encode($this->_return(1004,'验证码获取成功'));die;
                }
                echo json_encode($this->_return(1000,'验证码获取成功'));die;
            }else{
                echo json_encode($this->_return($res['error'],$res['msg']));die;
            }
        }else{
            echo json_encode($sms->last_error());die;
        }
    }
}
