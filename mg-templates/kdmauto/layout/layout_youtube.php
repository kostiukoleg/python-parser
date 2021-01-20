<?php 
    $sql = "SELECT * FROM `".PREFIX."page` WHERE title='youtube'";
    $res = DB::query($sql);
    while ($row = DB::fetchRow($res)) { ?>
<div class="container-tittle">
    <a class="marker-youtube" href="https://www.youtube.com/channel/UCSv8lG9EX7TOuMo8n6gFyyQ/videos">youtube.com/seekracer</a>

    <div class="tittle-youtube">
        <?php echo html_entity_decode($row[6]); ?>
    </div>

    <div class="deskription-youtube">
        <?php echo html_entity_decode($row[8]); ?>
    </div>
</div>
<div class="container-yotube-bg">
    <?php echo $row[5]; ?>
</div>
   <?php }
?>