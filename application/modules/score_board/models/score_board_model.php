<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
 
class Score_board_model extends CI_Model{
 function __construct(){
	parent::__construct();
 }
 
 function insert_row($tablename,$data,$getId=FALSE){
 	$this->db->insert($tablename,$data);
 	if ($getId == TRUE) return $this->db->insert_id();
 }
 
 function getTargets($param){
 	$this->db->where($param);
 	$data=$this->db->get('targets');
 	$rows=$data->result();
 	return $rows;
 }
 
 function getJobs($id=0){
 	$this->db->group_by('job.ID');
 	$this->db->where(array('targets.ID'=>$id));
 	$this->db->select(array('job.ID as jId','job.job_name','targets.ID as tId','targets.target_name',
 				'targets.period','targets.user_id','job_result.ID'));
 	$this->db->join('job','job.target_id = targets.ID','left');
 	$this->db->join('schedule','job.ID = schedule.job_id','left');
 	$this->db->join('job_result','schedule.ID = job_result.schedule_id','left');
 	$data=$this->db->get('targets');
 	return $data->result();
 }
 
 
 function getJobRes($data=''){
 	$this->db->where($data);
 	$this->db->select(array('schedule.ID as sc_id','schedule.date as date','job_result.ID as ID','schedule.count as count'));
 	$this->db->join('schedule','schedule.job_id = job.ID','left');
 	$this->db->join('job_result','job_result.schedule_id = schedule.ID','left');
 	$this->db->order_by('date','asc');
 	$data=$this->db->get('job');
 	return $data->result();
 }
 
 function get_period($data=''){
 	$this->db->where($data);
 	$this->db->order_by('date','asc');
 	$data=$this->db->get('schedule');
 	return $data->result();
 }
 
 function get_score($data=''){
 	$this->db->where($data);
 	$this->db->select(array(
 		'job_result.ID as jId',
 		'job_result.url as url',
 		'job.target_id as tId',
 		'job.job_name as jName',
 		'targets.user_id as uId',
 		'targets.target_name as tName',
 		'schedule.date as date'
 		));
 	$this->db->join('schedule','job_result.schedule_id = schedule.ID','left');
 	$this->db->join('job','job.ID = schedule.job_id','left');
 	$this->db->join('targets','targets.ID = job.target_id','left');
 	$data=$this->db->get('job_result');
 	return $data->result();
 }
 
 function saveToDb($table,$data=array(),$where=array()){
 	if(count($where) != 0){
 		$this->db->where($where);
 	}
 	$this->db->update($table,$data);
 }
 
 function get_total_contents($where=array()){
 	$this->db->where($where);
 	$this->db->select('job_result.url');
 	$this->db->join('schedule','schedule.ID = job_result.schedule_id','left');
 	$data=$this->db->get('job_result');
 	foreach($data->result() as $r){
 	 return count(json_decode($r->url,TRUE));
 	}
 }
 
 function get_all_job($param=array()){
  if (count($param) > 0) $this->db->where($param);
  $this->db->select(array('targets.*','job.ID as job_id','job.job_name'));
  $this->db->join('targets','targets.ID=job.target_id','right');
  $q=$this->db->get('job');
  return $q->result();
 }
 
 function get_period_data($param=array()){
 	$this->db->where($param);
 	$q=$this->db->get('targets');
 	return $q->result();
 }
 
 function delete_row($tablename,$param=array()){
 	$this->db->where($param);
 	$this->db->delete($tablename);
 }
 
 function update_row($tablename,$data=array(),$param=array()){
 	$this->db->where($param);
 	$this->db->update($tablename,$data);
 }
 
 function delete_job_result($param=array()){
 	$this->db->where($param);
	$q=$this->db->get('schedule');
 	foreach($q->result() as $r){
 		$ID=$r->ID;
 		$this->db->delete('job_result',array('schedule_id'=>$ID));
 	}
 }
 
 function delete_target_packet($id){
 	$this->db->where(array('targets.ID'=>$id));
 	$this->db->select(array('job_result.ID as jres_id','schedule.ID as sc_id','job.ID as j_id'));
 	$this->db->join('job','targets.ID=job.target_id','left');
 	$this->db->join('schedule','job.ID=schedule.job_id','left');
 	$this->db->join('job_result','schedule.ID=job_result.schedule_id','left');
 	$q=$this->db->get('targets');
 	$res=$q->result();
 	foreach($res as $r){
 		$jres_id=$r->jres_id;
 		$sc_id=$r->sc_id;
 		$j_id=$r->j_id;
 		$this->delete_row('job_result',array('ID'=>$jres_id)); // => delete job_result
 		$this->delete_row('schedule',array('ID'=>$sc_id)); // => delete schedule
 		$this->delete_row('job',array('ID'=>$j_id)); // => delete job
 	}
 	$this->delete_row('targets',array('ID'=>$id)); // => delete targets
 }
 
 function reset_table($table){
 	$this->db->query('truncate '.$table);
 }
 
 function get_schedule_id($param){
 	$this->db->where($param);
 	$q=$this->db->get('schedule');
 	$res=$q->result();
 	return $res;
 }
 
 function get_sch_datas($param){
 	$this->db->where($param);
 	$this->db->select(array('schedule.ID as ID','job.job_name as jobname','schedule.count as count','schedule.date as date','job.ID as job_id'));
 	$this->db->join('job','schedule.job_id=job.ID','left');
 	$q=$this->db->get('schedule');
 	return $q->row();
 }
 
 function delete($table,$data){
 	$this->delete_row($table,$data);
 }
 

}
?>
