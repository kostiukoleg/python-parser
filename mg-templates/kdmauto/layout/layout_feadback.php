<?php 
    $sql = "SELECT * FROM `".PREFIX."page` WHERE title='feadback'";
    $res = DB::query($sql);
    while ($row = DB::fetchRow($res)) { ?>
<div id="block45">
  <div class="container-wrap"> 
    <div class="feadback-tittle"><?php echo html_entity_decode($row[6]); ?></div>
    <a class="feadback" target="blank" href="<?php echo html_entity_decode($row[6]); ?>"><?php echo html_entity_decode($row[8]); ?></a>

    <div class="row-feadback">
      <?php echo html_entity_decode($row[5]); ?>
    </div>
  </div>
</div>
    <?php }
?>