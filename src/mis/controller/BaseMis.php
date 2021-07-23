<?php


namespace app\mis\controller;


use app\BaseController;
use GuzzleHttp\Client;
use think\App;
use think\facade\Cache;
use think\facade\View;
use think\response\Redirect;

class BaseMis extends BaseController
{
    private $_allow_url = [
        '/mis/login',
        '/mis/api/login',
    ];


    public function __construct(App $app)
    {
        parent::__construct($app);

//        echo $this->getMemberId();die;
        if($this->getMemberId()<=0 && !in_array($this->request->url(),$this->_allow_url)){

            exit('access deny Please go to /mis/login');
        } else {
            $site_info = $this->GetSiteInfo();
            if($site_info['Result']=='true' && $site_info['ErrorId']==0){
                View::assign('system',$site_info['Data']);
            }
        }

    }

    /**
     * @return mixed
     */
    public function getMemberId()
    {
        return session('member_id');
    }

    /**
     * @param mixed $member_id
     */
    public function setMemberId($member_id): void
    {
        session('member_id',$member_id);
    }

    private function GetSiteInfo(){
        $url = '/openapi/Standard/v1/SiteInfo';
        return $this->_RequestCloud($url);
    }

    public function AuthAdmin($user,$password){
        $param['user_name'] = $user;
        $param['password'] = $password;
        $url = '/openapi/Standard/v1/AuthAdmin';
        return $this->_RequestCloud($url,$param);
    }

    public function GetMenu(){
        $url = '/openapi/Standard/v1/MenuList';
        return $this->_RequestCloud($url);
    }

    public function SendMail($param){
        $url = '/openapi/Standard/v1/SendEmail';
        return $this->_RequestCloud($url,$param);
    }


    private function _getAccessToken(){
        $token = Cache::get('cloud.access_token');
        if(empty($token)){
            $url = '/openapi/Authorize/v1/GetAccessToken';
            $post = [
                'client_id'=>env('GATEWAY.CLIENT_ID'),
                'client_secret'=>env('GATEWAY.CLIENT_SECRET'),
                'grant_type'=>'client_credentials'
            ];
            $data = $this->_RequestCloud($url,$post,false);
            Cache::set('cloud.access_token',$data['access_token'],3500);
            return $data['access_token'];

        } else {
            return $token;
        }
    }

    private function _RequestCloud($url,$param=[],$auth=true){
        $client = new Client();
        $option = [];
        if($auth){
            $option['headers'] = ['Authorization'=>'Bearer '.$this->_getAccessToken()];
            if(!empty($param)){
                $option['form_params'] = ['data'=>json_encode($param)];
            }
        } else {
            $option['form_params'] = $param;
        }

        $option['headers']['User-Agent'] = env('GATEWAY.AGENT');
        $res = $client->request('POST',env('GATEWAY.CLOUD_URL').$url,$option);
        if($res->getStatusCode()==200){
            return json_decode($res->getBody(),true);
        } else {
            return [];
        }
    }

}