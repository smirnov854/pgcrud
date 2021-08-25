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
       
        
        $res = $this->db->select("r.*, string_agg(rr.right_id::character varying,',' order by rr.id) as rights_id,  count(*) OVER() AS total_count",FALSE)       
                         ->join("role_rights rr","rr.role_id=r.id","LEFT")    
                         ->order_by('r.id DESC')
                         ->group_by('r.id')
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
    
    function get_rights_list()
    {
        $query = $this->db->get("rights");
        return $query->result();
    }
    
    public function add_connection($role_id,$rights){
        try {
            if(empty($role_id)){
                throw new Exception("Ошибка",1);
            }
            if(empty($rights) || !is_array($rights)){
                throw new Exception("Ошибка",1);
            }
            $this->db->where("role_id",$role_id)->delete("role_rights");
            foreach($rights as $right_id){                
                $insert_array = [
                    "role_id"=>$role_id,
                    "right_id"=>$right_id
                ];
                $this->db->insert("role_rights",$insert_array,FALSE);
            }
            $result = true;
        } catch (Exception $ex) {
            $result = false;
        }
        return $result;
    }

}