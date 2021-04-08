<?php

$user = User::model()->findByPk($data->owner_id);

?>
<div class="materials-box horizontal col-sm-4 <?=($data['is_top']?' top':'')?>">
    <a href="<?= Company::model()->getSlug($data);?>">
        <div class="materials-inner star-full">
            <?php if($data->plan == 9): ?>
                <div class="material-inner-vip-icon">

                </div>
            <?php endif; ?>
            <?php if($data['is_top']):?>
                <span class="partner"><?= Yii::t('buttons', 'partner');?></span>
            <?php endif ?>
            <div class="materials-img">
                <div class="img-wrap">
                    <?= CHtml::image($user->getAvatar(300,300), CHtml::encode($data->name),
                        ['style'=>'max-width: 170px; max-height: 170px;'])?>
                </div>
                <div class="raiting">
                <?php  
                    $raiting = round(CompanyReit::getReit($data['id'])/20);
                    for ($i=0; $i < $raiting; $i++) { 
                        echo '<div class="full"></div>';
                    }
                    for ($i=0; $i < 5-$raiting; $i++) { 
                        echo '<div class="empty"></div>';
                    }
                ?>
                </div>
            </div>
            <div class="materials-text clearfix">
                <p class="title"><?= CHtml::encode($data->name)?>
                    <span class="city">
                    <?php
                    if ($data->locale_c) {
                        echo CHtml::encode(CompanyLocale::getById($data->locale_c)->title);
                    }
                    ?>
                    </span></p>
                <div class="describe">
                    <?php
                    foreach (array($data['main_line'],$data['busyness_line1'],$data['busyness_line2']) as $item) {
                        if ($item) echo '<span style="color: black;"><br>'.'✔ '.StoreCategory::model()->findByPk($item)['name'] . '</span>';
                    }
                    echo '<br>'.strip_tags(\yupe\helpers\YText::wordLimiter($data['activities'], $limit = 100));
                    ?>
                </div>
                <br><br>
                <span class="show-all"><?= Yii::t('buttons', 'more')?></span>
            </div>
        </div>
    </a>
</div>