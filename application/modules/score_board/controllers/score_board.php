<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Score_board extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->helper(array('url','html'));
		$this->load->model('score_board_model','m');
		$this->load->library('encrypt');
	}
 
	function header(){
	 	$this->load->view('main/header');
	}
	
	function get_uId(){
		return 1;
	}
	
	function flash(){
		$this->load->library('session');
		return $this->session;
	}
	
	function path($name=''){
		return BASE_PATH.$name;
	}
	
	function uri_segment(){
		return $this->uri;
	}
	
	function index(){
		$this->home();
	}
	
	function home($data=array()){
		$this->load->helper('form');
		$this->header();
		$this->load->view('view_sbd',$data);
	}
	
	function targets(){
		$uId=$this->get_uId();
		$data['result']=$this->m->getTargets(array('user_id'=>$uId));
		$this->header();
		$this->load->view('targets',$data);
	}
	
	function save(){
		$target=$this->input->post('target');
		$start=$this->input->post('start');
		$finish=$this->input->post('finish');
		if (empty($target)) exit;	//if target is empty
		$this->create_data($target,'',$start,$finish);
		echo "Terjadi kesalahan saat memasukkan data. Harap periksa kembali!";
	}
	
	function scoreBoard($id=''){
		if ($id=='') exit;
		$this->header();
		$result=$this->m->getJobs($id);
		$data['result']=$result;
		$data['model']=$this->m;
		$data['encrypt']=$this->encrypt;
		$data['c']=$this;
		$this->load->helper('form');
		$this->load->view('scoreboard',$data);
		$this->load->view('modal');
	}
	
	function edit_job(){
		$ID=$this->uri->segment(3);
		$job_id=$this->uri->segment(4);
		$userId=$this->get_uId();
		$data['flash']=$this->flash();
		$data['uri']=$this->uri_segment();
		
		$this->load->helper('form');
		$data['res']=$this->m->get_score(array('job_result.ID'=>$ID,'schedule.job_id'=>$job_id,'targets.user_id'=>$userId));
		$this->header();
		$this->load->view('edit',$data);
	}
	
	function update_score(){
		if($_POST){
			 echo br().'DATA '.br();print_r($_POST);
			 echo br(2);
			 $id=0;
			 for($c=0;$c <= count($_POST['content'])-1;$c++){
			 	$id +=$c;
			 	$url=$_POST['content'][$c];
			 	if(strlen($url)!=0 or $url!='')	$val[]=array('ID'=>$id,'url'=>$url);
			 }
			 	$plus=1;
			 	if(isset($_POST['hidden'])){
			 	 $hidden=$_POST['hidden'];
				 	for($b=0;$b<=count($hidden)-1;$b++){
				 	   $filename=$hidden[$b];
					   if(isset($_POST['delete'.$b])){
						    $filedelete=$_POST['delete'.$b];
						    if($filedelete==$filename){
						 	   $this->delete_file($filedelete);
						 	   //echo 'delete '.$filedelete.br();
						   }else{
							  $val[]=array('ID'=>$id,'url'=>'file://'.$this->path($filename));
							  //echo 'simpan '.$filename.br();
						    	}
					    }else{
						  $val[]=array('ID'=>$id,'url'=>'file://'.$this->path($filename));
						  //echo 'simpan '.$filename.br();
					    }
					 }
				 }
			if($_FILES['userfile']['name'][0] != ''){
				$this->load->library('upload');
				$files = $_FILES;
				$cpt = count($_FILES['userfile']['name']);
				for($i=0; $i<$cpt; $i++)
				{
					$_FILES['userfile']['name']= $files['userfile']['name'][$i];
					$_FILES['userfile']['type']= $files['userfile']['type'][$i];
					$_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
					$_FILES['userfile']['error']= $files['userfile']['error'][$i];
					$_FILES['userfile']['size']= $files['userfile']['size'][$i];    
					$id += $i+1;
					$this->upload->initialize($this->set_upload_options());
					if(!$this->upload->do_upload())	print_r($this->upload->display_errors());
					else	$val[]=array('ID'=>$id,'url'=>'file://'.$this->upload->data()['full_path']);
				}
			}
			$data=json_encode($val);
			//echo br().'SIMPAN'.$data;
			try{
				$this->m->saveToDb('job_result',array('url'=>$data),array('ID'=>$this->flash()->flashdata('ID')));
				redirect($this->flash()->flashdata('last_url'));
			}catch(Exception $e){
				print_r($e->getMessage());
			}
		}
		exit;
	}
	
	function delete_file($filename){
		$path = $this->path('assets/uploads/score_board/'.$filename);
		if(file_exists($path)){
			@unlink($path);
		}
	}
	
	private function set_upload_options()
	{   
	//  upload an image options
	    $config = array();
	    $config['upload_path'] = './assets/uploads/score_board/';
	    $config['allowed_types'] = '*';
	    //$config['max_size']      = '990000';
	    $config['overwrite']     = FALSE;
	    $config['file_name']     = md5(microtime());


	    return $config;
	}
	
	function change_field($name='',$id=''){
		if($_POST){
		 $target_id=$this->flash()->flashdata('id');
			if($name == 'period'){
				$this->save_post_period($target_id);
			}elseif($name == 'tname'){
				$this->save_post_target($target_id);
			}elseif($name == 'job'){
				$this->save_post_job($target_id);
			}
			redirect('score_board/change_field/sboard/'.$target_id);
		}
		$q=$this->m->get_all_job(array('targets.ID'=>$id));//get_all_job([parameters])
		$data['result']=$q;
		$data['name']=$name;
		$data['ID']=$id;
		$data['flash']=$this->flash();
		$data['uri']=$this->uri_segment();
		$this->load->helper('form');
		
		$this->header();
		$this->load->view('edit_fields',$data);
	}
	
	function save_post_period($target_id=0){
		//print_r($_POST);
		$target_id=$this->flash()->flashdata('id');
		$q=$this->m->get_period_data(array('ID'=>1));//$target_id
		foreach($q as $trgt){
			$data=explode('::',$trgt->period);
			$old_start=date_format(new dateTime($data[0]),'Y-m-d');
			$old_end=date_format(new dateTime($data[1]),'Y-m-d');
		}
		$new_start=$this->input->post('start_period');
		$new_end=$this->input->post('end_period');
		
		$max_value_start=$this->get_day_from_time($old_start,$old_end);
		
		$difrnc_start=$this->get_day_from_time($old_start,$new_start);//(strtotime($old_start) - strtotime($new_start))/(60*60*24);
		$difrnc_end=$this->get_day_from_time($old_end,$new_end);//(strtotime($old_end) - strtotime($new_end))/(60*60*24);
	// START PERIOD
		$sisa=abs($max_value_start)+abs($difrnc_start);
		//echo abs($max_value_start).'|'.$sisa.'|'.abs($difrnc_start).br();
		if($old_start != $new_start){
		 if($difrnc_start > 0){
			echo "add".br();
			$job_id=0;
			for ($date=0;$date<=$difrnc_start-1;$date++){
				$newdate1 = strtotime('+'.$date.' day',strtotime($new_start));
				$newdate1 = date('Y-m-d', $newdate1);
				$q_job=$this->m->getJobs($target_id);
				foreach($q_job as $r){
					$job_id=$r->jId;
					$data=array(
						'date'=>$newdate1,
						'job_id'=>$job_id
					);
					print_r($data);
					echo br(2);
					$sc_id=$this->m->insert_row('schedule',$data,true);
					$this->m->insert_row('job_result',array('schedule_id'=>$sc_id,'url'=>'[]'));
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
					$q_job=$this->m->getJobs($target_id);
					foreach($q_job as $r){
						$job_id=$r->jId;
						$data=array(
							'date'=>$newdate1,
							'job_id'=>$job_id
						);
						$this->m->delete_job_result($data);
						$this->m->delete_row('schedule',$data);
					}
					//change date +1 day
					if($date==0){
						$newdate1_ = strtotime('+'.abs($difrnc_start).' day',strtotime($newdate1));
						$newdate1_ = date('Y-m-d', $newdate1_);
						$new_date_start=$newdate1_;
						//print_r($new_date_start.br());
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
				//$new_date_end=$newdate2;
					$q_job=$this->m->getJobs($target_id);
					foreach($q_job as $r){
						$job_id=$r->jId;
						$data=array(
							'date'=>$newdate2,
							'job_id'=>$job_id
						);
						$this->m->delete_job_result($data);
						$this->m->delete_row('schedule',$data);
					}
					if($date==$difrnc_end){
						$newdate2_ = strtotime('-'.abs($difrnc_end).' day',strtotime($newdate2));
						$newdate2_ = date('Y-m-d', $newdate2_);
						$new_date_end=$newdate2_;
						//print_r($new_date_start.br());
						//$this->log('date = difrn',$new_date_end);
					}
					//$this->log('loop',$new_date_end.'|'.$newdate2);
			}
		 }elseif($difrnc_end < 0){
			echo "add".br();
			for ($date=-1;$date>=$difrnc_end;$date--){ //$difrnc_start-1
				$newdate2 = strtotime('-'.$date.' day',strtotime($old_end));
				$newdate2 = date('Y-m-d', $newdate2);
				$new_date_end=$newdate2;
					$q_job=$this->m->getJobs($target_id);
					foreach($q_job as $r){
						$job_id=$r->jId;
						$data=array(
							'date'=>$newdate2,
							'job_id'=>$job_id
						);
						$sc_id=$this->m->insert_row('schedule',$data,true);
						$this->m->insert_row('job_result',array('schedule_id'=>$sc_id,'url'=>'[]'));
					}
				//echo $new_date_end.br();
			}
		 }else{
			$new_date_end=$new_end;
		 }
		}else{
			$new_date_end=$new_end;
		}
		
		$new_period=$new_date_start.'::'.$new_date_end;
		$this->m->update_row('targets',array('period'=>$new_period),array('ID'=>$target_id));
		redirect('score_board/targets');
	}
	
	function save_post_target($target_id=0){
		if($_POST){
			$name=$this->input->post('tname');
			$this->m->update_row('targets',array('target_name'=>$name),array('ID'=>$target_id));
		}else{echo 'error';exit;}
		
		redirect('score_board/targets/'.$target_id);
	}
	
	function save_post_job($target_id){
		if($_POST){
		$job_data=array();
		 for($a=0;$a<=count($_POST['job'])-1;$a++){
		 	$job=$_POST['job'][$a];
		 	 if($job=='')$job='NotSet(Empty)';
		 	$job_id=$_POST['jid'][$a];
		 	if($job_id != ''){
				$this->m->update_row('job',array('job_name'=>$job),array('ID'=>$job_id));
			}else{
				$job_data[]=$job;
			}
		 }
//		 print_r($job_data);
			$this->create_new_job($target_id,$job_data);

		}else{echo 'error';exit;}
		redirect('score_board/targets/'.$target_id);
		
	}
	
	function create_new_job($target_id,$job_data){
		if(!empty($job_data)){
			$date=explode('|',$this->input->post('date_period'));
			$start=$date[0];
			$finish=$date[1];
			//echo $target_id;
			//$id=$this->m->insert_row('job',$data,true);
			$this->create_data('',$target_id,$start,$finish,$job_data);
		}
	}
	
	function create_data($target='',$target_id='',$start,$finish,$job_data=array()){
		//echo $target_id;
		$userId=$this->get_uId();
		$period=$start.'::'.$finish;
		$difrnc=(strtotime($finish) - strtotime($start))/(60*60*24);
		if ($difrnc < 1){echo "periode tidak valid"; exit;}
		  if($target !== ''){
			$target_data=array('user_id'=>$userId,'target_name'=>$target,'period'=>$period);
			$target_id=$this->m->insert_row('targets',$target_data,TRUE);
		  }
		if ($target_id != ''){
			$job_count=count($job_data);
			if($job_count == 0){$n1=0;$n2=count($_POST['job'])-1;}else{$n1=0;$n2=$job_count-1;}
				//exit;
			for ($a=$n1;$a<=$n2;$a++){ //echo count($job_data);
			 //$job=$job_data[$a];
			 //print_r($job_data[0].':'.$job_data[1]);echo br();
				if($job_count == 0){
				 $job=$_POST['job'][$a];
				 //echo '*'.br();
				}else{
				 $job=$job_data[$a];
				 //echo $a.'>'.br();
				}
				if(!empty($job)){
				$job_datas=array('target_id'=>$target_id,'job_name'=>$job);
					$jobId=$this->m->insert_row('job',$job_datas,TRUE);
				//echo $difrnc;
				 for ($date=0;$date<=$difrnc;$date++){
					$newdate = strtotime('+'.$date.' day',strtotime($start));
					$newdate = date('Y-m-d', $newdate);
					$schedule_data=array(
							'job_id'=>$jobId,
							'date'=>$newdate,
							'count'=>0
						);
					$sc_id=$this->m->insert_row('schedule',$schedule_data,TRUE);
					$data_res=array(
							'schedule_id'=>$sc_id,
							'url'=>'[]'
						);
					if($sc_id != '')$this->m->insert_row('job_result',$data_res);
				 }
				}
			}
		 redirect('score_board/targets');		
		}
	}
	
	function log($bagian,$value){
		print_r(br().'['.$bagian.'<>'.$value.']'.br());
	}
	
	function get_day_from_time($start,$end){
		return (strtotime($start) - strtotime($end))/(60*60*24);
	}
	
	function delete_target($id=0){
		$userId=$this->get_uId();
		$q=$this->m->getTargets(array('ID'=>$id,'user_id'=>$userId));
		if(count($q)>0){
			$this->m->delete_target_packet($id);
		}
		redirect('score_board');
	}
	
	function delete_job($job_id=0,$target_id=0){
	 $q=$this->m->get_schedule_id(array('job_id'=>$job_id));
	 foreach($q as $r){
	 	$sc_id=$r->ID;
	 	$this->m->delete('job_result',array('schedule_id'=>$sc_id));
	 }
	 $table=array(	'job'=>array('ID'=>$job_id),
	 		'schedule'=>array('job_id'=>$job_id)
	 	);
	 foreach($table as $t=>$key){
		$this->m->delete($t,$key);
	 }
	 redirect('score_board/scoreBoard/'.$target_id);
	}
	
	function reset_data($table=''){
		$table=array('job','job_result','schedule','targets');
		foreach($table as $table){
			$this->m->reset_table($table);
		}
		redirect('score_board');
	}	
	
	function edit_targets(){
		if(isset($_POST['id'])){
			//print_r($_POST);
			$ids=$this->input->post('id');
			$limit= count($ids);
			 for($a=0;$a<=$limit-1;$a++){
				$ex=explode('|',$ids[$a]);
				 $id=$ex[0];
				 $v=$ex[1];
				$val=$this->input->post('count'.$v);
				$data=array('count'=>$val);
				$param=array('ID'=>$id);
				$this->m->update_row('schedule',$data,$param);
			 }
			redirect('score_board/targets');
		}
		if(!isset($_POST['check'])){echo 'tidak ada field yang akan di edit!!!';exit;}
		$post=$this->input->post('check');
		foreach($post as $sc_id){
		 $param=array('schedule.ID'=>$sc_id);
		 $data_form[]=$this->m->get_sch_datas($param);
		}
		$data['fields']=$data_form;
		$this->load->helper('form');
		$this->header();
		$this->load->view('change_field_schedule',$data);
	}
	
	function get_smile($status=':-)'){
		$this->load->helper('smiley');
		$data = parse_smileys($status,base_url().'assets/images/smileys/');
		return $data;
		
	}
	
}
