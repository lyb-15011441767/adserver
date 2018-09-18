<?php
/**
 * Created by PhpStorm.
 * Publisher: Administrator
 * Date: 2018/9/13
 * Time: 16:21
 */
namespace app\admin_server\controller;
use think\Controller;

class Base extends Controller{
    function _initialize()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials : true");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie");
    }
}