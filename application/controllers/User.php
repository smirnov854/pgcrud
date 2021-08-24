<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public $menu_values = [];

    public function __construct() {
        parent::__construct();
        $user_data = $this->session->userdata("user_data");
        $this->load->model("user_model");
    }

    public function index() {
        $this->show_login();
    }


    public function login() {
        try {
            $data = [
                "login" => $this->input->post("user_name"),
                "password" => $this->input->post("user_password"),
            ];
            //$password = password_hash("admin", PASSWORD_BCRYPT);
            //$query = $this->db->insert("users",['email'=>"admin@admin.com","user_password"=>$password]);
            if (empty($data['login']) || empty($data['password'])) {
                throw new Exception("Необходимо заполнить поля логин и пароль!", 1);
            }
            //$data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);            
            $user = $this->user_model->login($data);
            if (empty($user)) {
                throw new Exception("Пользователя с таким данными не существует!", 1);
            }
            $user = reset($user);

            $session_array = [
                "id" => $user->id,
                "user_name" => $user->user_name,
                "user_email" => $user->email,
                "role_id" => $user->role_id,
            ];
            $this->session->set_userdata($session_array);

            $result = array("status" => 0,
                "message" => "Добро пожаловать"
            );
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }

    public function show_users() {
        $this->load->view('includes/header');
        $this->load->view("includes/menu");
        $this->load->view("user/user_list");
        $this->load->view('includes/footer');
    }

    public function search($page = 0) {

        $params = json_decode(file_get_contents('php://input'));
        try {
            $search_params = [
                "name" => $params->name,      
                "role_id"=>$params->role_id,
                "limit" => 25,
                "offset" => (!empty($page) ? ($page - 1) * 25 : 0)
            ];
            $list = $this->user_model->get_list($search_params);
            if ($list === FALSE) {
                throw new Exception("Ошибка обращения к БД!", 300);
            }
            $result = [
                "status" => 200,
                "content" => $list,
                "total_rows" => $list[0]->total_count,
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }

    public function get_role_list() {
        $meter_type_list = $this->user_model->get_role_list();
        $result = [
            "status" => 200,
            "contents" => $meter_type_list,
        ];
        echo json_encode($result);
    }

    public function add_new_user() {
        $params = json_decode(file_get_contents('php://input'));
        $common_info = array(           
            "name" => $params->name,
            "login" => $params->login,
            "role_id" => $params->role_id,
            'password'=>$params->password,
        );

        try {
            if (empty($common_info['login']) || empty($common_info['name']) || empty($common_info['role_id']) || empty($common_info['password'])){
                throw new Exception("Ошибка заполнения формы!", 300);
            }           
            $res = $this->user_model->add($common_info);
            if (!$res) {
                throw new Exception("Ошибка обращения к базе данных!", 2);
            }
            $result = [
                "status" => 200,
                "message" => "Пользователь добавлен!",
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
            "name" => $params->name,
            "login" => $params->login,
            "role_id" => $params->role_id,
            'password'=>$params->password,
        );
        try {
            if (empty($common_info['login']) || empty($common_info['name']) || empty($common_info['role_id']) || empty($common_info['password'])){
                throw new Exception("Ошибка заполнения формы!", 300);
            }         
            $res = $this->user_model->edit($user_id,$common_info);
            if (!$res) {
                throw new Exception("Ошибка обращения к базе данных!", 2);
            }
            $result = [
                "status" => 200,
                "message" => "Пользователь отредактирован!",
                "content"=>[]
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }
}
