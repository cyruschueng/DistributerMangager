<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientWxLoginAo extends CI_Model {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('client/clientGenderEnum','clientGenderEnum');
		$this->load->model('client/clientTypeEnum','clientTypeEnum');
		
		$wxAppId = 'wxad20aeed157e7847';
		$wxAppKey = 'ceb71bf49d02a0fce247add066d162f2';
		$wxScope = 'snsapi_userinfo';
		$wxCallback = 'http://'.$_SERVER['HTTP_HOST'].'/clientlogin/wxlogincallback';
		
		$this->load->library('wxSdk',array(
			'appId'=>$wxAppId,
			'appKey'=>$wxAppKey,
			'callback'=>$wxCallback,
			'scope'=>$wxScope
		),'wxSdk');
    }
	
	public function login($callback){
		$loginUrl = $this->wxSdk->getLoginUrl($callback);
		
		header('Location: '.$loginUrl);
	}
	
	public function loginCallback(){
		//调用QQ接口获取登录信息
		$accessToken = $this->wxSdk->getAccessTokenAndOpenId();
		
		$userInfo = $this->wxSdk->getUserInfo($accessToken['access_token'],$accessToken['openid']);
		
		$callback = $this->wxSdk->getLoginInfo();
		
		//第一次登录更新用户信息
		if( $userInfo['sex']== 1 )
			$gender = $this->clientGenderEnum->BOY;
		else if(  $userInfo['sex']== 2 )
			$gender = $this->clientGenderEnum->GIRL;
		else
			$gender = $this->clientGenderEnum->UNKNOWN;
		$image = $userInfo['headimgurl'];
		
		$clientId = $this->clientAo->addOnce(array(
			'name'=>$userInfo['nickname'],
			'gender'=>$gender,
			'image'=>$image,
			'openId'=>$accessToken['openid'],
			'year'=>0,
			'district'=>$userInfo['country'].' '.$userInfo['province'].' '.$userInfo['city'],
			'type'=>$this->clientTypeEnum->WX,
			'sign'=>''
		));
		
		//设置登录态
		$this->clientLoginAo->login($clientId);
		
		header('Location: '.$callback);
	}
}