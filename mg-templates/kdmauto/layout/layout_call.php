<?php 
    $sql = "SELECT * FROM `".PREFIX."page` WHERE title='call'";
    $res = DB::query($sql);
    while ($row = DB::fetchRow($res)) { ?>
<div class="bg-form-wh">
 	 <div class="container-wrap"> 
        <div class="form-wh-tittle">
        	<?php echo html_entity_decode($row[6]); ?>
        </div>
        <div class="form-wh-desk">
        	<?php echo html_entity_decode($row[8]); ?>
        </div>
        <?php echo $row[5]; ?>
     </div>
 </div>
    <?php }
?>