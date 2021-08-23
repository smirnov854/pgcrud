<?php


class Flat_person_model extends CI_Model
{

    public $table_name = "flat_person";

    public function get_list($search_params  =[])
    {
        extract($search_params);
        if(!empty($limit)){
            $this->db->limit($limit);
        }
        if(!empty($offset)){
            $this->db->offset($offset);
        }
        if(!empty($phone)){
            $this->db->where("phone",$phone);
        }
        if(!empty($name)){
            $this->db->where("name",$name);
        }
        $query = $this->db->select("fl.*,flat.name as flat_name,count(*) OVER() AS total_count")
                          ->join("flat","flat.id=fl.flat_id","LEFT")
                          ->order_by("fl.id DESC")
                          ->get("flat_person fl");        
        
        return $query->result();
    }
    
    public function check_phone($phone,$user_id=0){
        if(empty($phone)){
            return FALSE;
        }        
        if(!empty($user_id)){
            $this->db->where("id<>$user_id",NULL,FALSE);
        }
        $query = $this->db->where("phone",$phone)
                        ->get("flat_person");
        $res = $query->result();
        if(count($res) >0){
            return FALSE;
        }
        return TRUE;
    }
    
}