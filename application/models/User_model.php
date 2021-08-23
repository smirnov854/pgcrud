<?php

class User_model extends CI_Model
{

    public function get_user_list($search_params = [])
    {
        $limit = 25;
        $offset = 0;
        extract($search_params);        
        $where = [];
                 

        if(!empty($role_id)){
            $this->db->where("u.role_id",$role_id,FALSE);            
        }
        
        if(!empty($fio)){
            $this->db->where("u.name LIKE '%$fio%'",NULL,FALSE);            
        }
        $res = $this->db->select("u.*,  r.id as role_id, r.name as role_name",FALSE)
                         ->join("role r","r.id=u.role_id","LEFT",FALSE)
                        ->get("user u", FALSE);      
              
        if (!$res) {
            return FALSE;
        }
        return $res->result();
    }


    function add_new_user($common_info)
    {

        if (empty($common_info)) {
            return FALSE;
        }
        $query = $this->db->insert("users", $common_info);
        if (!$query) {
            return FALSE;
        }
        return $this->db->insert_id();
    }

    function get_role_list()
    {
        $query = $this->db->get("role");
        return $query->result();
    }


    function get_by_id($id)
    {
        if (!$id) {
            return FALSE;
        }
        $query = $this->db->where("id", $id)
            ->get("users");
        if (!$query) {
            return FALSE;
        }
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


    public function edit_user($user_id, $data)
    {
        if (!$user_id) {
            return FALSE;
        }
        $query = $this->db->where("id", $user_id)
            ->update("users", $data);
        if (!$query) {
            return FALSE;
        }
        return TRUE;
    }
  
    public function set_delete($id){        
        if(empty($id) || !is_numeric($id)){
            $res = FALSE;
        }else{
            $res = $this->db->where("id",$id)->update("users",["is_delete"=>1]);    
        }
        return $res;
    }

}