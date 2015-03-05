<link rel="stylesheet" href="<?=base_url('assets/css/bootstrap.min.css')?>">
<table class='table'>
 <tr>
  <td><?=anchor(site_url('score_board/home'),'HOME')?></td>
  <td><?=anchor(site_url('score_board/targets'),'TARGET')?></td>
 </tr>
 <tr>
  <td colspan=2> reset table : 
  <?=anchor(site_url('score_board/reset_data/job'),'job')?> | 
  <?=anchor(site_url('score_board/reset_data/job_result'),'job_result')?> | 
  <?=anchor(site_url('score_board/reset_data/target'),'tergets')?> | 
  <?=anchor(site_url('score_board/reset_data/schedule'),'schedule')?></td>
 </tr>
</table>
