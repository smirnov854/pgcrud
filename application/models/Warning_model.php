<?php

class Warning_model extends CI_Model
{
    public $table_name = "notify_person";
    
    public function get_list($search_params  =[])
    {
        extract($search_params);
        if(!empty($limit)){
            $this->db->limit($limit);
        }
        
        if(!empty($offset)){
            $this->db->offset($offset);
        }
        
        $query = $this->db->select("np.*, flat.name as flat_name, flat_person.name as person_name,count(*) OVER() AS total_count")
                          ->join("flat_person","flat_person.id=np.person_id","LEFT")
                          ->join("flat","flat.id=np.flat_id","LEFT")
                          ->order_by("np.id DESC")
                          ->get("notify_person np");
        
        return $query->result();        
    }
    
  
}