<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Status extends CI_Controller {
    public function __construct()
    {
         parent::__construct();
         //constructor code
         $this->load->helper(array('url','html','smiley'));
         $this->load->library(array('form_validation','table'));
    }
    function index(){
        $data['title'] = 'Status smiley';
        //untuk smiley
        $image_array = get_clickable_smileys(base_url().'assets/images/smileys/', 'status_text');
        $col_array = $this->table->make_columns($image_array, 16);
        $sub_data['smiley_table'] = $this->table->generate($col_array);
        $sub_data['status_result'] = '';
        //
        if($this->input->post('submit')) {
            $status = $this->input->post('status_text');
            $status_smiley = parse_smileys($status,base_url().'assets/images/smileys/');
            $sub_data['status_result'] = $status_smiley;
        }
        $data['body'] = $this->load->view('status_form', $sub_data);
        $this->load->view('output_html', $data);
    }
}
