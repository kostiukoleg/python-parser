<div class="phone-col">
  <div class="phone-num">
    <span class="phone-ic"></span> 
       <div class="phone-mobile-class">
         <div class="btn-messeng">
         <a class="viber-item" href="viber://chat?number=<?php echo MG::getSetting('shopPhone') ?>"><img src="/uploads/ed3d55061da72e796ef8c7cd7cda22af.png"></a>
         <a class="whotapp-item" href="https://wa.me/<?php echo MG::getSetting('shopPhone') ?>/?text=urlencodedtext"><img src="/uploads/66b7b2f42d509e636c5560ae51bb130e.png"></a>
         </div>
        <a href="tel:<?php echo MG::getSetting('shopPhone') ?>"><?php echo MG::getSetting('shopPhone') ?></a>
          <?php if (class_exists('BackRing')): ?>
          [back-ring]
          <?php endif; ?>
       </div>
  </div>
</div>