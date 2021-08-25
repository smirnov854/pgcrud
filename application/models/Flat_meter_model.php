<?php

class Flat_meter_model extends CI_Model
{
    public $table_name = "flat_meter";
    
    public function get_list($search_params  =[])
    {
        extract($search_params);
        if(!empty($limit)){
            $this->db->limit($limit);
        }
        
        if(!empty($offset)){
            $this->db->offset($offset);
        }
        
        if(!empty($meter_type_id)){
            $this->db->where("meter_type_id IN (".implode(",",$meter_type_id).")",NULL,FALSE);
        }
        
        if(!empty($flat_id)){
            $this->db->where("flat_id",$flat_id,FALSE);
        }

        if(!empty($flat_name)){
            $this->db->where("flat.name LIKE '$flat_name'",NULL,FALSE);
        }

        if(!empty($flat_meter_value)){
            $this->db->where("fl.value$flat_meter_value",NULL,FALSE);
        }

        if(!empty($port_number)){
            $this->db->where("fl.port",$port_number,FALSE);
        }

        if(!empty($tube)){
            $this->db->where("tube LIKE '$tube'",NULL,FALSE);
        }

        if(!empty($date_from)){
            $this->db->where("fl.stamp>='$date_from'::date",NULL,FALSE);
        }

        if(!empty($date_to)){
            $this->db->where("fl.stamp<='$date_to'::date",NULLL,FALSE);
        }

        if(!empty($port_number)){
            $this->db->where("fl.port",$port_number,FALSE);
        }
        
        $query = $this->db->select("fl.*, 
                                    mt.name as meter_name,
                                    flat.name as flat_name,
                                    acc.deveui as deveui, 
                                    count(*) OVER() AS total_count")
                          ->join("meter_type mt","mt.id=fl.meter_type_id","LEFT")
                          ->join("flat","flat.id=fl.flat_id","LEFT")
                          ->join("acc","acc.id=fl.acc_id","LEFT")
                          ->order_by("fl.id DESC")
                          ->get("flat_meter fl");
        
        return $query->result();        
    }
    
    public function get_types(){
        $res = $this->db->get("meter_type");
        return $res->result();
    }
    
    public function add($common_info) {
        if(!empty($common_info['acc_id'])){
            if(!$this->is_acc_exists($common_info['acc_id'])){
                throw new Exception("Датчик с указанным ID не существует",300);
            }
        }
        
        return parent::add($common_info); // TODO: Change the autogenerated stub 
    }
    public function is_acc_exists($id){
        $res = $this->db->where("id",$id)->get("acc");
        $result = $res->result();
        if(count($result) == 0 ){
            return FALSE;
        }
        return TRUE;
    }
}