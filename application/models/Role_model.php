<?php

class Role_model extends CI_Model
{
    public $table_name = "role";
    
    public function get_list($search_params = [])
    {        
        extract($search_params);       

        if(!empty($role_id)){
            $this->db->where("u.role_id",$role_id,FALSE);            
        }        
       
        
        $res = $this->db->select("r.*,   count(*) OVER() AS total_count",FALSE)                         
                         ->order_by('id DESC')
                         ->get("role r", FALSE);      
              
        if (!$res) {
            return FALSE;
        }
        return $res->result();
    }

    function get_role_list()
    {
        $query = $this->db->get("role");
        return $query->result();
    }

    function login($data)
    {
        if (empty($data)) {
            return FALSE;
        }
        $query = $this->db->select("u.*, r.name as role_name",FALSE)
            ->where("login", $data['login'])
            ->join("role r","r.id=u.role_id","LEFT",FALSE)
            ->get("user u");      
        if ($query->num_rows() != 1) {
            return FALSE;
        }       
        $query_result = $query->result();       
        if ($data['password'] != $query_result[0]->password) {
            return FALSE;
        }
        return $query_result;
    }
}