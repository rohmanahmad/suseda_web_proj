<?php if(!isset($result))exit;
$flash->set_flashdata('id',$ID);
if($name=='period' )$action=site_url('score_board/change_field/period');
if($name=='tname' )$action=site_url('score_board/change_field/tname');
if($name=='sboard' )$action=site_url('score_board/change_field/job');
?>
<form name="f_change_field" action="<?=$action?>" method="POST">
<div class='clearfix'>
	<table class="table" width='50%'>
	<?php 
	$n=1;
	$first=true;
	foreach($result as $result){
	 $ID=$result->ID;
	 $tname=$result->target_name;
	 $period_=str_replace('::',' - ',str_replace('-','/',$result->period));
	 $job_id=$result->job_id;
	 $jname=$result->job_name;
	  if($first == true){
		 $period_=explode(' - ',$period_);
		 for($a=0;$a<=count($period_)-1;$a++){
		 	 $d=new dateTime($period_[$a]);
			 $date[]=date_format($d,'Y-m-d');
		 }
	  }
	 
	      if($n == 1)
		if($name=='period' ){ 
		 $periode=form_input(array('type'=>'date','name'=>'start_period','value'=>$date[0],'id'=>'datePicker'))
		  	.br(2)
		  	.form_input(array('type'=>'date','name'=>'end_period','value'=>$date[1],'id'=>'datePicker'));
		$n=2;
		}
		if($name =='tname'){
		 $tname=form_input('tname',$tname,'style="width:100%"');
		}
	 if($name =='sboard' ){
	 	if($first == true){
			$date_period=$date[0].'|'.$date[1];
			$periode=str_replace('|',' - ',str_replace('-','/',$date_period));
	 		$jobname[]=form_hidden('date_period',$date_period)
	 			.form_hidden('jid[]',$job_id).form_input('job[]',$jname,'style="width:90%;margin-top:2px;" ')
	 			.form_button('','Add','class="btn btn-default" style="margin-left:5px;" onclick="add_field();"');
	 	}else{
	 		$jobname[]=form_hidden('jid[]',$job_id).form_input('job[]',$jname,'style="width:100%;margin-top:2px;" ');
	 	}
	 }else{
	 	$jobname[]=strtoupper($jname);
	 }
	$first=false;
	} ?>
	 <tr>
	  <td style='width:150px'>Target Name</td>
	  <td><?=$tname?></td>
	 </tr>
	 <tr>
	  <td>Periode</td>
	  <td><?php if(isset($periode))echo $periode;?></td>
	 </tr>
	 <tr>
	  <td>Score Board</td>
	  <td id="job"><?php 
	  	if(isset($jobname)){
	  	foreach($jobname as $jobs){echo $jobs.br();}
	  	}?></td>
	 </tr>
	 <tr>
	  <td></td>
	  <td><?=form_submit('submit','Change','class="btn btn-primary"')?></td>
	 </tr>
	 
	</table>
</div>
</form>
<script src="<?=base_url('assets/js/jquery.js')?>"></script>
<script type="text/javascript">
function add_field() {
    var fokus = $("<input type='hidden' name='jid[]' value=''><input type='text' name='job[]' style='width:100%;margin-top:2px;'>");
    
     $('#job').append(fokus);
}
</script>
