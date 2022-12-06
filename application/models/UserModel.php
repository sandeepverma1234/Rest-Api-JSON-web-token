<?php  

class UserModel extends CI_Model {  

            //User Login Function
            function login($data)
            {
             $this->db->select('*');
             $this->db->from('user_tbl'); //Table Name And With fields
             $this->db->where('email', $data['email']);
             $query = $this->db->get();
             
            if($query->num_rows() > 0)
                {
                    $userData = $query->row();
                    if (password_verify($data['password'], $userData->password)) {
                       return $this->db->select('id,name,email,updated_at')->get_where('user_tbl', array('id' => $userData->id))->row();
                    }else{
                       return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            
            //User Registration Function
            function registration($data){
                
                $this->db->insert('user_tbl',$data); //Table Name And With fields
                $insert_id = $this->db->insert_id(); //Get Last Inserted ID
                return ($insert_id > 0) ? $this->db->select('id,name,email,updated_at')->get_where('user_tbl', array('id' => $insert_id))->row() : false;
            }
            
            
    
}