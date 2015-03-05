<?php
if (!isset($result)) exit;
?>
<table class='table'>
 <tr>
  <th>No</th>
  <th>Periode</th>
  <th>Target</th>
  <th></th>
 </tr>
 <?php
 $i=1;
  foreach($result as $r){
   $id=$r->ID;
   $period=$r->period;
   $target=$r->target_name;
 ?>
 <tr>
  <td><?=$i?></td>
  <td><?php
  	$start=date_format(date_create(explode('::',$period)[0]),'d/M/Y');
  	$finish=date_format(date_create(explode('::',$period)[1]),'d/M/Y');
  	$period=$start.' - '.$finish;
  	echo $period;
  	?></td>
  <td><?=$target?></td>
  <td><?=anchor(site_url('score_board/scoreBoard/'.$id),'<span class="glyphicon glyphicon-list" title="view"></span>').' | '.
  	anchor(site_url('score_board/delete_target/'.$id),'<span class="glyphicon glyphicon-trash" title="delete"></span>')?></td>
 </tr>
 <?php
  $i++;
  }
 ?>
</table>

