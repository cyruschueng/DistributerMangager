<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityDb extends CI_Model
{
    var $tableName = "t_shop_commodity";

    public function __construct(){
        parent::__construct();
    }

    public function search($where, $limit){
        foreach($where as $key=>$value){
            if($key == 'title' || $key == 'introduction' ||
                $key == 'detail' || $key == 'detail')
                $this->db->like($key, $value);
            else if($key == 'userId' || $key == 'shopCommodityClassifyId')
                $this->db->where($key, $value);
        }
        $count = $this->db->count_all_results($this->tableName);

        foreach($where as $key=>$value){
            if($key == 'title' || $key == 'introduction' ||
                $key == 'detail' || $key == 'detail')
                $this->db->like($key, $value);
            else if($key == 'userId' || $key == 'shopCommodityClassifyId')
                $this->db->where($key, $value);
        }
        $this->db->order_by('sort', 'asc');  

        if(isset($limit['pageIndex']) && isset($limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        $query = $this->db->get($this->tableName)->result_array();
        return array(
            'count'=>$count,
            'data'=>$query
        );
    }

    public function get($shopCommodityId){
        $this->db->where("shopCommodityId", $shopCommodityId);    
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException(1, '不存在此商品');
        return $query[0];
    }

    public function del($shopCommodityId){
        $this->db->where("shopCommodityId", $shopCommodityId);
        $this->db->delete($this->tableName);
    }

    public function getMaxSortByUser($userId){
        $this->db->select_max('sort');    
        $this->db->where('userId', $userId);
        $result = $this->db->get($this->tableName)->result_array();
        return $result[0]['sort'];
    }

    public function add($data){
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($shopCommodityId, $data){
        $this->db->where('shopCommodityId', $shopCommodityId);
        $this->db->update($this->tableName, $data);
    }

    public function modWhenClassifyDel($shopCommodityClassifyId, $data){
        $this->db->where('shopCommodityClassifyId', $shopCommodityClassifyId);
        $this->db->update($this->tableName, $data);
    }
}
