<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
 
class Score_board_model extends CI_Model{
 function __construct(){
	parent::__construct();
 }
 
 private function pref($tablename){
 	return 'score_'.$tablename;
 }
 
 function insert_new_target($data,$return=false){
 	return $this->insert_row($this->pref('targets'),$data,$return);
 }
 
 function insert_new_schedule($data,$return=false){
 	$table=$this->pref('schedule');
 	return $this->insert_row($table,$data,$return);
 }
 
 function insert_new_job_res($sc_id,$content){
 	$sql="INSERT IGNORE INTO ".$this->pref('job_result')." (`schedule_id`,`url`) 
 		VALUES (".$this->db->escape($sc_id).",".$this->db->escape($content).")";
 	$this->db->query($sql);
 }
 
 function insert_new_job($data,$return){
 	return $this->insert_row($this->pref('job'),$data,$return);
 }
 
 function insert_row($tablename,$data,$getId=FALSE){
 	$this->db->insert($tablename,$data);
 	if ($getId == TRUE) return $this->db->insert_id();
 }
 
 function getTargets($uId,$targetId=''){
 	if($uId !== '') $this->db->where(array('user_id'=>$uId));
 	if($targetId !== '') $this->db->where(array('ID'=>$targetId));
 	$data=$this->db->get($this->pref('targets'));
 	$rows=$data->result();
 	return $rows;
 }
 
 function getJobs($id=0){
 	$this->db->group_by($this->pref('job.ID'));
 	$this->db->where(array($this->pref('targets.ID')=>$id));
 	$this->db->select(array($this->pref('job.ID as jId'),
 				$this->pref('job.job_name'),
 				$this->pref('targets.ID as tId'),
 				$this->pref('targets.target_name'),
 				$this->pref('targets.period_start'),
 				$this->pref('targets.period_finish'),
 				$this->pref('targets.user_id'),
 				$this->pref('job_result.ID as jresID'),
 				$this->pref('schedule.ID as sc_id')
 			));
 	$this->db->join($this->pref('job'),
 				$this->pref('job.target_id').' = '.$this->pref('targets.ID'),
 				'left');
 	$this->db->join($this->pref('schedule'),
 				$this->pref('job.ID').' = '.$this->pref('schedule.job_id'),
 				'left');
 	$this->db->join($this->pref('job_result'),
 				$this->pref('schedule.ID').' = '.$this->pref('job_result.schedule_id'),
 				'left');
 	$data=$this->db->get($this->pref('targets'));
 	return $data->result();
 }
 
 function get_job_res($sc_id){	// where schedule id
 	$this->db->where(array('schedule_id'=>$sc_id,'LEFT(`url`,4)'=>'http'));//
 	$q=$this->db->get($this->pref('job_result'));
 	return $q->result();
 }
 
 function getJobRes($jobId){
 	$data=array($this->pref('job.ID')=>$jobId);
 	$this->db->where($data);
 	$this->db->select(array(
 				$this->pref('schedule.ID as sc_id'),
 				$this->pref('schedule.date as date'),
 				$this->pref('job_result.ID as ID'),
 				$this->pref('schedule.count as count')
 			));
 	$this->db->join($this->pref('schedule'),
 				$this->pref('schedule.job_id').' = '.$this->pref('job.ID'),
 				'left');
 	$this->db->join($this->pref('job_result'),
 				$this->pref('job_result.schedule_id').' = '.$this->pref('schedule.ID'),
 				'left');
 	$this->db->order_by('date','asc');
 	$this->db->group_by('date');
 	$data=$this->db->get($this->pref('job'));
 	return $data->result();
 }
 
 function get_period($jobId){
 	$param=array($this->pref('schedule.job_id')=>$jobId);
 	$this->db->where($param);
 	$this->db->order_by('date','asc');
 	$data=$this->db->get($this->pref('schedule'));
 	return $data->result();
 }
 
 function get_score($scID,$job_id,$userId){
 	$data=array(
 		//$this->pref('job_result.ID')=>$jresID,
 		$this->pref('schedule.ID')=>$scID,
 		$this->pref('targets.user_id')=>$userId
 		);
 	$this->db->where($data);
 	$this->db->select(array(
 		$this->pref('job_result.ID as jId'),
 		$this->pref('job_result.url as url'),
 		$this->pref('job.target_id as tId'),
 		$this->pref('job.job_name as jName'),
 		$this->pref('targets.user_id as uId'),
 		$this->pref('targets.target_name as tName'),
 		$this->pref('schedule.date as date')
 		));
 	$this->db->join($this->pref('schedule'),
 				$this->pref('schedule.job_id').' = '.$this->pref('job.ID'),
 				'left');
 	$this->db->join($this->pref('job_result'),
 				$this->pref('job_result.schedule_id').' = '.$this->pref('schedule.ID'),'left');
 	$this->db->join($this->pref('targets'),
 				$this->pref('targets.ID').' = '.$this->pref('job.target_id'),
 				'left');
 	$data=$this->db->get($this->pref('job'));
 	return $data->result();
 }
 
 function saveToDb($table,$data=array(),$where=array()){
 	if(count($where) != 0){
 		$this->db->where($where);
 	}
 	$this->db->update($table,$data);
 }
 
 function get_total_contents($id){
 	$where=array($this->pref('job_result.schedule_id')=>$id);
 	$this->db->where($where);
 	$this->db->select($this->pref('job_result.url'));
 	/*$this->db->join($this->pref('schedule'),
 				$this->pref('schedule.ID').' = '.$this->pref('job_result.schedule_id'),
 				'left');*/
 	$data=$this->db->get($this->pref('job_result'));
 	return $data->num_rows();
 }
 
 function get_all_job($id=0){
  $param=array($this->pref('targets.ID')=>$id);
  if (count($param) > 0) $this->db->where($param);
  $this->db->select(array(
  			$this->pref('targets.*'),
  			$this->pref('job.ID as job_id'),
  			$this->pref('job.job_name')
  		));
  $this->db->join($this->pref('targets'),
  			$this->pref('targets.ID').'='.$this->pref('job.target_id'),
  			'right');
  $q=$this->db->get($this->pref('job'));
  return $q->result();
 }
 
 function get_period_data($id){
 	$param=array('ID'=>$id);
 	$this->db->where($param);
 	$q=$this->db->get($this->pref('targets'));
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
 
 function update_job($job,$job_id){
 	$data=array('job_name'=>$job);
 	$param=array('ID'=>$job_id);
 	$this->update_row($this->pref('job'),$data,$param);
 }
 
 function update_job_res($jres_id,$content){
 	$param=array('ID'=>$jres_id);
 	$data=array('url'=>$content);
 	$sql="UPDATE IGNORE ".$this->pref('job_result')." SET `url`=".$this->db->escape($content)." WHERE ID=".$this->db->escape($jres_id);
 	$this->db->query($sql);
 }
 
 function update_target($new_period_start,$new_period_finish,$target_id){
 	$data=array('period_start'=>$new_period_start,'period_finish'=>$new_period_finish);
 	$param=array('ID'=>$target_id);
 	$this->update_row($this->pref('targets'),$data,$param);
 }
 
 function delete_job_res_id($id){
 	$param=array('ID'=>$id);
 	$this->delete_row($this->pref('job_result'),$param);
 }
 
 function delete_job_result($param=array()){
 	$this->db->where($param);
	$q=$this->db->get($this->pref('schedule'));
 	foreach($q->result() as $r){
 		$ID=$r->ID;
 		$this->delete_row($this->pref('job_result'),array('schedule_id'=>$ID));
 	}
 }
 
 function prepare_delete_jres($id){
 	$param=array('ID'=>$id);
 	$this->delete_row($this->pref('job_result'),$param);
 }
 
 function delete_schedule($param){
 	$this->delete_row($this->pref('schedule'),$param);
 }
 
 function delete_target_packet($id){
 	$this->db->where(array($this->pref('targets.ID')=>$id));
 	$this->db->select(array(
 			$this->pref('job_result.ID as jres_id'),
 			$this->pref('schedule.ID as sc_id'),
 			$this->pref('job.ID as j_id')
 			));
 	$this->db->join($this->pref('job'),
 				$this->pref('targets.ID').'='.$this->pref('job.target_id'),
 				'left');
 	$this->db->join($this->pref('schedule'),
 				$this->pref('job.ID').'='.$this->pref('schedule.job_id'),
 				'left');
 	$this->db->join($this->pref('job_result'),
 				$this->pref('schedule.ID').'='.$this->pref('job_result.schedule_id'),
 				'left');
 	$q=$this->db->get($this->pref('targets'));
 	$res=$q->result();
 	foreach($res as $r){
 		$jres_id=$r->jres_id;
 		$sc_id=$r->sc_id;
 		$j_id=$r->j_id;
 		$this->delete_row($this->pref('job_result'),array('ID'=>$jres_id)); // => delete job_result
 		$this->delete_row($this->pref('schedule'),array('ID'=>$sc_id)); // => delete schedule
 		$this->delete_row($this->pref('job'),array('ID'=>$j_id)); // => delete job
 	}
 	$this->delete_row($this->pref('targets'),array('ID'=>$id)); // => delete targets
 }
 
 function reset_table($table){
 	$this->db->query('truncate '.$this->pref($table));
 }
 
 function get_schedule_id($param){
 	$this->db->where($param);
 	$q=$this->db->get($this->pref('schedule'));
 	$res=$q->result();
 	return $res;
 }
 
 function get_sch_datas($sc_id){
 	$param=array($this->pref('schedule.ID')=>$sc_id);
 	$this->db->where($param);
 	$this->db->select(array(
 				$this->pref('schedule.ID as ID'),
 				$this->pref('job.job_name as jobname'),
 				$this->pref('schedule.count as count'),
 				$this->pref('schedule.date as date'),
 				$this->pref('job.ID as job_id')
 			));
 	$this->db->join($this->pref('job'),
 			$this->pref('schedule.job_id').'='.$this->pref('job.ID'),
 			'left');
 	$q=$this->db->get($this->pref('schedule'));
 	return $q->row();
 }
 
 function delete($table,$data){
 	$this->delete_row($table,$data);
 }
 
 function save_post_target($name,$target_id){
 	$data=array('target_name'=>$name);
 	$param=array('ID'=>$target_id);
 	$this->update_row($this->pref('targets'),$data,$param);
 }
	
 function get_day_from_time($start,$end){
	return (strtotime($start) - strtotime($end))/(60*60*24);
 }
 
 function save_post_period($target_id=0){
 	$q=$this->get_period_data($target_id); //$target_id
		foreach($q as $trgt){
			$old_start=date_format(new dateTime($trgt->period_start),'Y-m-d');
			$old_end=date_format(new dateTime($trgt->period_finish),'Y-m-d');
		}
		$new_start=$this->input->post('start_period');
		$new_end=$this->input->post('end_period');
		
		$max_value_start=$this->get_day_from_time($old_start,$old_end);
		
		$difrnc_start=$this->get_day_from_time($old_start,$new_start);//(strtotime($old_start) - strtotime($new_start))/(60*60*24);
		$difrnc_end=$this->get_day_from_time($old_end,$new_end);//(strtotime($old_end) - strtotime($new_end))/(60*60*24);
	// START PERIOD
		$sisa=abs($max_value_start) + abs($difrnc_start);
		//echo abs($max_value_start).'|'.$sisa.'|'.abs($difrnc_start).br();
		if($old_start != $new_start){
		 if($difrnc_start > 0){
			echo "add".br();
			$job_id=0;
			for ($date=0;$date<=$difrnc_start-1;$date++){
				$newdate1 = strtotime('+'.$date.' day',strtotime($new_start));
				$newdate1 = date('Y-m-d', $newdate1);
				$q_job=$this->getJobs($target_id);
				foreach($q_job as $r){
					$job_id=$r->jId;
					$data=array(
						'date'=>$newdate1,
						'job_id'=>$job_id
					);
					$sc_id=$this->insert_new_schedule($data,true);
					//$this->insert_new_job_res($sc_id,$content);
				}
				if($date==0){
					$new_date_start=$newdate1;
				}
				
				//echo $new_date_start.br();
			}
		 }elseif($difrnc_start < 0 ){
			if(abs($difrnc_start) <= abs($max_value_start)){
				echo "delete".br();
				for ($date=0;$date>=$difrnc_start+1;$date--){ //$difrnc_start-1
					$newdate1 = strtotime('-'.$date.' day',strtotime($old_start));
					$newdate1 = date('Y-m-d', $newdate1);
					$q_job=$this->getJobs($target_id);
					foreach($q_job as $r){
						$job_id=$r->jId;
						$data=array(
							'date'=>$newdate1,
							'job_id'=>$job_id
						);
						$this->delete_job_result($data);
						$this->delete_row($this->pref('schedule'),$data);
					}
					//change date +1 day
					if($date==0){
						$newdate1_ = strtotime('+'.abs($difrnc_start).' day',strtotime($newdate1));
						$newdate1_ = date('Y-m-d', $newdate1_);
						$new_date_start=$newdate1_;
					}
				}
			}else{
				$new_date_start=$old_start;
			}
		 }else{
			$new_date_start=$new_start;
		 }
		}else{$new_date_start=$new_start;}
	// END PERIOD
		if($new_end != $old_end){
		 if($difrnc_end > 0){
			echo "delete".br();
			for ($date=1;$date<=$difrnc_end;$date++){
				$newdate2 = strtotime('+'.$date.' day',strtotime($new_end));
				$newdate2 = date('Y-m-d', $newdate2);
					$q_job=$this->getJobs($target_id);
					foreach($q_job as $r){
						$job_id=$r->jId;
						$data=array(
							'date'=>$newdate2,
							'job_id'=>$job_id
						);
						$this->delete_job_result($data);
						$this->delete_schedule($data);
					}
					if($date==$difrnc_end){
						$newdate2_ = strtotime('-'.abs($difrnc_end).' day',strtotime($newdate2));
						$newdate2_ = date('Y-m-d', $newdate2_);
						$new_date_end=$newdate2_;
					}
			}
		 }elseif($difrnc_end < 0){
			echo "add".br();
			for ($date=-1;$date>=$difrnc_end;$date--){ //$difrnc_start-1
				$newdate2 = strtotime('-'.$date.' day',strtotime($old_end));
				$newdate2 = date('Y-m-d', $newdate2);
				$new_date_end=$newdate2;
					$q_job=$this->getJobs($target_id);
					foreach($q_job as $r){
						$job_id=$r->jId;
						$data=array(
							'date'=>$newdate2,
							'job_id'=>$job_id
						);
						$sc_id=$this->insert_new_schedule($data,true);
						//$this->insert_new_job_res($sc_id,$content);
					}
			}
		 }else{
			$new_date_end=$new_end;
		 }
		}else{
			$new_date_end=$new_end;
		}
		
		$this->update_target($new_date_start,$new_date_end,$target_id);
 }
 
 function save($user_id){
 	$target=$this->input->post('target');
	$start=$this->input->post('start');
	$finish=$this->input->post('finish');
	if (empty($target)) exit;	//if target is empty
	$data=$this->create_data($user_id,$target,'',$start,$finish);
	return $data;
 }
 
 function create_data($userId,$target='',$target_id='',$start,$finish,$job_data=array()){
	$difrnc=(strtotime($finish) - strtotime($start))/(60*60*24);
	if ($difrnc < 1){return "periode tidak valid"; exit;}
	  if($target !== ''){
		$target_data=array('user_id'=>$userId,'target_name'=>$target,'period_start'=>$start,'period_finish'=>$finish);
		$target_id=$this->insert_new_target($target_data,TRUE);
	  }
	if ($target_id != ''){
		$job_count=count($job_data);
		if($job_count == 0){$n1=0;$n2=count($_POST['job'])-1;}else{$n1=0;$n2=$job_count-1;}
		for ($a=$n1;$a<=$n2;$a++){ //echo count($job_data);
			if($job_count == 0){
			 $job=$_POST['job'][$a];
			}else{
			 $job=$job_data[$a];
			}
			if(!empty($job)){
			$job_datas=array('target_id'=>$target_id,'job_name'=>$job);
				$jobId=$this->insert_new_job($job_datas,TRUE);
			 for ($date=0;$date<=$difrnc;$date++){
				$newdate = strtotime('+'.$date.' day',strtotime($start));
				$newdate = date('Y-m-d', $newdate);
				$schedule_data=array(
						'job_id'=>$jobId,
						'date'=>$newdate,
						'count'=>0
					);
				$sc_id=$this->insert_row($this->pref('schedule'),$schedule_data,TRUE);
				/*$data_res=array(
						'schedule_id'=>$sc_id,
						'url'=>''
					);
				if($sc_id != '')$this->insert_row($this->pref('job_result'),$data_res);*/
			 }
			}
		}
	 return 1;	
	}else{
		return "Terjadi kesalahan saat memasukkan data. Harap periksa kembali!";
	}
 }
 
 function delete_job($job_id,$target_id){
 	$q=$this->get_schedule_id(array('job_id'=>$job_id));
	 foreach($q as $r){
	 	$sc_id=$r->ID;
	 	$data=$this->get_job_res($sc_id);
	 	foreach($data as $r){
	 	print_r($r);
	 		if(substr($r->url,0,4) == 'file'){
		 		$ex=explode('/',$r->url);
		 		$filename=$ex(count($ex)-2 .'/'.count($ex)-1);
		 		echo $filename;
	 		}
	 	}
	 	exit;
	 	//$this->delete_row($this->pref('job_result'),array('schedule_id'=>$sc_id));
	 }
	 $table=array($this->pref('job')=>array('ID'=>$job_id),
	 		$this->pref('schedule')=>array('job_id'=>$job_id)
	 	);
	 foreach($table as $t=>$key){
		$this->delete_row($t,$key);
	 }
 }
 
 function edit_target(){
 	$ids=$this->input->post('id');
	$limit= count($ids);
	 for($a=0;$a<=$limit-1;$a++){
		$ex=explode('|',$ids[$a]);
		 $id=$ex[0];
		 $v=$ex[1];
		$val=$this->input->post('count'.$v);
		$data=array('count'=>$val);
		$param=array('ID'=>$id);
		$this->update_schedule($data,$param);
	 }
 }
 
 function update_schedule($data,$param){
 	$this->update_row($this->pref('schedule'),$data,$param);
 }
 

}
?>
