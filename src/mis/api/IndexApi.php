<?php


namespace app\mis\api;


class IndexApi extends \app\mis\controller\BaseMis
{
    public function Menu(){
        return json($this->GetMenu());
    }

}