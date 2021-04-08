<?php

//fix cash

if ($product->currency) {
	$product->currency->value;
}

/* @var $product Product */
// Service meta
//var($product->sup_type);
if($product->sup_type == 2) {

	if ((isset($product->meta_description))&&(!empty($product->meta_description))) {
		$this->description = $product->meta_description;
	}
	else{
		$this->description = 'Заказать ';
		$this->description .= CHtml::encode($product->getName());
		if ($product->company['name']) {
			$this->description .= ' от компании ';
			$this->description .= $product->company['name'];
		}
		$this->description .= ' ДомСтрой Строительный портал №1 в Украине';
	}
	if ((isset($product->meta_title))&&(!empty($product->meta_title))) {
		$this->title = $product->meta_title;
	}
	else{
		if ($product->getBasePrice() > 0) {
			if (isset(Measurements::model()->findByPk($product->measure)->name)) {

				$this->title = 'Заказать '.CHtml::encode((strlen($product->getMetaTitle())>54?$product->getMetaTitle():$product->getMetaTitle())). ' цена '.$product->getBasePrice().' грн'.Measurements::model()->findByPk($product->measure)->name;
			}
			else{
				$this->title = 'Заказать '.CHtml::encode((strlen($product->getMetaTitle())>54?$product->getMetaTitle():$product->getMetaTitle())). ' цена '.$product->getBasePrice().' грн ';
			}

		}
		else{
			if (isset(Measurements::model()->findByPk($product->measure)->name)) {
			$this->title = 'Заказать '.CHtml::encode((strlen($product->getMetaTitle())>54?$product->getMetaTitle():$product->getMetaTitle())). ' цена договорная ';
			}
		}

		if (!empty($product->company['locale_c'])) {
			$this->title .= CHtml::encode(CompanyLocale::getById($product->company['locale_c'])->title);
		}
	}
		if ((isset($product->meta_keywords))&&(!empty($product->meta_keywords))) {
			$this->keywords = $product->meta_keywords;
		}
		else{
			if ($product->getBasePrice()>0) {
				$this->keywords = CHtml::encode($product->getName());
			}
			else{
				$this->keywords = CHtml::encode($product->getName());
			}
		}

} else {
// Product meta

	//Вытягиваем характеристики для описания
	if ($product->getAttributeGroups()){
		$attributes_1 = $product->getAttributeGroups();
		foreach ($attributes_1 as $group => $ite) {
			foreach ($ite as $val) {
				if ($val->title == 'Производитель') {
					//Вытягиваем значение атрибута
					$brand = Product::model()->getValueAttribute($val->id, $product->id);
				}
				if ($val->title == 'Страна производитель') {
					$brand_country = Product::model()->getValueAttribute($val->id, $product->id);
				}
				if ($val->title == 'Ширина') {
					$width_unit = $val->unit;
					$width = Product::model()->getValueAttributeNumber($val->id, $product->id);
				}
				if ($val->title == 'Длина') {
					$length_unit = $val->unit;
					$length = Product::model()->getValueAttributeNumber($val->id, $product->id);
				}
				if ($val->title == 'Толщина') {
					$thickness_unit = $val->unit;
					$thickness = Product::model()->getValueAttributeNumber($val->id, $product->id);
				}
				if ($val->title == 'Высота') {
					$height_unit = $val->unit;
					$height = Product::model()->getValueAttributeNumber($val->id, $product->id);
				}
				if ($val->title == 'Диаметр') {
					$diametr_unit = $val->unit;
					$diametr = Product::model()->getValueAttributeNumber($val->id, $product->id);
				}
			}
		}
	}

	//Вытягиваем цепочку категорий
	$parent_id_start = $product->category_id;
	$list_category = array();
	while ($parent_id_start != 0) {
		$categories_list = Product::model()->getCategoriesLink($parent_id_start);
		$list_category[] = $categories_list[0]['name'];
		$parent_id_start = $categories_list[0]['parent_id'];
	}
	if ((isset($product->meta_title))&&(!empty($product->meta_title))) {
		$this->title = $product->meta_title;
	}
	else{
		if ($product->getBasePrice() > 0) {
			if (isset(Measurements::model()->findByPk($product->measure)->name)) {
				$this->title = 'Купить '.CHtml::encode((strlen($product->getMetaTitle())>54?$product->getMetaTitle():$product->getMetaTitle())). ' цена '.$product->getBasePrice().' грн'.Measurements::model()->findByPk($product->measure)->name;
			}
			else{
				$this->title = 'Купить '.CHtml::encode((strlen($product->getMetaTitle())>54?$product->getMetaTitle():$product->getMetaTitle())). ' цена '.$product->getBasePrice().' грн ';
			}
		}
		else{
			$this->title = 'Купить '.CHtml::encode((strlen($product->getMetaTitle())>54?$product->getMetaTitle():$product->getMetaTitle())). ' цена договорная ';
		}

		if (!empty($product->company['locale_c'])) {
			$this->title .= CHtml::encode(CompanyLocale::getById($product->company['locale_c'])->title);
		}
	}

	if ((isset($product->meta_description))&&(!empty($product->meta_description))) {
		$this->description = $product->meta_description;
	}
	else{
		$this->description = CHtml::encode((strlen($product->getMetaTitle())>54?$product->getMetaTitle():$product->getMetaTitle()));
		//$brand = 0;
		if (isset($brand)) {
			if ($brand) {
				$this->description .= ', '.$brand;
			}
		}
		if (isset($brand_country)) {
			if ($brand_country) {
				$this->description .= ', '.$brand_country;
			}
		}
		if (isset($width)) {
			if ($width) {
				$this->description .= ', ширина '.$width.' '.$width_unit;
			}
		}
		if (isset($height)) {
			if ($height) {
				$this->description .= ', высота '.$height.' '.$height_unit;
			}
		}
		if (isset($length)) {
			if ($length) {
				$this->description .= ', длина '.$length.' '.$length_unit;
			}
		}
		if (isset($thickness)) {
			if ($thickness) {
				$this->description .= ', толщина '.$thickness.' '.$thickness_unit;
			}
		}
		if (isset($diametr)) {
			if ($diametr) {
				$this->description .= ', диаметр '.$diametr.' '.$diametr_unit;
			}
		}
		if (isset($list_category)) {
			foreach ($list_category as $cat) {
				$this->description .= ', '.$cat;
			}
		}
		if (!empty($product->company['locale_c'])) {
			$this->description .= ', с доставкой в город '.CHtml::encode(CompanyLocale::getById($product->company['locale_c'])->title);
		}

		$this->description .= ' – ДомСтрой Строительный портал №1 в Украине ';
	}
	if ((isset($product->meta_keywords))&&(!empty($product->meta_keywords))) {
		$this->keywords = $product->meta_keywords;
	}
	else{
		if ($product->getBasePrice()>0) {
			$this->keywords = CHtml::encode($product->getName());
		}
		else{
			$this->keywords = CHtml::encode($product->getName());
		}
	}
}

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
Yii::app()->clientScript->registerMetaTag(CHtml::encode(str_replace(array("'",'"'),' ',$product->company['name']).' '.$product->getTitle()),null,null,array('property'=>"og:title"));
Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo . Yii::app()->request->requestUri,null,null,array('property'=>"og:url"));
Yii::app()->clientScript->registerMetaTag(StoreImage::product($product,200,200),null,null,array('property'=>"og:image"));
Yii::app()->clientScript->registerMetaTag(strip_tags(str_replace(array("'",'"'),' ',$product->company['name']).' '.$product['description']),null,null,array('property'=>"og:description"));
Yii::app()->clientScript->registerMetaTag((($product->currency) ? round($product->currency->value * $product->getBasePrice(), 2) : round($product->getBasePrice(), 2)),null,null,array('property'=>"product:price:amount"));
Yii::app()->clientScript->registerMetaTag('UAH',null,null,array('property'=>"product:price:currency"));

?>
<html prefix=
  "og: http://ogp.me/ns#
   fb: http://ogp.me/ns/fb#
   product: http://ogp.me/ns/product#">
<?php
$user = Yii::app()->getUser()->getProfile();
if (isset($user->id)) {
    $id_user = $user->id;
}
else{
    $id_user = 0;
}
?>
<?php if (($id_user == 1761)||($id_user == 10)||($id_user == 2562)): ?>
<?php
$form = $this->beginWidget(
  '\yupe\widgets\ActiveForm',
  [
    'id' => 'update_metatag',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => 'vertical',
    'action' => Yii::app()->createUrl('/user/product/metatagedit/'),
    'htmlOptions' => ['enctype' => 'multipart/form-data', 'class' => ''],
    'clientOptions' => [
      'validateOnSubmit' => false,
    ],
  ]
); 
?>
 	<style>
.materials-text .describe{
  display: none;
}
</style>
  <div class="row" id="block_meta_tag" style="display:none; padding-bottom: 20px; border-bottom: 2px solid #000">

    <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
    <input type="hidden" name="product_url" value="<?php echo $product->slug; ?>">
    <input type="hidden" name="product_type" value="<?php echo $product->sup_type; ?>">
      <div class="col-sm-1">
      </div>
      <div class="col-sm-10">
          <div class="row">
              <div style="text-align: right;">
                  <img style="width:40px;height: 40px;cursor: pointer;" src="../images/close-button.png" onclick="CloseBlockEditMeta()">
              </div>
          </div>
          <div class="row">
              <label>Мета тег TITLE</label>
              <input type="text" name="meta_title" value="<?php echo $product->meta_title; ?>">
          </div>
          <div class="row">
              <label>Мета тег KEYWORDS</label>
              <input type="text" name="meta_keywords" value="<?php echo $product->meta_keywords; ?>">
          </div>
          <div class="row">
              <label>Мета тег H1</label>
              <input type="text" name="meta_h1" value="<?php echo $product->title; ?>">
          </div>
          <div class="row">
              <label>Мета тег DESCRIPTION</label>
              <textarea rows="10" name="meta_description"><?php echo $product->meta_description; ?></textarea>
          </div>
          <div class="row" style="margin-top:20px;">
              <div style="text-align: right;">
                <?php $this->widget(
                  'bootstrap.widgets.TbButton',
                  [
                    'buttonType' => 'submit',
                    'label' => 'Сохранить',
                  ]
                ); ?>
              </div>
          </div>
      </div>
      <div class="col-sm-1">
      </div>
  </div>
<?php $this->endWidget(); ?>
<?php endif ?>
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
  <div class="row">

	  <div class="col-xs-12">
<?php if((isset($product->title))&&(!empty($product->title))){ ?>
	<h1 id="name_product_s" data-name-product="<?= ($product->sup_type == 2) ? (CHtml::encode($product->getName()).($product->company['locale_c'] ? ' в городе '.CHtml::encode(CompanyLocale::getById($product->company['locale_c'])->title) : '')) : (CHtml::encode($product->getTitle())); ?>"><?php echo $product->title; ?></h1>
<?php } else{ ?>
	    <h1 id="name_product_s" data-name-product="<?= ($product->sup_type == 2) ? (CHtml::encode($product->getName()).($product->company['locale_c'] ? ' в городе '.CHtml::encode(CompanyLocale::getById($product->company['locale_c'])->title) : '')) : (CHtml::encode($product->getTitle())); ?>"><?= ($product->sup_type == 2) ? (CHtml::encode($product->getName()).($product->company['locale_c'] ? ' в городе '.CHtml::encode(CompanyLocale::getById($product->company['locale_c'])->title) : '')) : (CHtml::encode($product->getTitle())); ?></h1>
<?php } ?>
	  </div>
	  <div class="col-md-9 col-sm-8 col-xs-12">
	    <div class="row">
	    	<div class="col-lg-5 col-md-12 col-sm-12">
	      	<!--<?php $this->widget('application.modules.favorite.widgets.FavoriteControl',['product'=>$product,'view'=>'favorite']); ?>-->
			<!-- Внедряем галерею -->
			<div id="gallery" style="margin: 0 auto; text-align: center;">
				<?php if (!empty($product->attributes['image'])){ ?>
					<img style="width:auto; max-width:100%;height: 350px;padding-bottom: 26px;" src="<?=StoreImage::product($product); ?>" alt="<?php echo $this->title; ?>" id="main-img" />
				<?php } else { ?>
					<img style="width:auto; max-width:100%;height: 350px;padding-bottom: 26px;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png" alt="" id="main-img" />
				<?php } ?>
				<ul style="padding-left: 10px;">
				<?php if (!empty($product->attributes['image'])){ ?>
				  <li style="display: inline; margin-right: 3px;"><img style="width:20%; margin-left: 3%; float:left; height: 45px;" src="<?=StoreImage::product($product); ?>" alt="<?php echo $this->title; ?>_1" /></li>
				<?php } ?>
				<?php if (!empty($product->attributes['image1'])){ ?>
				  <li style="display: inline; margin-right: 3px;"><img style="width:20%; margin-left: 3%; float:left; height: 45px;" src="https://domstroy.com.ua/uploads/store/product/<?php echo $product->attributes['image1'];?>" alt="<?php echo $this->title; ?>_2" /></li>
				<?php } ?>
				<?php if (!empty($product->attributes['image2'])){ ?>
				  <li style="display: inline; margin-right: 3px;"><img style="width:20%; margin-left: 3%; float:left; height: 45px;" src="https://domstroy.com.ua/uploads/store/product/<?php echo $product->attributes['image2'];?>" alt="<?php echo $this->title; ?>_3" /></li>
				<?php } ?>
				<?php if (!empty($product->attributes['image3'])){ ?>
				  <li style="display: inline; margin-right: 3px;"><img style="width:20%; margin-left: 3%; float:left; height: 45px;" src="https://domstroy.com.ua/uploads/store/product/<?php echo $product->attributes['image3'];?>" alt="<?php echo $this->title; ?>_4" /></li>
				<?php } ?>
				</ul>
		    </div>

			<!-- Внедряем галерею -->
		    </div>
		    <div class="col-lg-7 col-md-12 col-sm-12">
		      <div class="product-describe">
		      	<span class="mark <?=($product->sup_type === '1' && $product->in_stock)?"mark-approved":"mark-order"?>"><?=($product->sup_type === '1' && $product->in_stock)?"В наличии":"Под заказ"?></span>
		      	<div class="materials-text">
			        <input type="hidden" id="base-price" value="<?= round($product->getResultPrice(), 2); ?>"/>
			        <p id="price_tovar_s" class="price">
				        <?php
				          if ($product->price>0) {
					          if ($product->currency) {
					            echo round($product->currency->value * $product->price, 2);
					          } else {
					            echo round($product->price, 2);
					          }
							  echo ' ' . Yii::t("StoreModule.store", Yii::app()->getModule('store')->currency);
					          if (Measurements::model()->findByPk($product->measure)) {
					            echo '' . Measurements::model()->findByPk($product->measure)->name;
							  }
							  echo '<p id="update_time" style="float:left;">Цена актуальная ' . preg_replace('/\W\d{2}:\d{2}:\d{2}/', '',$product->update_time) . '</p>';
				          } else {
				          echo 'Договорная';
				          }
				        ?>
	        		</p>
                    <p id="old_price_tovar_s" class="price">
                        <?php
                        if ($product->old_price>0) {
                            if ($product->currency) {
                                echo round($product->currency->value * $product->old_price, 2);
                            } else {
                                echo round($product->old_price, 2);
                            }
                            echo ' ' . Yii::t("StoreModule.store", Yii::app()->getModule('store')->currency);
                            if (Measurements::model()->findByPk($product->measure)) {
                                echo '' . Measurements::model()->findByPk($product->measure)->name;
                            }
                        }
                        ?>
                    </p>
                    <div style="clear: both"></div>
			        <?php
			        if ($product->company['locale_c']) {
			        echo '<p>'.Yii::t('CompanyModule.company', 'city').' <span>';
			        echo CHtml::encode(CompanyLocale::getById($product->company['locale_c'])->title);
			        echo '</span></p>';
			        }

			        ?>
                    <?php if ($product->sup_type == 1): ?>
						<?php if($product->company['plan'] !== '5' && $product->company['plan'] !== '6' && $product->company['plan'] !== '21'): ?>
                    	<!-- Блок вывода телефона для продуктов -->
                    	<div style="width: 100%;background-color: #EFF2FA;padding: 5px;margin-bottom: 10px;">
                    		<div style="width: 100%; margin-bottom: 10px;">
                    			<span style="color:#003366;font-weight: bold;">Контакты продавца</span>
                    		</div>
								<!--Выводим номер телефона основной-->
								<?php if($phoneindyvidual['phone']!=''){ ?>
									<div id="phone00" style="width: 100%; margin-bottom: 10px;cursor: pointer;" onClick="ModalPhone(this)" data-numberc="0" data-company="<?php echo $product->company['id']; ?>">
										<span id="phone10" style="color:#003366;float: left;" data-phone="<?php echo $phoneindyvidual['phone']; ?>"><?php echo substr_replace($phoneindyvidual['phone'],'xxx-xx-xx', 8); ?>&nbsp;</span>
										<span id="phone20" style="color:#003366; border-bottom: 1px dotted;"> показать</span>
										<a href="tel:<?php echo $phoneindyvidual['phone']; ?>">
											<span id="phone30" style="color:#003366;float: left;display: none; cursor: default; width:100%; margin-bottom: 10px;"></span>
										</a>
										<div id="block-phone0" style="display: none;">
											<span class="ph01" id="phone40" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;">
											</span>
											<a class="ph02" href="tel:<?php echo $phoneindyvidual['phone']; ?>">
												<span id="phone50" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;"></span>
											</a>
										</div>
									</div>
								<?php } ?>
								<!--Выводим номер телефона основной-->
                    		<?php
                    		$i = 1;
                    		foreach ($phone as $c) {
	                    		if ($c!='' && $i !== 1){ ?>
	                    		<div id="phone0<?php echo $i; ?>" style="width: 100%; margin-bottom: 10px;cursor: pointer;" onClick="ModalPhone(this)" data-numberc="<?php echo $i; ?>" data-company="<?php echo $product->company['id']; ?>">
	                    			<span id="phone1<?php echo $i; ?>" style="color:#003366;float: left;" data-phone="<?php echo $c; ?>"><?php echo substr_replace($c,'xxx-xx-xx', 8); ?>&nbsp;</span>
	                    			<span id="phone2<?php echo $i; ?>" style="color:#003366; border-bottom: 1px dotted;"> показать</span>
	                    			<a href="tel:<?php echo $c; ?>">
	                    				<span id="phone3<?php echo $i; ?>" style="color:#003366;float: left;display: none; cursor: default; width:100%; margin-bottom: 10px;"></span>
	                    			</a>
	                    			<div id="block-phone<?php echo $i; ?>" style="display: none;">
	                    				<span class="ph01" id="phone4<?php echo $i; ?>" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;">
	                    				</span>
	                    				<a class="ph02" href="tel:<?php echo $c; ?>">
	                    					<span id="phone5<?php echo $i; ?>" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;"></span>
	                    				</a>
	                    			</div>
	                    		</div>
	                    		<?php
	                    		
                    		} else if($c!='' && $i === 1) { ?>
								<a id="viber" href="viber://chat?number=<?php 
								$d = str_replace("+","%2B",$c);
								$d = str_replace(["(", ")", " ", "-"], "", $d); 
								echo $d;
								?>">
								<img src="/images/viber-logo.png">
								</a>
							<?php } $i++; } ?>
                    	</div>
						<?php endif; ?>
                        <button class="add-to-cart" onclick="cart.add(<?= $product->id; ?>); return false;">Купить</button>
                        <input type="hidden" id="price_for_kredit" value="<?php echo $product->price; ?>">
			<p><?php $this->widget('application.modules.callback.widgets.CallbackWidget'); ?></p>



                    <?php elseif ($product->sup_type == 2): ?>
						<?php if($product->company['plan'] !== '5' && $product->company['plan'] !== '6' && $product->company['plan'] !== '21'): ?>
                    	<!-- Блок вывода телефона для услуг -->
                    	<div style="width: 100%;background-color: #EFF2FA;padding: 5px;margin-bottom: 10px;">
                    		<div style="width: 100%; margin-bottom: 10px;">
                    			<span style="color:#003366;font-weight: bold;">Контакты исполнителя</span>
                    		</div>
                    		<!--Выводим номер телефона основной-->
                    		<?php if($phoneindyvidual['phone']!=''){ ?>
	                    		<div id="phone00" style="width: 100%; margin-bottom: 10px;cursor: pointer;" onClick="ModalPhone(this)" data-numberc="0" data-company="<?php echo $product->company['id']; ?>">
	                    			<span id="phone10" style="color:#003366;float: left;" data-phone="<?php echo $phoneindyvidual['phone']; ?>"><?php echo substr_replace($phoneindyvidual['phone'],'xxx-xx-xx', 8); ?>&nbsp;</span>
	                    			<span id="phone20" style="color:#003366; border-bottom: 1px dotted;"> показать</span>
	                    			<a href="tel:<?php echo $phoneindyvidual['phone']; ?>">
	                    				<span id="phone30" style="color:#003366;float: left;display: none; cursor: default; width:100%; margin-bottom: 10px;"></span>
	                    			</a>
	                    			<div id="block-phone0" style="display: none;">
	                    				<span class="ph01" id="phone40" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;">
	                    				</span>
	                    				<a class="ph02" href="tel:<?php echo $phoneindyvidual['phone']; ?>">
	                    					<span id="phone50" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;"></span>
	                    				</a>
	                    			</div>
	                    		</div>
                    		<?php } ?>
                    		<!--Выводим номер телефона основной-->
                    		<?php
                    		$i = 1;
                    		foreach ($phone as $c) {
					if($c!='' && $i === 1){ ?>
					<a id="viber" href="viber://chat?number=<?php 
					$d = str_replace("+","%2B",$c);
					$d = str_replace(["(", ")", " ", "-"], "", $d); 
					echo $d;
					?>">
					<img src="/images/viber-logo.png">
					</a>	
					<?php	} 
	                    		if ($c!='' && $i !== 1){ ?>
	                    		<div id="phone0<?php echo $i; ?>" style="width: 100%; margin-bottom: 10px;cursor: pointer;" onClick="ModalPhone(this)" data-numberc="<?php echo $i; ?>" data-company="<?php echo $product->company['id']; ?>">
	                    			<span id="phone1<?php echo $i; ?>" style="color:#003366;float: left;" data-phone="<?php echo $c; ?>"><?php echo substr_replace($c,'xxx-xx-xx', 8); ?>&nbsp;</span>
	                    			<span id="phone2<?php echo $i; ?>" style="color:#003366; border-bottom: 1px dotted;"> показать</span>
	                    			<a href="tel:<?php echo $c; ?>">
	                    				<span id="phone3<?php echo $i; ?>" style="color:#003366;float: left;display: none; cursor: default; width:100%; margin-bottom: 10px;"></span>
	                    			</a>
	                    			<div id="block-phone<?php echo $i; ?>" style="display: none;">
	                    				<span class="ph01" id="phone4<?php echo $i; ?>" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;">
	                    				</span>
	                    				<a class="ph02" href="tel:<?php echo $c; ?>">
	                    					<span id="phone5<?php echo $i; ?>" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;"></span>
	                    				</a>
	                    			</div>
	                    		</div>
	                    		<?php
                    			} 
				 $i++;
                    		} ?>
                    	</div>
						<?php endif; ?>
                        <a href="/order-service?service_id=<?php echo $product->id; ?>" class="order-service" style="margin-top: 20px;">Заказать</a>
			<p><?php $this->widget('application.modules.callback.widgets.CallbackWidget'); ?></p>
                    <?php endif; ?>
	        		<!-- <p class="views"><?php //echo $product->viewed? $product->viewed:0?></p> -->
	      		</div>
	      	</div>
	    	</div>
	    </div>
	    <?php $this->widget('application.modules.store.widgets.ProductSocialWidget',
	    ['assets'=>$this->mainAssets, 'uploadsAsset'=>$this->uploadsAsset]);?>
	    <div class="tab">
	      <ul class="nav" id="myTab">
	        <li>
	        	<a href="#description" data-toggle="tab">Описание</a>
	        </li>
	 	<?php
			$data = Company::model()->findByPk($product->company['id']);
			if (count($data["payment_methods_id"])>0) {
	        ?>
	        <li>
	        	<a href="#payment_methods" data-toggle="tab">Способы оплаты</a>
	        </li>
	        	<?php } 
	        	if (count($data["delivery_methods_id"])>0) { ?>
			<li>
	        	<a href="#delivery_methods" data-toggle="tab">Способы доставки</a>
	        </li>
	    		<?php } ?>
		<?php if ($product->sup_type === '1' && $product->getAttributeGroups()): ?>
		        <li>
		        	<a href="#attributes" data-toggle="tab"><?= Yii::t("StoreModule.store", "Характеристики"); ?></a>
		        </li>
	      	<?php endif; ?>
	      	<?php
	      		if ($product->video) {
	      			echo '<li><a href="#video" data-toggle="tab">';
	      			echo Yii::t("StoreModule.store", "Video");
	      			echo '</a></li>';
	      		}
	      		//if (!Plan::model()->findByPk($product->company->plan)->is_base && $product->video) {
	      		//	echo '<li><a href="#video" data-toggle="tab">';
	      		//	echo Yii::t("StoreModule.store", "Video");
	      		//	echo '</a></li>';
	      		//}
	      	?>
	      </ul>
	      <div class="tab-content">
	      		<?php 
	      			$mob_desc = '';
	      			if ($product['description']) {
		      			$mob_desc = strip_tags($product['description']);
		      			$mob_desc = substr($mob_desc, 0, 300);
		      			$mob_desc = rtrim($mob_desc, "!,.-");
		      			$mob_desc = substr($mob_desc, 0, strrpos($mob_desc, ' '));
		      			$mob_desc .= '...';
		      		}
	      		?>
	      		<div id="mobile_descr">
	      			<?php echo '<p>';
	      			echo $mob_desc;
	      			echo '</p>'; ?>
	      		</div>

	      		<div id="open_block_one" onclick="openmobile()">Подробнее</div>
		    	<div id="description" role="tabpanel" class="tab-pane">
			      <?php
			        $description = '';
				    echo '<p>';
				    if ($product['description']) {
				      $description = preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', '', $product['description']);
				    }
				    //echo strip_tags($description, '<p>, <h2>');
				    $description = $product->getDescription();
				    $description = preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', '', $description);
				    echo $description;
				    //echo $product->getDescription();
				    echo '</p>';
				    ?>
		    	</div> 
			<script>
			(function(){if(+screen.width<=568 && +screen.width>=320){
				document.getElementById('description').style.display='none';
				}
			})();
			function openmobile(str){
   			    document.getElementById('mobile_descr').style.display='none'; 
    			    document.getElementById('open_block_one').style.display='none';
    			    document.getElementById('description').style.display = 'block'; 
			};
			</script>                                                                            
			<div id="payment_methods" role="tabpanel" class="tab-pane">
		    	<?php 
				$payment_methods = explode(",", $data["payment_methods_id"]);
				foreach($payment_methods as $id){
					$name = CompanyPaymentMethods::model()->findByPk($id);
					print_r("<p>".$name["name"]."</p>");
				}
				?>
			</div>
		    <div id="delivery_methods" role="tabpanel" class="tab-pane">
	    		<?php 
				$payment_methods = explode(",", $data["delivery_methods_id"]);
				foreach($payment_methods as $id){
					$name = CompanyDeliveryMethods::model()->findByPk($id);
					print_r("<p>".$name["name"]."</p>");
				}
				?>
		    </div>
		    	<?php 

		    	if ($product->sup_type === '1' && $product->getAttributeGroups()): ?>
		      	<div id="attributes" role="tabpanel" class="tab-pane properties">
		        	<?php foreach ($product->getAttributeGroups() as $groupName => $items): { ?>
			        	<div class="propertyGroup">
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
	    		<?php endif; ?>
	    		<?php
	    		
	      		//if (!Plan::model()->findByPk($product->company->plan)->is_base && $product->video) {
	    		if ($product->video) {
	      			echo '<div id="video" role="tabpanel" class="tab-pane">';
	      			echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$product->video.'" frameborder="0" allowfullscreen></iframe>';
	      			echo '</div>';
	      		}
	      	?>
	      </div>
	    </div>
	  </div>
	  <?php $this->widget('application.modules.store.widgets.ShopWidget',
	    ['assets'=>$this->mainAssets, 'uploadsAsset'=>$this->uploadsAsset, 'company'=>$product->company]);?>
  </div>
</div>
<section class="section-color">
  <?php 
  $this->widget('application.modules.company.widgets.AnotherCompanyProductsWidget',
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
<?php
$value = StoreCategory::model()->getCategoryData($product->category['parent_id']);
if(is_array($value) && !empty($value) && $product->category['id']!==$value[0]['id']):
?>
<section style="padding:0;">
<div class="container">
<div class="row">
<h3>Возможно вас заинтересует</h3>
<?php
echo '<div class="container" style="margin-bottom:50px;">';
$i = 0;
foreach ($value as $v) {
    if($product->category['id'] !== $v['id']){
	if ($i%4===0) {
            echo '<div class="row">';
        } 
    ?>
    <div class="col-md-3"><a href="/<?php echo $v['slug']; ?>"><?php echo $v['name']; ?></a></div>
    <?php
if ($i%4===3||$i===count($value)-1) {
            echo '</div>';
      }
      $i++; 
    }
}
echo '</div>';
?>
</div>
</div>
</section>
<?php endif; ?>
<?php
$pp_id = StoreCategory::model()->find(
    'id = :id',
    [
	':id' => $product->category['parent_id']
    ]
)?: '';
if(is_numeric($pp_id['parent_id']) && !empty($pp_id)){
$pp_value = StoreCategory::model()->getCategoryData($pp_id['parent_id']);
if(is_array($pp_value)):
?>
<section style="padding:0;">
<div class="container">
<div class="row">
<h3>У нас покупают</h3>
<?php
echo '<div class="container" style="margin-bottom:50px;">';
$i = 0;
foreach ($pp_value as $v) {
    if($pp_id['id'] !== $v['id']){ 
        if ($i%4===0) {
            echo '<div class="row">';
        }
      ?>
      <div class="col-md-3"><a href="/<?php echo $v['slug']; ?>"><?php echo $v['name']; ?></a></div>
      <?php
      if ($i%4===3||$i===count($pp_value)-1) {
            echo '</div>';
      }
      $i++; 
    }
}
echo '</div>';
?>
</div>
</div>
</section>
<?php endif; } ?>
<section style="width: 100%;overflow: hidden;">
  <?php $this->widget('application.modules.viewed.widgets.ViewedWidget',[
  'text' => Yii::t('site','Вы просматривали'),
  ]);?>
</section>
<div class="col-sm-12">
  <?php $this->widget('application.modules.store.widgets.LinkedProductsWidget', ['product' => $product, 'code' => null,]); ?>
</div>
<?php //$this->widget('application.modules.seo.widgets.SeoBlockCategoryLinksWidget', ['categories' => [$product->category_id]]); ?>
<?php
Yii::app()->getClientScript()->registerScript(
  "product-images",
  <<<JS
  //$(".thumbnails").simpleGal({mainImage: "#main-image"});
  $("#myTab li").first().addClass('active');
  $(".tab-pane").first().addClass('active');
JS
);
Yii::app()->getClientScript()->registerScriptFile(Yii::app()->createUrl('/js/product_fix.js'));
Yii::app()->getClientScript()->registerScriptFile(Yii::app()->createUrl('/js/domstroy_2018.js'));
?>
<div id="Modal1" class="modalDialog1">
	<div>
		<img id="img_cancel" style="width:18px; position: absolute; right: 5px; cursor: pointer;" src="/images/button_cancel.png" onclick="CloseModal1(this)" alt="Закрыть окно">
		<p style="color: #003366;text-align: center;line-height: 1.1; font-weight: bold; font-size: 18px;" id="name_product_w"></p>
		<span>Цена: </span><span style="color:#009900;" id="price_m"></span>
		<span id="name_shop_m" style="font-weight: bold;"></span>
		<span style="margin-top:15px; color:#003366; font-size:12px;">Пожайлуста, скажите продавцу, что Вы нашли это обьявление на Строительном портале ДомСтрой</span></br>
		<a id="clicktel">
			<p id="h4_phonemodal" style="text-align: center;color: #003366; cursor: default;"></p>
		</a>
		<p id="h5_phonemodal" style="text-align: center;color: #003366; cursor: default;"></p>
		<div style="margin-top: 30px;">
			<div class="btn btn-primary btn-block-back" id="success" data-company="<?php echo $product->company['id']; ?>" onclick="CloseModal1(this)" style="background-color: #009900; font-size: 14px;">
				Успешный звонок
			</div>
			<div class="btn btn-primary btn-block-back" id="unsuccess" data-company="<?php echo $product->company['id']; ?>" onclick="CloseModal1(this)" style="background-color: #990000;float:right;font-size:14px;">
				Не дозвонился
			</div>
		</div>
	</div>
</div>

<script>
// prepare the form when the DOM is ready
$(document).ready(function() {
	$("#gallery li img").hover(function(){
		$('#main-img').attr('src',$(this).attr('src').replace('thumb/', ''));
	});
	var imgSwap = [];
	 $("#gallery li img").each(function(){
		imgUrl = this.src.replace('thumb/', '');
		imgSwap.push(imgUrl);
	});
	$(imgSwap).preload();
});
$.fn.preload = function() {
    this.each(function(){
        $('<img/>')[0].src = this;
    });
}
</script>
<div>