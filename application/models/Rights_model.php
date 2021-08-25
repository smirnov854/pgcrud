<?php

class Rights_model extends CI_Model
{
    public $table_name = "rights";
    
    public function get_list($search_params = [])
    {        
        extract($search_params);       

        if(!empty($role_id)){
            $this->db->where("u.role_id",$role_id,FALSE);            
        }        
       
        
        $res = $this->db->select("r.*,   count(*) OVER() AS total_count",FALSE)                         
                         ->order_by('id DESC')
                         ->get("rights r", FALSE);      
              
        if (!$res) {
            return FALSE;
        }
        return $res->result();
    }

}