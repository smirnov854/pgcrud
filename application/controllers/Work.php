<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Work extends CI_Controller
{
    public $menu_values = [];
    public $max_size = 10048577;
    public $name = "";
    public $tmp_name = "";
    public $size = 0;
    public $req_id = 0;
    public $path_real = "uploads";
    public $res_file_name = "";
    public $allow_ext = [
        "pdf","gif", "jpg","png", "zip","rar"
    ];
    
    
    public function __construct() {
        parent::__construct();
        $user_data = $this->session->userdata("user_data");
        $this->load->model("user_model");
        $this->load->model("work_model");
        $this->load->model("object_model");
        $this->load->model("mail_model");
    }

    public function index() {
        $this->show_work_table();
    }


    public function add_new_job() {
        $params = json_decode(file_get_contents('php://input'));
        $user_data = $this->session->userdata();
        $common_info = array(
            "type_id" => $params->type,
            "description" => $params->description,
            "object_id" => $params->object_id,
            "date_add" => time(),
            "id_user_add" => $user_data['id'],
            "user_done_date"=>(!empty($params->date_done) ? strtotime($params->date_done) : '')
        );
        if(!empty($params->date_done)){
            $common_info['id_user_done'] = $user_data['id'];
        }
        try {
            if (empty($common_info['type_id'])) {
                throw new Exception("Ошибка заполнения формы!", 300);
            }
            $type = 'client';
            $subject = "";
            $body = "";
            switch($user_data['role_id']){
                case 2:
                    $type = 'client';
                    $subject = "Добавлена новая задача";
                    $body = "По объекту добавлена новая задача<br/>Для просмотра перейдите по <a href='".$_SERVER['SERVER_NAME']."'>ссылке</a>";
                    break;
                case 3:
                    $type = 'engineer';
                    $subject = "Добавлена новая задача";
                    $body = "По объекту добавлена новая задача<br/>Для просмотра перейдите по <a href='".$_SERVER['SERVER_NAME']."'>ссылке</a>";
                    break;
                case 4:
                    $type = 'worker';
                    $subject = "Добавлена новая задача";
                    $body = "По объекту добавлена новая задача<br/>Для просмотра перейдите по <a href='".$_SERVER['SERVER_NAME']."'>ссылке</a>";
                    break;
            }
            $res = $this->work_model->add_new_request($common_info);
            if (!$res) {
                throw new Exception("Ошибка обращения к базе данных!", 2);
            }
            $user_to_send_mail = $this->work_model->get_user($res, $type);
            
            if(!empty($user_to_send_mail)){                
                foreach($user_to_send_mail as $row){                    
                    $email_send = $this->mail_model->send($row,$subject,$body);
                    if(empty($email_send)){
                        throw new Exception("Задача добавлена! Ошибка отправки почты!",250);
                    }
                }
            }
            $result = [
                "status" => 200,
                "message" => "Заявка добавлена!",
                "request_id"=>$res
            ];
        } catch (Exception $ex) {
            $result = array(
                "message" => $ex->getMessage(),
                "status" => $ex->getCode());
            if($ex->getCode() == 250){
                $result['request_id'] = $res;
            }

        }
        echo json_encode($result);
    }
    
    public function upload_file($request_id){
        try {
            if (empty($request_id)) {
                throw new Exception("Ошибка получения номера заявки!", 300);
            }
            $files = [];
            foreach ($_FILES as $index=>$file) {
                $this->name = $_FILES[$index]['name'];
                $this->tmp_name = $_FILES[$index]['tmp_name'];
                $this->size = $_FILES[$index]['size'];
                $this->req_id = $request_id;                
                if(!$this->check_size()){
                    throw new Exception("Один из файлов превышает допустимый размер в 10 МБ",3);
                }
                $dir_name = 'uploads/' . $request_id;
                if(!is_dir($dir_name)){
                    if (!mkdir($dir_name)) {
                        throw new Exception("Ошибка добавления директории! Обратитесь к администратору!", 2);
                    }
                }
                if(!$this->do_upload()){
                    throw new Exception("Ошибка загрузки файла!",3);
                }
                $res = $this->work_model->add_connection($this->req_id,$this->path_real);
                $this->path_real= "uploads";
                if(empty($res)){
                    throw new Exception("Ошибка добавления связи!",3);
                }
            }            
            
            $result = [
                "status" => 200,
                "message" => "Заявка добавлена! Файлы загружены!"
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }

    public function check_size() {
        if ($this->size > $this->max_size) {
            return FALSE;
        }
        return TRUE;
    }

    public function do_upload() {
        $name_exploded = explode(".", $this->name);
        $filename_tmp = md5($this->name);
        if (move_uploaded_file($this->tmp_name, $this->path_real."/".$this->req_id."/" . $filename_tmp . "." . $name_exploded[1])) {
            $this->res_file_name = $filename_tmp . "." . $name_exploded[1];
            $this->path_real = $this->path_real."/".$this->req_id ."/". $filename_tmp . "." . $name_exploded[1];
            return TRUE;
        }
        return FALSE;
    }

    public function show_work_table() {        
        $user_data = $this->session->userdata();
        $type_list = $this->work_model->get_type_list();
        $search_params = [
            "role_id" => $user_data['role_id'],
            "user_id" => $user_data['id'],
            "limit"=>25,
            "offset"=>0
        ];
       
        $object_list = $this->object_model->get_list($search_params,FALSE);
        $request_list = $this->work_model->get_list($search_params);

        $total_rows = $this->db->query("SELECT FOUND_ROWS() as cnt")->result();


        $this->load->view('includes/header');
        $this->load->view("includes/menu");
        $this->load->view("work/work_list", [
            "req_list" => $request_list,
            "type" => $type_list,
            "objects" => $object_list,
            "role_id" => $user_data['role_id'],
            "total_rows"=>$total_rows[0]->cnt
            
        ]);
        $this->load->view('includes/footer');
    }
    


    public function search($page) {
        $user_data = $this->session->userdata();
        $params = json_decode(file_get_contents('php://input'));
        try {
            $search_params = [
                "status"=>$params->current_status,
                "objects" => $params->objects_id,
                "date_from" => $params->date_from,
                "date_to" => $params->date_to,
                "role_id" => $user_data['role_id'],
                "user_id" => $user_data['id'],
                "limit"=>25,
                "offset"=>(!empty($page) ? ($page-1)*25:0)
            ];
            $requests = $this->work_model->get_list($search_params);
            if ($requests === FALSE) {
                throw new Exception("Ошибка обращения к БД!", 300);
            }
            $total_rows = $this->db->query("SELECT FOUND_ROWS() as cnt")->result();
            $total_pages = ceil($total_rows[0]->cnt/25);
            //var_dump($total_rows);
            if($total_rows <= 25){
                $pages = [];
            }else{
                $pages = [];
                for($i=1;$i<=$total_pages;$i++){
                    $pages[] = $i;
                }
                //$pages = implode(",",$pages);
            }

            $result = [
                "status" => 200,
                "content" => $requests,
                "total_rows"=>$total_rows[0]->cnt,
                "pages"=>$pages,
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }
    
    public function save_worker_comment($id){
        $params = json_decode(file_get_contents('php://input'));
        $user_data = $this->session->userdata();
        try {
            if(empty($id) || !is_numeric($id)){
                throw new Exception("Ошибка получения номера заявки!", 300);
            }
            $update_param = [
                "done_work"=>$params->comment,
                "id_user_done"=>$user_data['id'],
                "user_done_date"=>time(),
            ];
            $res = $this->work_model->update_by_id($id,$update_param);
            if ($res === FALSE) {
                throw new Exception("Ошибка обращения к БД!", 300);
            } 
            $search_params = [                
                "role_id" => $user_data['role_id'],
                "user_id" => $user_data['id'],
                'id'=>$id,
                "limit"=>1,
                "offset"=>0
            ];
            $res = $this->work_model->get_list($search_params);
            $result = [
                "status" => 200,    
                "content"=>$res[0],
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }
    
    public function update_check_date($id,$type){
        $user_data = $this->session->userdata();
        try {
            if(empty($id) || !is_numeric($id)){
                throw new Exception("Ошибка получения номера заявки!", 300);
            }            
            $tmp_date = time();
            switch($type){
                case 'user_check_date':                    
                    $update_param = [
                        "id_user_check"=>$user_data['id'],
                        "user_check_date"=>$tmp_date,
                    ];
                    break;
                case 'common_date':
                    $update_param = [
                        "id_user_common_check"=>$user_data['id'],
                        "common_date"=>$tmp_date,
                    ];
                    break;
            }
            
            $res = $this->work_model->update_by_id($id,$update_param);
            if ($res === FALSE) {
                throw new Exception("Ошибка обращения к БД!", 300);
            }
            $result = [
                "status" => 200,
                "content"=>date(date("d.m.Y H:i",$tmp_date))
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }
    
    public function show_type_of_works(){
        $list = $this->work_model->get_type_list();
        $this->load->view('includes/header');
        $this->load->view("includes/menu");        
        $this->load->view("work/work_type", [
            "list" => $list
        ]);
        $this->load->view('includes/footer');
    }
    
    public function add_new_work_type($id){
        $params = json_decode(file_get_contents('php://input'));
        $common_info = array(
            "name" => $params->name,           
        );
        try {
            if (empty($common_info['name'])){
                throw new Exception("Ошибка заполнения формы!", 300);
            }
            if(!empty($id)){
                if(!is_numeric($id)){
                    throw new Exception("Ошибка получени id!", 300);
                }
                $res = $this->work_model->edit_work_type($id,$common_info);
                if (!$res) {
                    throw new Exception("Ошибка обращения к базе данных!", 2);
                }
            }else{                
                $res = $this->work_model->add_new_work_type($common_info);
                if (!$res) {
                    throw new Exception("Ошибка обращения к базе данных!", 2);
                }
            }

            $result = [
                "status" => 200,
                "message" => "Объект добавлен!"
            ];
        } catch (Exception $ex) {
            $result = array("message" => $ex->getMessage(),
                "status" => $ex->getCode());
        }
        echo json_encode($result);
    }
    
    public function set_delete_work_type($id){

        try {
            if(empty($id) || !is_numeric($id)){
                throw new Exception("Ошибка получения id!",301);
            }
            if(!$this->work_model->edit_work_type($id,['is_delete'=>1])){
                throw new Exception('Ошибка простановки статуса Удален',302);
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
    
    public function generate_data() {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        for ($i = 0; $i < 10000; $i++) {
            $user_id = rand(1, 50);
            $query = $this->db->select("object_id")
                ->where('user_id', $user_id)
                ->get("user_object");
            $res = $query->result();
            $length = count($res);
            $insert_array = [
                "type_id" => rand(1, 3),
                "id_user_add" => $user_id,
                "object_id" => $res[rand(1, $length - 1)]->object_id,
                "description" => substr(str_shuffle($permitted_chars), 0, 10) . " " . substr(str_shuffle($permitted_chars), 0, 15),
                "date_add" => strtotime(date("d.m.Y H:i:s", rand(strtotime("01.01.2021"), strtotime("31.04.2021")))),
            ];
            $this->db->insert("requests", $insert_array);
        }
    }


}
