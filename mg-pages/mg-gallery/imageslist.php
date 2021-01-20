<div id="mg-gallery"> <!-- в этом блоке будут содержаться картинки -->
  <ul class="mg-gallery-list">
    <?php
    foreach ($data['items'] as $cell=>$image) {
      $nomargin = '';
      if (($cell+1)%$options['line_count'] == 0) {
        $nomargin = 'nomargin';
      }
      ?>
      <li style="width:<?php echo 100/$options['line_count']-2.5 ?>%;height:<?php echo $options['height'] ?>px;">
        <a class="pic" href="<?php echo $data['path'].$image['url']?>" rel="gallery" title="<?php echo $image['title']?>" target="_blank">
          <img src="<?php echo $data['path'].$image['url']?>" title="<?php echo $image['title']?>" alt="<?php echo $image['title']?>"/>
        </a>
      </li>
    <?php } ?>
  </ul>
</div>
