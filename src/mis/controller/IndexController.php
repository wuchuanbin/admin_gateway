<?php


namespace app\mis\controller;


use think\facade\View;

class IndexController extends BaseMis
{
    public function Index(){
        return View::fetch('mis@index:index');
    }

}