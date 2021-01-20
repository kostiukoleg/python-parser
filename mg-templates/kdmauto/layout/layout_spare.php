<?php 
    $sql = "SELECT * FROM `".PREFIX."page` WHERE title='spare'";
    $res = DB::query($sql);
    while ($row = DB::fetchRow($res)) { ?>
<div class="container-wrap detail-repair"> 
	<div class="tittle-blockar"><?php echo html_entity_decode($row[6]); ?></div>
	 <div class="zap-desk"><?php echo html_entity_decode($row[8]); ?></div>
	 <?php echo $row[5]; ?>
</div>
   <?php }
?>