<?php


namespace app\mis\controller;


use think\facade\View;

class LoginController extends BaseMis
{
    public function Login(){
        return View::fetch('mis@login:login');
    }

}