<?php

/* @var $product Product */
$this->title = strlen($product->getName())>54?$product->getName():$product->getName().' - Domstroy.com.ua';
$this->description = 'Заказать -'.$product->getName().' в городе ';
if ($product->company['locale_c']) {
    $this->description .= CHtml::encode(CompanyLocale::getById($product->company['locale_c'])->title);
}
$this->description .= '. Большой выбор услуг, приятные цены.';
$this->keywords = $product->getMetaKeywords();
$this->canonical = $product->getMetaCanonical();

$mainAssets = Yii::app()->getModule('store')->getAssetsUrl();
Yii::app()->getClientScript()->registerScriptFile($mainAssets . '/js/jquery.simpleGal.js');

Yii::app()->getClientScript()->registerCssFile(Yii::app()->getTheme()->getAssetsUrl() . '/css/store-frontend.css');
Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getTheme()->getAssetsUrl() . '/js/store.js');

$this->breadcrumbs = array_merge(
    $product->category ? $product->category->getBreadcrumbs(true) : [0=>''],
    [CHtml::encode($product->name)]
);
Yii::app()->clientScript->registerMetaTag('product',null,null,array('property'=>"og:type"));
Yii::app()->clientScript->registerMetaTag(CHtml::encode($product->getTitle()),null,null,array('property'=>"og:title"));
Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo . Yii::app()->request->requestUri,null,null,array('property'=>"og:url"));
Yii::app()->clientScript->registerMetaTag(StoreImage::product($product,200,200),null,null,array('property'=>"og:image"));
Yii::app()->clientScript->registerMetaTag(strip_tags($product['description']),null,null,array('property'=>"og:description"));
Yii::app()->clientScript->registerMetaTag((($product->currency) ? round($product->currency->value * $product->getBasePrice(), 2) : round($product->getBasePrice(), 2)),null,null,array('property'=>"product:price:amount"));
Yii::app()->clientScript->registerMetaTag('UAH',null,null,array('property'=>"product:price:currency"));
?>
<html prefix=
    "og: http://ogp.me/ns#
     fb: http://ogp.me/ns/fb#
     product: http://ogp.me/ns/product#">
<?php
    echo "<script type=\"application/ld+json\">";
    echo "{";
    echo "\"@context\": \"http://schema.org/\",";
    echo "\"@type\": \"Product\",";
    echo "\"name\": \"".CHtml::encode($product->getName())."\",";
    if (!empty($product->attributes['image'])){
        echo "\"image\": \"".StoreImage::product($product)."\",";
    }
    $description_json =  preg_replace("/[^\p{L}0-9 ]/ui", "",strip_tags($product['description']));
    echo "\"url\": \"https://domstroy.com.ua/product/".$product->slug.".html\",";
    echo "\"description\": \"".$description_json."\",";
    echo "\"offers\": {";
    echo "\"@type\": \"Offer\",";
    echo "\"priceCurrency\": \"UAH\",";                     
    if ($product->price>0) {
      if ($product->currency) {
        echo "\"price\": \"".round($product->currency->value * $product->price, 2)."\",";
      } else {
        echo "\"price\": \"".round($product->price, 2)."\",";
      }
    } else {
        echo "\"price\": \"0 грн\",";
    }   
    echo "\"itemCondition\": \"new\"";
    echo "}";
    echo "}";
    echo "</script>";
?>
<div class="container">
    <section class="">
        <div class="row">
            <div class="col-xs-12">
                <h1><?= CHtml::encode($product->getTitle()); ?></h1>
            </div>
            <div class="col-md-9 col-sm-8 col-xs-12">
                <div class="row">
                    <div class="col-lg-5 col-md-12 col-sm-12">
                        <?php $this->widget('application.modules.favorite.widgets.FavoriteControl',['product'=>$product,'view'=>'favorite']); ?>
                        <div class="product-gallery">
                            <div class="product-gallery-main">
                                <a class="gallery-item" href="<?=StoreImage::product($product); ?>">
                                    <img 
                                        src="<?= StoreImage::product($product);?>"
                                        alt="<?= CHtml::encode($product->getImageAlt()); ?>"
                                        title="<?= CHtml::encode($product->getImageTitle()); ?>"
                                        style="height:300px;"
                                    >
                                </a>
                                <?php if ($product->is_special>0) {echo '<div><div class="mark mark-order" style="position: absolute;top: 5%;left: -1%; background-color: red; color:white; padding: 10px 29px 8px;">Акция</div></div>';}; ?>
                            </div>
                            <div class="product-gallery-all">
                                <?php foreach ($product->getImages() as $key => $image): { ?>
                                    <a href="<?= $image->getImageUrl(); ?>" class="gallery-item">
                                        <img src="<?= $image->getImageUrl(); ?>"
                                             alt="<?= CHtml::encode($image->alt) ?>"
                                             title="<?= CHtml::encode($image->title) ?>" />
                                    </a>
                                <?php } endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12 col-sm-12">
                        <div class="product-describe">

                        <br><br><span class="mark mark-order"><?=Yii::t('site','Под заказ')?></span>
                        <div class="materials-text">
                            <input type="hidden" id="base-price" value="<?= round($product->getResultPrice(), 2); ?>"/>
                            <p class="price">
                                <?php
                                    if ($product->getBasePrice()>0) {
                                        if ($product->currency) {
                                            echo round($product->currency->value * $product->getBasePrice(), 2);
                                        } else {
                                            echo round($product->getBasePrice(), 2);
                                        }
                                        echo ' ' .
                                        Yii::t("StoreModule.store", Yii::app()->getModule('store')->currency);
                                        if (Measurements::model()->findByPk($product->measure)) {
                                            echo ' ' . Measurements::model()->findByPk($product->measure)->name;
                                        }
                                    } else {
                                        echo 'Договорная';
                                    }
                                ?>
                            </p>
                            <p>
                                <?=Yii::t('site','Город')?> <span>
                                <?php
                                if ($product->company['locale_c']) {
                                    echo CHtml::encode(CompanyLocale::getById($product->company['locale_c'])->title);
                                }
                                ?>
                                </span>
                            </p>
                            <!-- <p class="views"><?php //echo $product->viewed? $product->viewed:0?></p> -->
                        </div>
                    </div>
                    </div>
                </div>
                <?php $this->widget('application.modules.store.widgets.ProductSocialWidget',
                    ['assets'=>$this->mainAssets, 'uploadsAsset'=>$this->uploadsAsset]);?>
                <div class="tab">
                    <p class="h3"><?=Yii::t('site','Описание')?></p>
                    <?php
                    echo '<p>';
                    if ($product['description']) {
                        //echo strip_tags($product['description'], '<p><span><br><ul><ol><li>');
                    }
                    echo $product->getDescription();
                    echo '</p>';
                    ?>
                    <ul class="nav" id="myTab">
                        <?php if ($product->getAttributeGroups()): ?>
                            <li>
                                <a href="#description" data-toggle="tab"><?= Yii::t("StoreModule.store", "Характеристики"); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php /*if (Yii::app()->hasModule('comment')): */?><!--
                            <li><a href="#comments-tab" data-toggle="tab"><?/*= Yii::t("StoreModule.store", "Video"); */?></a></li>
                        --><?php /*endif; */?>
                    </ul>
                    <div class="tab-content">
                        <div class="properties">
                            <?php foreach ($product->getAttributeGroups() as $groupName => $items): { ?>
                                <div class="propertyGroup">
                                    <h4>
<!--                                        <span><?/*= CHtml::encode($groupName); */?></span>
-->                                    </h4>
                                    <table>
                                        <tbody>
                                        <?php
                                        foreach ($items as $attribute): {
                                            $value = $product->attribute($attribute);

                                            if (empty($value)) continue;
                                            ?>
                                            <tr>
                                                <td class="key">
                                                    <span><?= CHtml::encode($attribute->title); ?></span>
                                                </td>
                                                <td class="value">
                                                    <?= AttributeRender::renderValue($attribute, $product->attribute($attribute)); ?>
                                                </td>
                                            </tr>
                                        <?php } endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->widget('application.modules.store.widgets.ShopWidget',
                    ['assets'=>$this->mainAssets, 'uploadsAsset'=>$this->uploadsAsset, 'company'=>$product->company]);?>
        </div>
    </section>
</div>
<section class="section-color">
    <?php $this->widget('application.modules.company.widgets.AnotherCompanyProductsWidget',
        [
            'company'=>$product->company->id,
            'type' => 1,
            'text' => Yii::t('site','Другие товары продавца'),
        ]); ?>
    <?php $this->widget('application.modules.company.widgets.AnotherCompanyProductsWidget',
        [
            'company'=>$product->company->id,
            'type' => 2,
            'text' => Yii::t('site','Другие услуги продавца'),
        ]); ?>
</section>
<section>
    <?php $this->widget('application.modules.viewed.widgets.ViewedWidget',[
        'text' => Yii::t('site','Вы просматривали'),
    ]);?>
</section>
    <div class="col-sm-12">
        <?php $this->widget('application.modules.store.widgets.LinkedProductsWidget', ['product' => $product, 'code' => null,]); ?>
    </div>
</div>

<?php Yii::app()->getClientScript()->registerScript(
    "product-images",
    <<<JS
        //$(".thumbnails").simpleGal({mainImage: "#main-image"});
        $("#myTab li").first().addClass('active');
        $(".tab-pane").first().addClass('active');
JS
);
Yii::app()->getClientScript()->registerScriptFile('/js/product_fix.js');
?>