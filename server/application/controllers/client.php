<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Client extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('client/clientTypeEnum','clientTypeEnum');
		$this->load->model('user/loginAo','loginAo');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function getType()
	{
		return $this->clientTypeEnum->names;
	}
	
	/**
	* @view json
	*/
	public function search()
	{
		//检查输入参数
		$dataWhere = $this->argv->checkGet(array(
			array('type','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_INTRODUCE
		);
		$userId = $user['userId'];
			
		//执行业务逻辑
		return $this->clientAo->search($userId,$dataWhere,$dataLimit);
	}
	
	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('clientId','require'),
		));
		$clientId = $data["clientId"];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_INTRODUCE
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->clientAo->get(
			$userId,
			$clientId
		);
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
