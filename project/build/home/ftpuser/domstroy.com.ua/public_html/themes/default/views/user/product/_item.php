<?php
Yii::app()->getClientScript()->registerCssFile($this->mainAssets . '/css/bootstrap-touchspin.min.css');
Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/bootstrap-touchspin.min.js');
?>
<div class="user-cabinet-box add-product" style="position: relative;">
    <div class="check-box-mass-op" style="position: absolute;top: 10px;left: 10px; z-index: 10; width: 29px;">
        <input type="checkbox" class="product-mass-operations" data-id="<?=$data['id']?>">
    </div>
    <div class="user-cabinet-img">
        <span class="coints"><?=$data->top_level?></span>
        <img src="<?=StoreImage::product($data,190, 190, false); ?>" alt="">
    </div>
    <div class="user-cabinet-text">
    <ul>
        <li class="name" style="font-size: 14px;"><?=$data['name']?></li>
        <li class="price" style="font-size: 14px;">
        <?php
            echo number_format($data['price'], 2, ',', ' ');
            echo ' ';
            if ($data['currency']) {
                echo $data['currency']->name;
            } else {
                echo 'грн';
            }
            echo '/';
            if (Measurements::model()->findByPk($data->measure)) {
                echo Measurements::model()->findByPk($data->measure)->name;
            }
        ?>
        </li>
        <?php $publish_day = new DateTime($data['update_time']);?>
        <li class="minifyed-12"><?= Yii::t("site", "Дата публикации: "); ?> <span><?=$publish_day->format('d.m.Y H:i:s')?></span></li>
        <li class="minifyed-12"><?= Yii::t("site", "Дата окончания публикации: "); ?> <span></span></li>
        <li class="minifyed-12"><?= Yii::t("site", "Просмотры (день / неделя / месяц): "); ?> <span>
        <?php
        echo Viewed::countDayViews($data->id).' / '.Viewed::countWeekViews($data->id).' / '.Viewed::countMonthViews($data->id);
        ?>
        </span></li>
        <li><a target="_blank" style="color: darkgreen;" href="<?= ProductHelper::getUrl($data); ?>"><?= Yii::t("site", "Просмотреть на сайте"); ?></a></li>
    </ul>
</div>
    <div class="user-cabinet-rating">
    <?php $date = new DateTime($data['top_to']);?>
    <ul>
            <li style="margin-bottom: 15px;">
                <a class="onClickAction productToTop is_top-<?=$data['is_top']?>" data-id="<?=$data['id']?>" href=""><?php echo(!$data['is_top']?"<span style=\"color: red;\">Поднять в ТОП</span>":"В ТОП до: " . $date->format('d.m.Y H:i:s'))?></a>
                <?php if(!$data['is_top']): ?>
                    <div>
                        <form class="top-add-form">
                            <div class="row">
                                <div class="col-sm-6 text-center">
                                    <label class="minifyed" for="target_term">Дни</label>
                                    <input class="minifyed" type="text" data-touch="" name="target_term" value="7" min="1" max="365">
                                </div>
                                <div class="col-sm-6 text-center">
                                    <label class="minifyed" for="target_term">Топ</label>
                                    <input class="minifyed form-control" type="text" data-touch="" name="target_value" value="5" min="1" max="1000">
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif;?>
            </li>
        <?php if(isset($_GET['Product_page'])){
            $page = $_GET['Product_page'];
        } else{
            $page = 1;
        }
        ?>
        <?php if(Yii::app()->getUser()->id !== '1775'): ?>
            <li style="background: url(../images/pencil.png) left no-repeat;"><a href="<?=Yii::app()->createUrl('/user/product/update/' . $data['id'].'/'.$page)?>"><?= Yii::t("site", "Редактировать"); ?></a></li>
        <?php endif; ?>
        <li style="background: url(../images/del.png) left no-repeat;"><a href="<?=Yii::app()->createUrl('/user/product/dispublish/' . $data['id'])?>"><?= Yii::t("site", "В архив"); ?></a></li>
<li style="background: url(../images/top.png) left no-repeat;">
<?php if(Company::model()->findByPk($data['company_id'])->plan === '7' || Company::model()->findByPk($data['company_id'])->plan === '8' || Company::model()->findByPk($data['company_id'])->plan === '22' || Company::model()->findByPk($data['company_id'])->plan === '9'): ?>
        <a href="<?=Yii::app()->createUrl('/user/product/republish/' . $data['id'].'/'.$page)?>"><?= Yii::t("site", "Переопубликовать"); ?></a>
<?php else: ?>
        <a href="javascript:void(0);" id="free-republish" onclick="OpenFreeRepublisherModal();"><?= Yii::t("site", "Переопубликовать"); ?></a>
<?php endif; ?>
</li>
    </ul>
</div>
</div>