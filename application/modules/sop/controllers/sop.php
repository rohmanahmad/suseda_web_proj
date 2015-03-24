<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sop extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->helper(array('url','html'));
		$this->load->model('sop_model','m');
	}
 
	function header(){
	 	$this->load->view('main/sop_header');
	}
	
	function get_uId(){
		return 1;
	}
	
	function flash(){
		$this->load->library('session');
		return $this->session;
	}
	
	function view($filename,$parse,$header=false){
		if($header == true) $this->header();
		$this->load->view($filename,$parse);
	}
	
	function index(){
		$this->home();
	}
	
	#...........................................
	
	function home(){
		$data['title']='home';
		$this->m->get_sop_datas();
		$this->view('sop',$data,true);
	}
	
}
