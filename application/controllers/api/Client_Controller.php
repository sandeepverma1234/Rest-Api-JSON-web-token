<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_Controller extends CI_Controller {
    public function __construct()
	{
		parent::__construct();
                $this->load->model('clientmodel');
	}
        
        public function index(){
            
                $data=array();
                $response = $this->clientmodel->getData();
                if(!empty($response)){
                    $data['status']=true;
                    $data['allData']=$response;
                }else{
                    $data['status']=false;
                    $data['allData']='No Record Found!';
                }
           
            echo json_encode($data);
        }
        
        public function store(){
            $data = array('success'=>false,'messages'=>array());
            $this->form_validation->set_rules('name', 'Client Name', 'trim|required');
            $this->form_validation->set_rules('email', 'Client email', 'trim|required|min_length[10]|max_length[150]|valid_email|is_unique[client.email]');
            $this->form_validation->set_rules('retainer_fee', 'Retainer Fees', 'required|max_length[255]');
            
            $this->form_validation->set_error_delimiters('<p class="error_show">','</p>');
            if($this->form_validation->run()) {
            
            // If Validation True
            $data['success'] = true; //Message Success True
            
            $name=$this->input->post('name');
            $email=$this->input->post('email');
            $retainer_fee=$this->input->post('retainer_fee');
            
            $formdata = array(
                        'name' => $name,
	        	'email' => $email,
                        'retainer_fee' => $retainer_fee,
                        'updated_at'=>date('Y-m-d H:i:s', time()),
	        	);
            $query = $this->clientmodel->insert($formdata);
            
            if(!empty($query)){
                $data['messages']['lastid']=$query;
                $data['messages']['text']="Client added successfully";
                $data['messages']['type']="success";
                $data['messages']['status']=true;
            }else{
                $data['messages']['status']=false;
                $data['messages']['text']="Something went wrong!";
                $data['messages']['type']="danger";
            }
            
            
            }else{
                
           $dataPost=$this->input->post();
           foreach ($dataPost as $key => $values){
                $data['messages'][$key]=form_error($key);
                }
            }
            
            echo json_encode($data); 		
	}
        
        public function update(){
            $id=$this->uri->segment(3);
            
            $data = array('success'=>false,'messages'=>array());
            $this->form_validation->set_rules('name', 'Client Name', 'trim|required');
            $this->form_validation->set_rules('email', 'Client email', 'trim|required|min_length[10]|max_length[150]|valid_email|is_unique[client.email]');
            $this->form_validation->set_rules('retainer_fee', 'Retainer Fees', 'required|max_length[255]');
            
            $this->form_validation->set_error_delimiters('<p class="error_show">','</p>');
            if($this->form_validation->run()) {
            
            // If Validation True
            $data['success'] = true; //Message Success True
            $name=$this->input->post('name');
            $email=$this->input->post('email');
            $retainer_fee=$this->input->post('retainer_fee');
            
            $formdata = array(
                        'name' => $name,
	        	'email' => $email,
                        'retainer_fee' => $retainer_fee,
                        'updated_at'=>date('Y-m-d H:i:s', time()),
	        	);
            
            if(!empty($id)){
                $query = $this->clientmodel->update($formdata, $id);
            }else{
              
            }
            
            if(!empty($query)){
                $data['messages']['updatedid']=$id;
                $data['messages']['text']="Client Updated successfully";
                $data['messages']['type']="success";
                $data['messages']['status']=true;
            }else{
                $data['messages']['status']=false;
                $data['messages']['text']="Something went wrong!";
                $data['messages']['type']="danger";
            }
            
            
            }else{
                
           $dataPost=$this->input->post();
           foreach ($dataPost as $key => $values){
                $data['messages'][$key]=form_error($key);
                }
            }
            
            echo json_encode($data); 		
	}
        
        
        
        function viewdetail(){
            $id=$this->uri->segment(3);
            if(!empty($id)){
                $data = $this->clientmodel->get_Data_by_id($id);
            }else{
                $data='No Data Found!';
            }
            echo json_encode($data);
        }
        
        function remove(){
        $id=$this->uri->segment(3);
        if(!empty($id)){
        $response = $this->clientmodel->remove($id);
            if(!empty($response)){
                $data['type']="success";
                $data['status']=true;
            }else{
                $data['type']="danger";
                $data['status']=false;
            }
            }else{
                $data='No Data Found!';
            }
            echo json_encode($data);
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
