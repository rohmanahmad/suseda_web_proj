<?php
if(!isset($fields))exit;
?>
<?=form_open()?>
<table class='table table-hover' border=0>
 <tr>
  <th>Nama Job</th>
  <th>Tanggal</th>
  <th>Jumlah</th>
 </tr>
<?php
$jobname='';
foreach($fields as $r){
 $id=$r->ID;
 $date=$r->date;
 $count=$r->count;
 $jname=$r->jobname;
 if($jobname!==$r->jobname){
	  echo '<tr bgcolor="#C0C0C0">
	  	 <td colspan=3> </td>
	  	</tr>';
	  $jobname=$r->jobname;
	}else{
		
	}
	
?>
 <tr>
  <td><?php echo ucwords($jname);//if($print==true){echo ucwords($jobname);}?></td>
  <td><?=$date.form_hidden('id[]',$id)?></td>
  <td><?=form_input(array('name'=>'count[]','value'=>$count,'type'=>'number'))?></td>
 </tr>
 <?php
 }
 ?>
 <tr>
  <td colspan=2></td>
  <td><?=form_submit('submit','Simpan','class="btn btn-primary"').' '.form_reset('reset','Reset','class="btn btn-warning"').' '.form_button('button','Cancel','class="btn btn-danger"')?></td>
 </tr>
</table>
<?=form_close()?>
