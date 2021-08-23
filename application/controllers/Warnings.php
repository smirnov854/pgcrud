<?php


class Warnings extends CI_Controller
{


    public function __construct() {
        parent::__construct();

        $this->load->model("user_model");
        $this->load->model("warning_model");
    }

    public function index() {
        $this->show_list();
    }
       
    public function show_list(){
        $this->load->view('includes/header');
        $this->load->view("includes/menu");
        $this->load->view("warning/warning_list");
        $this->load->view('includes/footer');
    }

    public function search($page){

        $user_data = $this->session->userdata();
        $params = json_decode(file_get_contents('php://input'));
        try {
            $search_params = [
                "limit"=>25,
                "offset"=>(!empty($page) ? ($page-1)*25:0)
            ];
            $meter_list = $this->warning_model->get_list($search_params);            
            if ($meter_list === FALSE) {
                throw new Exception("Ошибка обращения к БД!", 300);
            }
            $result = [
                "status" => 200,
                "content" => $meter_list,
                "total_rows"=>$meter_list[0]->total_count,
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }

    public function delete($id){
        try {
            if(empty($id) || !is_numeric($id)){
                throw new Exception("Ошибка получения id!",301);
            }
            if(!$this->warning_model->delete($id)){                
                throw new Exception('Ошибка удаления',302);
            }           
            $result = [
                "status" => 200,
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }
    
    
    public function generate_data(){       
        $insert_arr = [           
            "person_id"=>14,
            "flat_id"=>3704,
            "phone"=>"79999999999",
            "message"=>"Test message",
            "pickup"=>date("d.m.Y H:i:s",time()),
            "delivery"=>date("d.m.Y H:i:s",time()),
        ];
        for($i=0;$i<100;$i++){
            $this->db->insert("notify_person",$insert_arr);
            echo $this->db->last_query();
            echo "<br/>";
        }        
    }
}