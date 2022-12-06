<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_Controller extends CI_Controller {

        public function __construct()
	{
		parent::__construct();
                $this->load->model('usermodel');
	}
        
        public function index()
	{
		
	}
        
        public function login(){
            $data = array('success'=>false);
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_error_delimiters('<p class="error_show">','</p>');
            if($this->form_validation->run()) {
                    // If Validation True
                $data['success'] = true; //Status Success True
                
                $emailid = $this->input->post('email',TRUE);
                $pass = $this->input->post('password',TRUE);
                
                $logindata = array(
                            'email' => $emailid,
                            'password' => $pass,
                        );
                $logeddinresult = $this->usermodel->login($logindata);
                if(!empty($logeddinresult)){
//                   http_response_code(200);
                   $token_key = $this->token($logeddinresult);
                   header("Authorization:'.$token_key.'");
                   $data['authtoken']=$token_key;
                   $data['messages'] = 'User logged in successfully'; //Message
                }else{
//                   http_response_code(401);
                   $data['messages'] = 'Please check email or password'; //Message 
                }
            }else{
                $dataPost=$this->input->post();
                foreach ($dataPost as $key => $values){
                    $data['messages'][$key]=form_error($key);
                }
            }
            
            echo json_encode($data); 
        }
        
        public function registration(){
            
            $data = array('success'=>false);
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[6]|max_length[50]|valid_email|is_unique[user_tbl.email]');
            $this->form_validation->set_rules('password', 'Password','trim|required|min_length[8]|max_length[255]',array('required' => 'You must provide a %s.'));
            
            $this->form_validation->set_error_delimiters('<p class="error_show">','</p>');
            if($this->form_validation->run()) {
                // If Validation True
                $data['success'] = true; //Message Success True
                
                $name=$this->input->post('name');
                $email=$this->input->post('email');
                $password=$this->input->post('password');
                
                $formdata = array(
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password,PASSWORD_DEFAULT),
                'updated_at'=>date('Y-m-d H:i:s', time()),
                );
                
                $query = $this->usermodel->registration($formdata);
                if(!empty($query)){
                    $data['user_data'] = $query; 
                    $data['authtoken']=$this->token($query);
                }
            }else{
                $dataPost=$this->input->post();
                foreach ($dataPost as $key => $values){
                    $data['messages'][$key]=form_error($key);
                }
            }
            
            echo json_encode($data); 
        }
        
        public function token($data){
            $jwt = new JWT();
            $JwtSecreteKey = 'AssiengmentSecretKey';
            $token = $jwt->encode($data,$JwtSecreteKey,'HS256');
            return $token;
        }
        
        public function validate(){
            $jwt = new JWT();
            $token_key = $this->get_headertoken();
            
            $JwtSecreteKey = 'AssiengmentSecretKey';
            $tokenValidate = $jwt->decode($token_key,$JwtSecreteKey,'HS256');
            $data['status']='Success';
            $data['Userdata']=$tokenValidate;
            //This will return json
            echo  json_encode($data);
        }
        
        function get_headertoken(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
        }
}
