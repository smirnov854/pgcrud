<?php


class Flat extends CI_Controller
{
    
    public function __construct() {        
        parent::__construct();
        $this->load->model("user_model");
        $this->load->model("flat_model");
    }

    public function index() {
        $this->show_list();
    }

    public function show_list(){
        $this->load->view('includes/header');
        $this->load->view("includes/menu");
        $this->load->view("flat/flat_list");
        $this->load->view('includes/footer');
    }

    public function search($page){
        $params = json_decode(file_get_contents('php://input'));
        try {
            $search_params = [
                "name"=>$params->name,
                "limit"=>25,
                "offset"=>(!empty($page) ? ($page-1)*25:0)
            ];
            $list = $this->flat_model->get_list($search_params);
            if ($list === FALSE) {
                throw new Exception("Ошибка обращения к БД!", 300);
            }
            $result = [
                "status" => 200,
                "content" => $list,
                "total_rows"=>$list[0]->total_count,
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }
    
    public function add_new_flat(){
        $params = json_decode(file_get_contents('php://input'));
        $common_info = array(            
            "name" => $params->name,            
        );

        try {
            if (empty($common_info['name'])){
                throw new Exception("Ошибка заполнения формы!", 300);
            }           
            $res = $this->flat_model->add($common_info);
            if (!$res) {
                throw new Exception("Ошибка обращения к базе данных!", 2);
            }
            $result = [
                "status" => 200,
                "message" => "Квартира добавлена!",
                "content"=>[]
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }
    
    public function edit_flat($id){
        $params = json_decode(file_get_contents('php://input'));
        $common_info = array(
            "name" => $params->name,
        );

        try {
            if (empty($common_info['name'])){
                throw new Exception("Ошибка заполнения формы!", 300);
            }
            $res = $this->flat_model->edit($id,$common_info);
            if (!$res) {
                throw new Exception("Ошибка обращения к базе данных!", 2);
            }
            $result = [
                "status" => 200,
                "message" => "Квартира отредактирована!",
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
            if(!$this->flat_model->delete($id)){
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