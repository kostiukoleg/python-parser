<?php
mgSEO($data['category']);
if (!class_exists('Blog')) {
  echo "Плагин не подключен!";
  return false;
}?>
<div class="main-block">
<h1 class="newsheader">Новости нашей компании</h1>
<a href="<?php echo SITE . $data['category']['url'] . "rss"; ?>" title="rss" class="rss">Подписаться на RSS</a>
<div class="main-news-block">    
      <?php foreach ($data["entity"] as $arItem):?>
    <div class="main-news-item">      
      <h2 class="news-title">
          <a href="<?php echo SITE . $arItem['path']; ?>"><?php echo $arItem['title']; ?></a>
      </h2>
      <div class="news-date"><?php echo $arItem['date_active_from']; ?></div>
      <div class="clear"></div>
      <?php if ($arItem['image_url']): ?>
      <a href="<?php echo SITE . $arItem['path']; ?>" class="main-news-img">
        <img src="<?php echo SITE . $data['img_path'] . 'thumbs/30_' . $arItem['image_url']; ?>" alt="<?php echo $arItem['title']; ?>" title="<?php echo $arItem['title']; ?>">
      </a>
    <?php endif; ?>
      <p class="news-main-desc"> <?php echo $arItem['previewText']; ?> </p>
      <?php if(!empty($arItem['tags'])):?>
      <div class="tags">
        Теги:
        <?php foreach($arItem['tags'] as $cell=>$tag){
          if($cell > 0) 
            echo ', ';
          echo '<a href="'.$tag['url'].'">'.$tag['value'].'</a>';
        }?>
      </div>
      <?php endif;?>
      <a href="<?php echo SITE . $arItem['path']; ?>" class="read-more">Читать всю новость →</a>
      <div class="clear"></div>
    </div>
  <?php endforeach;?>
  <?php echo $data['pagination'];?>
</div>                    
</div>