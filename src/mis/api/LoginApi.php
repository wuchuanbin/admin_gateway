<?php


namespace app\mis\api;


use app\common\InternalResponse;

class LoginApi extends \app\mis\controller\BaseMis
{
    public function Login(){
       $user = input('user');
       $password = input('password');
       $res = $this->AuthAdmin($user,$password);
       if($res['Result']=='true'){
           //login succ
           $this->setMemberId($res['Data']['id']);
           $res['Data']['url'] = '/mis/index';
           return json(InternalResponse::Success($res['Message'],$res['Data']));
       } else {
           return json(InternalResponse::Fail($res['ErrorId'],$res['Message'],[]));
       }
    }

}