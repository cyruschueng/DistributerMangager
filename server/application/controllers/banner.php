<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banner extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('banner/companyBannerAo','companyBannerAo');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function search()
	{
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('title','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->companyBannerAo->search($userId,$dataWhere,$dataLimit);
	}
	
	/**
	* @view json
	*/
	public function getByUserId()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		
		//执行业务逻辑
		return $this->companyBannerAo->search($userId,array(),array());
	}
	
	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userCompanyBannerId','require'),
		));
		$userCompanyBannerId = $data['userCompanyBannerId'];
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->companyBannerAo->get($userId,$userCompanyBannerId);
	}
	
	/**
	* @view json
	*/
	public function add()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('image','require'),
			array('url','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyBannerAo->add($userId,$data);
	}
	
	/**
	* @view json
	*/
	public function del()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userCompanyBannerId','require'),
		));
		$userCompanyBannerId = $data['userCompanyBannerId'];
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyBannerAo->del($userId,$userCompanyBannerId);
	}
	
	/**
	* @view json
	*/
	public function mod()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userCompanyBannerId','require'),
		));
		$userCompanyBannerId = $data['userCompanyBannerId'];
		
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('image','require'),
			array('url','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyBannerAo->mod($userId,$userCompanyBannerId,$data);
	}
	
	/**
	* @view json
	*/
	public function moveUp()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userCompanyBannerId','require'),
		));
		$userCompanyBannerId = $data['userCompanyBannerId'];
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyBannerAo->move($userId,$userCompanyBannerId,'up');
	}
	
	/**
	* @view json
	*/
	public function moveDown()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userCompanyBannerId','require'),
		));
		$userCompanyBannerId = $data['userCompanyBannerId'];
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyBannerAo->move($userId,$userCompanyBannerId,'down');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
