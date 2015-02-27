<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderAo extends CI_Model 
{

	public function __construct(){
		parent::__construct();
		$this->load->model('shop/commodityAo','commodityAo');
		$this->load->model('shop/trollerAo','trollerAo');
		$this->load->model('address/addressAo','addressAo');
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('order/orderPayAo','orderPayAo');
		$this->load->model('order/orderDb','orderDb');
		$this->load->model('order/orderCommodityDb','orderCommodityDb');
		$this->load->model('order/orderAddressDb','orderAddressDb');
		$this->load->model('order/orderStateEnum','orderStateEnum');
		$this->load->model('address/addressPayMentEnum','addressPayMentEnum');
	}

	private function addMyOrder($userId,$clientId,$shopCommodity,$address){
		//计算出订单基本信息
		$orderPrice = array_reduce($shopCommodity,function($sum,$single){
			return $sum + $single['price']*$single['quantity'];
		},0);
		$orderProducts = array_map(function($shopCommodity){
			return $shopCommodity['title'];
		},$shopCommodity);
		$orderNum = count($shopCommodity);
		$orderDesc = $address['name'].'购买的"'.implode('","', $orderProducts).'"';
		if($address['payment'] == $this->addressPayMentEnum->CODPAY)
			$orderState = $this->orderStateEnum->NO_SEND;
		else
			$orderState = $this->orderStateEnum->NO_PAY;

		//添加订单基本信息
		$shopOrderId = date('YmdHis').$clientId.rand(10000,99999);
		$orderInfo = array(
			'shopOrderId'=>$shopOrderId,
			'userId'=>$userId,
			'clientId'=>$clientId,
			'image'=>$shopCommodity[0]['icon'],
			'price'=>$orderPrice,
			'num'=>$orderNum,
			'name'=>$address['name'],
			'description'=>$orderDesc,
			'wxprePayId'=>0,
			'state'=>$orderState
		);
		$this->orderDb->add($orderInfo);

		//添加订单商品信息
		$this->orderCommodityDb->addBatch(array_map(function($singleShopCommodity)use($shopOrderId){
			return array(
				'shopOrderId'=>$shopOrderId,
				'shopCommodityId'=>$singleShopCommodity['shopCommodityId'],
				'userId'=>$singleShopCommodity['userId'],
				'title'=>$singleShopCommodity['title'],
				'icon'=>$singleShopCommodity['icon'],
				'introduction'=>$singleShopCommodity['introduction'],
				'price'=>$singleShopCommodity['price'],
				'oldPrice'=>$singleShopCommodity['oldPrice'],
				'quantity'=>$singleShopCommodity['quantity'],
			);
		},$shopCommodity));

		//添加订单地址信息
    	$this->orderAddressDb->add(array(
			'shopOrderId'=>$shopOrderId,
			'name'=>$address['name'],
			'province'=>$address['province'],
			'city'=>$address['city'],
			'address'=>$address['address'],
			'phone'=>$address['phone'],
			'payment'=>$address['payment'],
		));

    	return $orderInfo;
	}

	private function addWxOrder($userId,$clientId,$orderInfo){
		$wxOrderInfo = $this->orderPayAo->wxPay(
			$userId,
			$clientId,
			$orderInfo['shopOrderId'],
			$orderInfo['description'],
			$orderInfo['price']
		);

		$this->orderDb->mod(
			$orderInfo['shopOrderId'],
			array('wxPrePayId'=>$wxOrderInfo['prepay_id'])
		);
	}

	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
        $data = $this->orderDb->search($dataWhere,$dataLimit);
        foreach($data['data'] as $key=>$value ){
            $data['data'][$key]['priceShow'] = $this->commodityAo->getFixedPrice($data['data'][$key]['price']);
        }
        return $data;
	}

	public function getClientOrder($userId,$clientId){
		$orderNum = $this->orderDb->getCountByUserIdAndClientId($userId,$clientId);
		$result = array();

		foreach($orderNum as $single){
			$result[$single['state']] = intval($single['count']);
		}

		foreach($this->orderStateEnum->names as $key=>$value){
			if(isset($result[$key]) == false )
				$result[$key] = 0;
		}

		$result[0] = array_reduce($result,function($sum,$single){
			return $sum+$single;
		},0);
		return $result;
	}
	public function getClientOrderDetail($userId,$clientId,$state){
		$dataWhere = array('clientId'=>$clientId);
		if( $state != 0 )
			$dataWhere['state'] = $state;
		return $this->search($userId,$dataWhere,array())['data'];
	}

	public function get($userId,$shopOrderId){
		$shopOrder = $this->orderDb->get($shopOrderId);
		if($shopOrder['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无阅读该订单权限');

		$shopOrder['priceShow'] = $this->commodityAo->getFixedPrice($shopOrder['price']);

		$shopOrder['address'] = $this->orderAddressDb->getByShopOrderId($shopOrderId)[0];

		$shopOrder['commodity'] = $this->orderCommodityDb->getByShopOrderId($shopOrderId);
		foreach($shopOrder['commodity'] as $key=>$value){
			$shopOrder['commodity'][$key]['priceShow'] = $this->commodityAo->getFixedPrice(
				$shopOrder['commodity'][$key]['price']
			);
		}
		return $shopOrder;
	}

	public function add($userId,$clientId,$shopTrollerId,$address){
		//获取购物车内的商品信息
		$shopTroller = $this->trollerAo->getByIds($userId,$clientId,$shopTrollerId);

		//校验购物车内的商品信息
		foreach($shopTroller as $singleShopTroller){
			$this->trollerAo->check($singleShopTroller);
		}

		//校验地址信息
		$this->addressAo->check($address);
		
		//校验商品信息
		if( count($shopTroller) == 0 )
			throw new CI_MyException(1,'订单内的商品不能为空');
		foreach($shopTroller as $singleShopTroller)
			if($singleShopTroller['quantity'] <= 0 )
				throw new CI_MyException(1,'选择的商品数量不能为0');

		//扣库存
		foreach($shopTroller as $singleShopTroller ){
			$this->commodityAo->reduceStock($singleShopTroller['userId'],$singleShopTroller['shopCommodityId'],$singleShopTroller['quantity']);
		}

		//下单
		$orderInfo = $this->addMyOrder($userId,$clientId,$shopTroller,$address);

		//微信统一下单
		$this->addWxOrder($userId,$clientId,$orderInfo);

		//删购物车
		$this->trollerAo->delByIds($userId,$clientId,$shopTrollerId);

		return $orderInfo['shopOrderId'];
	}

	public function wxJsPay($userId,$clientId,$shopOrderId){
		$orderInfo = $this->get($userId,$shopOrderId);

		return $this->orderPayAo->wxJsPay($userId,$orderInfo['wxPrePayId']);
	}

	public function modHasSend( $userId,$shopOrderId ){
        $shopOrder = $this->orderDb->get($shopOrderId);
        if($shopOrder['userId'] != $userId)
        	throw new CI_MyException(1, "非本商城用户无阅读该订单权限");

        if($shopOrder['state'] == $this->orderStateEnum->NO_PAY)
         	throw new CI_MyException(1, "该订单未支付，不能扭转为已发货状态");

        $this->orderDb->mod($shopOrderId, array('state'=>$this->orderStateEnum->HAS_SEND));
	}
}