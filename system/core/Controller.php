<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;
	public $model_name;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
        
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}
        
		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
        
        if($this->uri->segment(1) != "login"){
            if(empty($this->session->userdata("role_id"))){
                redirect("login");
            }
            $user_data = $this->session->userdata();
            if($user_data['role_id'] != 1){
                if(!$this->check_rights($user_data['role_id'],$this->uri->segment(1),$this->uri->segment(2))){
                    if(stripos($_SERVER['HTTP_ACCEPT'],"json") !== FALSE){
                        echo json_encode(["status"=>325,"message"=>"У вас нет прав для данного действия"]);
                        die();
                    }else{
                        redirect("login/no_rights");
                    }
                }    
            }
        }        
		log_message('info', 'Controller Class Initialized');
	}
	
	public function check_rights($role_id,$controller,$method){
	    $res = $this->db->select("id")
                        ->where("controller",$controller)
                        ->where("method",$method)
                        ->get("rights");
	    $result = $res->result();
	    if(count($result) == 0){
	        return TRUE;
        }
	    $res = $this->db->join("rights","rights.id=role_rights.right_id","LEFT")
                        ->where("controller",$controller)
                        ->where("method",$method)
                        ->where("role_id",$role_id)                 
                        ->get("role_rights");
	    
	    $result = $res->result();
	    if(count($result) > 0){
	        return TRUE;
        }
	    return FALSE;
    }

	// --------------------------------------------------------------------

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}

}
