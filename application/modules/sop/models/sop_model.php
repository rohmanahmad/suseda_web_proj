<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
 
class Sop_model extends CI_Model{
 function __construct(){
	parent::__construct();
 }
 
 //primary functions
 
 function get($tablename,$param){
 	$this->db->where($param);
 	$q=$this->db->get($tablename);
 	return $w->result();
 }
 
 function insert($tablename,$data,$getId=FALSE){
 	$this->db->insert($tablename,$data);
 	if ($getId == TRUE) return $this->db->insert_id();
 }
 
 function save($table,$data=array(),$where=array()){
 	if(count($where) != 0){
 		$this->db->where($where);
 	}
 	$this->db->update($table,$data);
 }
 
 function delete($tablename,$param=array()){
 	$this->db->where($param);
 	$this->db->delete($tablename);
 }
 
 function reset_table($table){
 	$this->db->query('truncate '.$table);
 }
 
 // sub functions
 
 function get_sop_datas(){
 	$table='';
 	$param=array();
 	$q=$this->get($table,$param);
 }

}
?>
