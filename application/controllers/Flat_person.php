<?php


class Flat_person extends CI_Controller
{
    public function __construct() {
        parent::__construct();

        $this->load->model("user_model");
        $this->load->model("flat_person_model");
    }

    public function index() {
        $this->show_list();
    }

    public function show_list(){
        $this->load->view('includes/header');
        $this->load->view("includes/menu");
        $this->load->view("flat_person/flat_person_list");
        $this->load->view('includes/footer');
    }

    public function search($page){
        
        $params = json_decode(file_get_contents('php://input'));        
        try {
            $search_params = [
                "name"=>$params->name,
                "phone"=>$params->phone,
                "flat_id"=>$params->flat_id,
                "flat_name"=>$params->flat_name,                
                "limit"=>25,
                "offset"=>(!empty($page) ? ($page-1)*25:0)
            ];
            $list = $this->flat_person_model->get_list($search_params);
            if ($list === FALSE) {
                throw new Exception("Ошибка обращения к БД!", 300);
            }
            $cnt = count($list);
            $total_rows = 0;
            if($cnt>0){
                $total_rows = $list[0]->total_count;
            }
            $result = [
                "status" => 200,
                "content" => $list,
                "total_rows"=>$total_rows,
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }

    public function add_new_user() {                
        $params = json_decode(file_get_contents('php://input'));
        $common_info = array(
            "email"=>$params->email,
            "name" => $params->name,
            'phone'=>$params->phone,
            'flat_id'=>$params->flat_id,
            'password'=>$params->password,
        );        
        
        try {
            if (empty($common_info['email']) || empty($common_info['name']) || empty($common_info['phone'])){
                throw new Exception("Ошибка заполнения формы!", 300);
            }
            if(!$this->flat_person_model->check_phone($common_info['phone'])){
                throw new Exception("Телефон ранее испольовался!", 300);
            }
            $res = $this->flat_person_model->add($common_info);
            if (!$res) {
                throw new Exception("Ошибка обращения к базе данных!", 2);
            }   
            $result = [
                "status" => 200,
                "message" => "Жилец добавлен!",
                "content"=>[]
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }

    public function edit_user($user_id) {
        $params = json_decode(file_get_contents('php://input'));        
        $common_info = array(            
            "email"=>$params->email,
            "name" => $params->name,
            'phone'=>$params->phone,
            'flat_id'=>$params->flat_id,
            'password'=>$params->password,
        );

        try {
            if (empty($user_id) || empty($common_info['email']) || empty($common_info['name']) || empty($common_info['phone'])){
                throw new Exception("Ошибка заполнения формы!", 300);
            }
            if(!$this->flat_person_model->check_phone($common_info['phone'],$user_id)){
                throw new Exception("Телефон ранее испольовался!", 300);
            }            
            $res = $this->flat_person_model->edit($user_id,$common_info);
            if (!$res) {
                throw new Exception("Ошибка обращения к базе данных!", 2);
            }
            $result = [
                "status" => 200,
                "message" => "Жилец отредактирован!",
                "content"=>[]
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
            if(!$this->flat_person_model->delete($id)){
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
}