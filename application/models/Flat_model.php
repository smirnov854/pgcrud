<?php


class Flat_model extends CI_Model
{
    public $table_name = "flat";
    
    public function get_list($search_params  =[])
    {
        extract($search_params);
        if(!empty($limit)){
            $this->db->limit($limit);
        }
        if(!empty($offset)){
            $this->db->offset($offset);
        }
        if(!empty($name)){
            $this->db->where(" name LIKE '%$name%'",NULL,FALSE);
        }
        $query = $this->db->select("fl.*,count(*) OVER() AS total_count")
                          ->order_by("fl.id DESC")
                          ->get("$this->table_name fl");
        return $query->result();
    }     
    
    public function delete($id) {
        if(!$this->check_person($id)){
            throw new Exception("К данной квартире привязан жилец! Необходимо удалить жильца, затем квартиру!",300);
        }
        if(!$this->check_meter($id)){
            throw new Exception("К данной квартире привязан датчик! Необходимо удалить датчик, затем квартиру!",300);
        }
        return parent::delete($id); // TODO: Change the autogenerated stub
    }
    
    public function check_meter($flat_id){
        $res = $this->db->where("flat_id",$flat_id)->get("flat_meter");
        $result = $res->result();
        if(count($result)>0){
            return FALSE;
        }
        return TRUE;
    }

    public function check_person($flat_id){
        $res = $this->db->where("flat_id",$flat_id)->get("flat_person");
        $result = $res->result();
        if(count($result)>0){
            return FALSE;
        }
        return TRUE;
    }


}