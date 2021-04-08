<ul class="products__box">
        <?php foreach($model as $key){?>
        <?php
            if (isset(CompanyLocale::model()->findByPk($key->locale_c)->title)) {
                $city = CompanyLocale::model()->findByPk($key->locale_c)->title;
            } else{
                $city = '';
            }
        ?>
        <li>
            <a class="products__cart" href="<?= Company::getSlug($key);?>">
                <div class="cart-top" style="background-image: url('<?php echo $key->owner->getAvatar(180,180); ?>');"></div>
                <div class="cart-bottom" style="position: relative;">
                    <span class="name"><?=$key['name']?></span>
                    <p class="price">
                        <?php echo $city; ?>
                    </p>
                </div>
            </a>
        </li>


            
        <?}?>
</ul>
