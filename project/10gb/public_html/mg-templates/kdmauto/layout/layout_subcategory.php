<?php 
$lotteautoauction = DB::query("SELECT value FROM mg_product_user_property WHERE property_id=166 AND value LIKE '%lotteautoauction%' ORDER BY product_id DESC ");
$lotteautoauction_row = DB::fetchArray($lotteautoauction);
$lotteautoauction_url = urlencode($lotteautoauction_row['value']);

$sellcarauction = DB::query("SELECT value FROM mg_product_user_property WHERE property_id=166 AND value LIKE '%sellcarauction%' ORDER BY product_id DESC");
$sellcarauction_row = DB::fetchArray($sellcarauction);
$sellcarauction_url = urlencode($sellcarauction_row['value']);

$glovisaauction = DB::query("SELECT value FROM mg_product_user_property WHERE property_id=166 AND value LIKE '%glovisaauction%' ORDER BY product_id DESC");
$glovisaauction_row = DB::fetchArray($glovisaauction);
$glovisaauction_url = urlencode($glovisaauction_row['value']);
?>
<?php if(!empty($data)): ?>
<ul class="sub-categories">
    <li>
        <a href="/catalog?prop%5B166%5D%5B%5D=<?php echo $sellcarauction_url; ?>&applyFilter=1&filter=1" class="cat-image" id="sellcarauction"><img src="/uploads/cat_sellcarauction.png" alt="sellcarauction" title="sellcarauction"></a>
        <a href="/catalog?prop%5B166%5D%5B%5D=<?php echo $sellcarauction_url; ?>&applyFilter=1&filter=1" class="sub-cat-name">Sell Car Auction</a>
        <ul class="sub-categories" style="display:none;">
          <?php foreach($data as $category): ?>    
            <?php if (empty($category['insideProduct'])) { continue;} ?>      
            <li>
              <?php if(!empty($category['image_url'])): ?>
                <a href="<?php echo SITE.'/'.$category['parent_url'].$category['url'].'?prop%5B166%5D%5B%5D='.$sellcarauction_url.'&applyFilter=1&filter=1'; ?>" class="cat-image">
                  <img src="<?php echo SITE.$category['image_url']; ?>" alt="<?php echo $category['title']; ?>" title="<?php echo $category['title']; ?>">
                </a>
              <?php else: ?>
                <a href="<?php echo SITE.'/'.$category['parent_url'].$category['url'].'?prop%5B166%5D%5B%5D='.$sellcarauction_url.'&applyFilter=1&filter=1'; ?>" class="cat-image">
                  <img src="<?php echo SITE.'/uploads/thumbs/70_no-img.jpg' ?>" alt="<?php echo $category['title']; ?>" title="<?php echo $category['title']; ?>">
                </a>
              <?php endif; ?>

              <a href="<?php echo SITE.'/'.$category['parent_url'].$category['url'].'?prop%5B166%5D%5B%5D='.$sellcarauction_url.'&applyFilter=1&filter=1'; ?>" class="sub-cat-name"><?php echo $category['title']; ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
    </li>
    <li>
        <a href="/catalog?prop%5B166%5D%5B%5D=<?php echo $lotteautoauction_url; ?>&applyFilter=1&filter=1" class="cat-image" id="lotteautoauction"><img src="/uploads/cat_lotteautoauction.png" alt="lotteautoauction" title="lotteautoauction"></a>
        <a href="/catalog?prop%5B166%5D%5B%5D=<?php echo $lotteautoauction_url; ?>&applyFilter=1&filter=1" class="sub-cat-name">Lotte Auto Auction</a>
        <ul class="sub-categories" style="display:none;">
          <?php foreach($data as $category): ?>    
            <?php if (empty($category['insideProduct'])) { continue;} ?>      
            <li>
              <?php if(!empty($category['image_url'])): ?>
                <a href="<?php echo SITE.'/'.$category['parent_url'].$category['url'].'?prop%5B166%5D%5B%5D='.$lotteautoauction_url.'&applyFilter=1&filter=1'; ?>" class="cat-image">
                  <img src="<?php echo SITE.$category['image_url']; ?>" alt="<?php echo $category['title']; ?>" title="<?php echo $category['title']; ?>">
                </a>
              <?php else: ?>
                <a href="<?php echo SITE.'/'.$category['parent_url'].$category['url'].'?prop%5B166%5D%5B%5D='.$lotteautoauction_url.'&applyFilter=1&filter=1'; ?>" class="cat-image">
                  <img src="<?php echo SITE.'/uploads/thumbs/70_no-img.jpg' ?>" alt="<?php echo $category['title']; ?>" title="<?php echo $category['title']; ?>">
                </a>
              <?php endif; ?>

              <a href="<?php echo SITE.'/'.$category['parent_url'].$category['url'].'?prop%5B166%5D%5B%5D='.$lotteautoauction_url.'&applyFilter=1&filter=1'; ?>" class="sub-cat-name"><?php echo $category['title']; ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
    </li>
    <li>
        <a href="/catalog?prop%5B166%5D%5B%5D=<?php echo $glovisaauction_url; ?>&applyFilter=1&filter=1" class="cat-image" id="glovisaauction"><img src="/uploads/cat_glovisauction.png" alt="glovisaauction" title="glovisaauction"></a>
        <a href="/catalog?prop%5B166%5D%5B%5D=<?php echo $glovisaauction_url; ?>&applyFilter=1&filter=1" class="sub-cat-name">Glovis Auto Auction</a>
        <ul class="sub-categories" style="display:none;">
          <?php foreach($data as $category): ?>    
            <?php if (empty($category['insideProduct'])) { continue;} ?>      
            <li>
              <?php if(!empty($category['image_url'])): ?>
                <a href="<?php echo SITE.'/'.$category['parent_url'].$category['url'].'?prop%5B166%5D%5B%5D='.$glovisaauction_url.'&applyFilter=1&filter=1'; ?>" class="cat-image">
                  <img src="<?php echo SITE.$category['image_url']; ?>" alt="<?php echo $category['title']; ?>" title="<?php echo $category['title']; ?>">
                </a>
              <?php else: ?>
                <a href="<?php echo SITE.'/'.$category['parent_url'].$category['url'].'?prop%5B166%5D%5B%5D='.$glovisaauction_url.'&applyFilter=1&filter=1'; ?>" class="cat-image">
                  <img src="<?php echo SITE.'/uploads/thumbs/70_no-img.jpg' ?>" alt="<?php echo $category['title']; ?>" title="<?php echo $category['title']; ?>">
                </a>
              <?php endif; ?>

              <a href="<?php echo SITE.'/'.$category['parent_url'].$category['url'].'?prop%5B166%5D%5B%5D='.$glovisaauction_url.'&applyFilter=1&filter=1'; ?>" class="sub-cat-name"><?php echo $category['title']; ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
    </li>
</ul>
<?php endif; ?>
