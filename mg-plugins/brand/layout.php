<div class="mg-brand-block" id="brand-slider">
    <?php if (!empty($brand)) : ?>
    <div class="m-p-products-slider">
        <div class="<?php echo count($brand) > 4 ? "m-p-products-slider-start" : "" ?>">
      <?php foreach ($brand as $value) : ?>
        <?php if ($value['url']) { ?>
          <div class="mg-brand-logo">
              <a href="<?php echo SITE.'/brand?brand='.urlencode($value['brand']) ?>">
                  <img src="<?php echo $value['url'] ?>" alt="<?php echo $value['brand']?>" title="<?php echo $value['brand']?>">
              </a>
          </div>
        <?php } ?>
      <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

