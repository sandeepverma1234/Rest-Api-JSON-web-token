<?php  

class ClientModel extends CI_Model {  
    
    public function __construct() {
        parent::__construct();
        
    }
    
    //Insert Into table with field 
     function insert($formArray) 
    {
            $this->db->insert('client',$formArray); //Table Name And With fields
            $insert_id = $this->db->insert_id(); //Get Last Inserted ID
            return ($insert_id > 0) ? $insert_id : false;
    }
    
    //Update Record Into table with field 
     function update($formArray,$id) 
    {
           $this->db->where('id',$id);  // Also mention table name here
           $this->db->update('client',$formArray);
           return ($this->db->affected_rows() != 1) ? false : true;
    }
    
    //Delete record from table with field 
     function remove($id) 
    {
           $this->db->where('id',$id);  // Also mention table name here
           $this->db->delete('client');
           return ($this->db->affected_rows() != 1) ? false : true;
    }
    
    //Get All record from table 
    function getData(){
        $this->db->select('client.*');
        $this->db->from('client');
        $query = $this->db->get();
        $result = $query->result_array();
        return !empty($result) ? $result : false;
    }
    
    //Get record from table by id
    function get_Data_by_id($id){
        $this->db->select('client.*');
        $this->db->from('client');
        $this->db->where('id',$id);  // Also mention table name here
        $query = $this->db->get();    
        $result = $query->row();
        return !empty($result) ? $result : false; // Return Results
    }
    
}
