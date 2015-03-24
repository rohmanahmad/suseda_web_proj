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
		$data['result']=$this->m->getTargets($uId);
		$this->header();
		$this->load->view('targets',$data);
	}
	
	function save(){
		$userId=$this->get_uId();
		$res=$this->m->save($userId);
		if ($res == 1) redirect('score_board/targets');
		echo $res;
		exit;
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
	
	function edit_job($jresID=0,$job_id=0){
		$userId=$this->get_uId();
		$data['flash']=$this->flash();
		$data['uri']=$this->uri_segment();
		
		$this->load->helper('form');
		$data['res']=$this->m->get_score($jresID,$job_id,$userId);
		$this->header();
		
		$this->load->view('edit',$data);
	}
	
	function update_sc(){
		$str=urlencode($_POST['content']);
	 	$ex=explode('%0A',$str);
	 	$total=count($ex);
		 for($a=0;$a <= $total-1;$a++){
		 	$url[]=urldecode($ex[$a]);
		 }
		 print_r($url);
	}
	
	function update_score(){
		if($_POST){
			 	$str=urlencode($_POST['content']);
			 	$ex=explode('%0A',$str);
			 	$total=count($ex);
			 for($a=0;$a <= $total-1;$a++){
			 	$url=urldecode($ex[$a]);
			 }
			 	exit;
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
			exit;
			redirect('score_board/change_field/sboard/'.$target_id);
		}
		$q=$this->m->get_all_job($id);//get_all_job([parameters])
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
		$this->m->save_post_period($target_id);
		//redirect('score_board/targets');
	}
	
	function save_post_target($target_id=0){
		if($_POST){
			$name=$this->input->post('tname');
			$this->m->save_post_target($name,$target_id);
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
				$this->m->update_job($job,$job_id);
			}else{
				$job_data[]=$job;
			}
		 }
//		 print_r($job_data);
			$this->create_new_job($target_id,$job_data);

		}else{echo 'error';exit;}
		//redirect('score_board/targets/'.$target_id);
		
	}
	
	function create_new_job($target_id,$job_data){
		if(!empty($job_data)){
			$date=explode('|',$this->input->post('date_period'));
			$start=$date[0];
			$finish=$date[1];
			$userId=$this->get_uId();
			$this->m->create_data($userId,'',$target_id,$start,$finish,$job_data);
		}
	}
	
	function log($bagian,$value){
		print_r(br().'['.$bagian.'<>'.$value.']'.br());
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
		 $this->m->delete_job($job_id,$target_id);
		 //redirect('score_board/scoreBoard/'.$target_id);
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
			$this->m->edit_target();
			redirect('score_board/targets');
		}
		if(!isset($_POST['check'])){echo 'tidak ada field yang akan di edit!!!';exit;}
		$post=$this->input->post('check');
		foreach($post as $sc_id){
		 $data_form[]=$this->m->get_sch_datas($sc_id);
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
	
	function get_period($jId){
		$data=$this->m->get_period($jId);
		return $data;
	}
	
	function getjobRes($jobId){
		$data=$this->m->getJobRes($jobId);
		return $data;
	}
	
	function get_total_contents($id){
		return $this->m->get_total_contents($id);
	}
	
}
