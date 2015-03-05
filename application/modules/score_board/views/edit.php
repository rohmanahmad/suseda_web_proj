<?=form_open_multipart('score_board/update_score')?>
	<table class='table'>
	<?php
	$flash->set_flashdata('ID',$uri->segment(3));
	$flash->set_flashdata('last_url',$uri->uri_string());
	if(isset($res)){
	if (count($res) == 0) exit;
	 foreach($res as $r){
	 	$uId=$r->uId;
	 	$jId=$r->jId;
	 	$tId=$r->tId;
	 	$jname=$r->jName;
	 	$url=$r->url;
		if(empty($url))$url='[]';
	 	$date=$r->date;
	 	$tname=$r->tName;
	 	$contents=json_decode($url,FALSE);
	 }
	}else exit;
	?>
<?php
//print_r(count($contents));
?>
	 <tr>
	  <td>Nama Target</td>
	  <td><?=strtoupper($tname)?></td>
	  <td></td>
	 </tr>
	 <tr>
	  <td>Nama Job</td>
	  <td colspan=2><?=strtoupper($jname)?></td>
	 </tr>
	 <tr>
	  <td>Tanggal</td>
	  <td colspan=2><?php 
	  	$date=new DateTime($date);
	  	$date= date_format($date,'d-M-Y');
	  	echo $date;
	  	?></td>
	 </tr>
	 <tr>
	  <td>Results</td>
	  <td>
	  	<div class="row" id='row1'>
	  <?php
	  	if(count($contents) > 0)
		  	foreach($contents as $c){
		  	 if(substr($c->url,0,4)=='http') echo '
				  <div style="margin:2px;">'.form_input('content[]',$c->url,'style="width:100%"').'</div>
		  		';
		  	}
		 
		// if empty
		  echo '
				  <div style="margin:2px;">'.form_input('content[]','','style="width:100%"').'</div>
		  	';
	  ?>
		</div>
	  </td>
	  <td><a class='btn btn-default' onclick='add_itm_input()'>Add Field</a></td>
	 </tr>
	 <tr>
	  <td></td>
	  <td>
	  	<div id='row2'>
	  <?php
	  	$id_c=0;
	  	if(count($contents) != 0)
	  	foreach($contents as $c){
	  	 $ex=explode('/',str_replace(array('http://','file://'),'',$c->url));
	  	 $index=count($ex)-1;
	  	 if(substr($c->url,0,4)=='file') 
	  	 	echo form_hidden('hidden[]',$ex[$index])
	  	 	.'<div class="row" >
	  	 		<div class="col-md-6">'.img(array('src'=>'assets/uploads/score_board/'.$ex[$index],'style'=>'width:40px;')).'</div>
			  	<div class="col-md-4">'.form_checkbox('delete[]',$ex[$index]).' Delete</div>
			  </div>';
	  	}
	  	$id_c++;
	  	echo '
			  <div style="margin:2px;">'.form_upload('userfile[]','','style="width:100%"').'</div>
			
	  	';
	  ?>
		</div>
	  </td>
	  <td><a class='btn btn-default' onclick='add_itm_upload()'>Add Field</a></td>
	 </tr>
	 <tr>
	  <td></td>
	  <td colspan=2><?=form_submit('submit','Update','class="btn btn-primary"')?></td>
	 </tr>
	</table>
<?=form_close()?>
<script src="<?=base_url('assets/js/jquery.js')?>"></script>
<script type="text/javascript">
function add_itm_input() {
    var div = $('<div style="margin:2px;"></div>');
    var input = $('<input name="content[]" type="text" style="width:100%">');
    
	$('#row1').append(div);
	$(div).append(input);
}
</script>

<script type="text/javascript">
function add_itm_upload() {
    var div = $('<div style="margin:2px;"></div>');
    var input = $('<input type="file" name="userfile[]" value="" style="width:100%">');
    
	$('#row2').append(div);
	$(div).append(input);
}
</script>

