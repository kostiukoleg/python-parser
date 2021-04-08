<?php
$this->layout = '//layouts/product';
$this->breadcrumbs = [
  Yii::t('CompanyModule.company', 'Мастера') => ['/master'],
  $model->name,
];
$this->pageTitle = Yii::t('CompanyModule.company', 'Просмотр профиля мастера');
$this->pageTitle = Yii::t('CompanyModule.company', 'Компании - просмотр');
// Master meta
if($model->sup_type == 2) {
    $busyness_line = [];
    foreach (array($model['main_line'],$model['busyness_line1'],$model['busyness_line2']) as $item) {
      if ($item) {
        $item = StoreCategory::model()->findByPk($item);
        $busyness_line[] = $item['name'];
      }
    }
  $this->title = 'Мастер '.CHtml::encode($model->name).' - '.(sizeof($busyness_line) > 1 ? implode(',',$busyness_line) : $busyness_line[0]).' '.($model->locale_c ? CHtml::encode(CompanyLocale::getById($model->locale_c)->title) : '').'., рейтинг мастера '.CompanyReit::getReit($model->id).'%';
  $this->description = 'Строители города - '.($model->locale_c ? CHtml::encode(CompanyLocale::getById($model->locale_c)->title) : '').', '.CHtml::encode($model->name).' вам поможет! Рейтинг мастера '.CHtml::encode($model->name).' в категории '.(sizeof($busyness_line) > 1 ? implode(',',$busyness_line) : $busyness_line[0]).'- '.CompanyReit::getReit($model->id).'%, примеры работ на строительном портале Украины - ДомСтрой';
} else {
  $this->title = CHtml::encode($model->name).' - ДомСтрой';
  $this->description = 'Ищете мастера в г. ';
  if ($model->locale_c) {
    $this->description .= CHtml::encode(CompanyLocale::getById($model->locale_c)->title);
  }
  $this->description .= '? '.CHtml::encode($model->name).' вам поможет! Контакная информация, рейтинг и примеры работ на лучшем строительном портале Украины. ';
}

Yii::app()->getClientScript()->registerCssFile(Yii::app()->createUrl('/css/custom.css'));
Yii::app()->getClientScript()->registerCssFile(Yii::app()->createUrl('/css/materials.css'));

Yii::app()->clientScript->registerMetaTag('article',null,null,array('property'=>"og:type"));
Yii::app()->clientScript->registerMetaTag(CHtml::encode($model->name),null,null,array('property'=>"og:title"));
Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo . Yii::app()->request->requestUri,null,null,array('property'=>"og:url"));
Yii::app()->clientScript->registerMetaTag($model->owner->getAvatar(600,600),null,null,array('property'=>"og:image"));
Yii::app()->clientScript->registerMetaTag(strip_tags($model->activities),null,null,array('property'=>"og:description"));
?>
<section class="section-transparent">
  <div class="container">
    <div class="row" style="margin:70px 0px 30px;">
      <div class="col-sm-8">
        <h1 style="margin:0px;padding: 0px;">
          <?=  Yii::t('CompanyModule.company', '') . ' ' . Yii::t('CompanyModule.company', 'Просмотр профиля мастера '); ?>
          <?php
          echo '<br/>';
          echo '<span>';
          echo CHtml::encode($model->name);
          echo '</span>';
          ?>
        </h1>
      </div>
      <div class="col-sm-3 pull-right">
        <?php if($model->is_top): ?>
          <span class='mark mark-approved'>
              <?=Yii::t("CompanyModule.company", "partner") ?>
            </span>
        <?php endif ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-9 col-sm-7 col-xs-12">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-0">
          <div class="company-img star-empty ">
            <?= CHtml::image($model->owner->getAvatar(10000,10000), $model->getName(),['style' => 'max-height: 100%; max-width: 100%;'] )?>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-top-30">
          <div class="">
            <p>
                <?php $this->widget('application.modules.callback.widgets.CallbackWidget'); ?>
            </p>
            <?php
            if (!empty($model->companyContacts->website)){
              if ($model->plan == 9) {
                echo '<p> Сайт: <span  class="hlink" data-id="'.$model->id.'" data-href="'.base64_encode(CHtml::encode($model->companyContacts['website'])).'">';
                echo CHtml::encode($model->companyContacts->website);
                echo '</span></p>';
              }
              else{
                echo '<p> Сайт: <span>';
                echo CHtml::encode($model->companyContacts->website);
                echo '</span></p>';
              }
            }
            if ($model->locale_c) {
              echo '<p>'.Yii::t('CompanyModule.company', 'city').': <span>';
              echo CHtml::encode(CompanyLocale::getById($model->locale_c)->title);
              echo '</span></p>';
            } elseif ($model->locale_r) {
              echo '<p>'.Yii::t('CompanyModule.company', 'region').': <span>';
              echo CHtml::encode(CompanyLocale::getById($model->locale_r)->title);
              echo '</span></p>';
            }
            ?>
          </div>
          <?php if($model->plan == 9): ?><!-- Тут проверка плана -->
          <div>
            <h4><?= Yii::t('CompanyModule.company', 'phone')?></h4>
            <?php if((User::model()->findByPk($model->owner_id)->phone)&&(User::model()->findByPk($model->owner_id)->phone!='')){ ?>
              <div id="phone00" style="width: 100%; margin-bottom: 10px;cursor: pointer;display:block;" onClick="ModalPhone1(this)" data-numberc="0" data-suptype="master" data-company="<?php echo $model->id; ?>" data-companyname="<?php echo CHtml::encode($model->name); ?>">
                <span id="phone10" style="color:#003366;float: left;" data-phone="<?php echo User::model()->findByPk($model->owner_id)->phone; ?>"><?php echo substr_replace(User::model()->findByPk($model->owner_id)->phone,'xxx-xx-xx', 8); ?>&nbsp;</span>
                <span id="phone20" style="color:#003366; border-bottom: 1px dotted;"> показать</span>
                <a href="tel:<?php echo User::model()->findByPk($model->owner_id)->phone; ?>">
                  <span id="phone30" style="color:#003366;float: left;display: none; cursor: default; width:100%; margin-bottom: 10px;"></span>
                </a>
                <div id="block-phone0" style="display: none;">
                  <span class="ph01" id="phone40" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;">
                  </span>
                  <a class="ph02" href="tel:<?php echo User::model()->findByPk($model->owner_id)->phone; ?>">
                    <span id="phone50" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;"></span>
                  </a>
                </div>
              </div>
            <?php } ?>
            <?php if(($model->companyContacts['phone_2'])&&($model->companyContacts['phone_2']!='')){ ?>
              <a id="viber" href="viber://chat?number=<?php 
              $d = str_replace("+","%2B",$model->companyContacts['phone_2']); 
              $d = str_replace(["(", ")", " ", "-"], "", $d);
              echo $d; ?>">
              <img src="/images/viber-logo.png">
              </a>
              <!--<div id="phone01" style="width: 100%; margin-bottom: 10px;cursor: pointer;display:block;" onClick="ModalPhone1(this)" data-numberc="1" data-suptype="master" data-company="<?php echo $model->id; ?>" data-companyname="<?php echo CHtml::encode($model->name); ?>">
                <span id="phone11" style="color:#003366;float: left;" data-phone="<?php echo $model->companyContacts['phone_2']; ?>"><?php echo substr_replace($model->companyContacts['phone_2'],'xxx-xx-xx', 8); ?>&nbsp;</span>
                <span id="phone21" style="color:#003366; border-bottom: 1px dotted;"> показать</span>
                <a href="tel:<?php echo $model->companyContacts['phone_2']; ?>">
                  <span id="phone31" style="color:#003366;float: left;display: none; cursor: default; width:100%; margin-bottom: 10px;"></span>
                </a>
                <div id="block-phone1" style="display: none;">
                  <span class="ph01" id="phone41" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;">
                  </span>
                  <a class="ph02" href="tel:<?php echo $model->companyContacts['phone_2']; ?>">
                    <span id="phone51" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;"></span>
                  </a>
                </div>
              </div>-->
            <?php } ?>
            <?php if(($model->companyContacts['phone_3'])&&($model->companyContacts['phone_3']!='')){ ?>
              <div id="phone02" style="width: 100%; margin-bottom: 10px;cursor: pointer;display:block;" onClick="ModalPhone1(this)" data-numberc="2" data-suptype="master" data-company="<?php echo $model->id; ?>" data-companyname="<?php echo CHtml::encode($model->name); ?>">
                <span id="phone12" style="color:#003366;float: left;" data-phone="<?php echo $model->companyContacts['phone_3']; ?>"><?php echo substr_replace($model->companyContacts['phone_3'],'xxx-xx-xx', 8); ?>&nbsp;</span>
                <span id="phone22" style="color:#003366; border-bottom: 1px dotted;"> показать</span>
                <a href="tel:<?php echo $model->companyContacts['phone_3']; ?>">
                  <span id="phone32" style="color:#003366;float: left;display: none; cursor: default; width:100%; margin-bottom: 10px;"></span>
                </a>
                <div id="block-phone2" style="display: none;">
                  <span class="ph01" id="phone42" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;">
                  </span>
                  <a class="ph02" href="tel:<?php echo $model->companyContacts['phone_3']; ?>">
                    <span id="phone52" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;"></span>
                  </a>
                </div>
              </div>
            <?php } ?>
            <?php if(($model->companyContacts['phone_4'])&&($model->companyContacts['phone_4']!='')){ ?>
              <div id="phone03" style="width: 100%; margin-bottom: 10px;cursor: pointer;display:block;" onClick="ModalPhone1(this)" data-numberc="3" data-suptype="master" data-company="<?php echo $model->id; ?>" data-companyname="<?php echo CHtml::encode($model->name); ?>">
                <span id="phone13" style="color:#003366;float: left;" data-phone="<?php echo $model->companyContacts['phone_4']; ?>"><?php echo substr_replace($model->companyContacts['phone_4'],'xxx-xx-xx', 8); ?>&nbsp;</span>
                <span id="phone23" style="color:#003366; border-bottom: 1px dotted;"> показать</span>
                <a href="tel:<?php echo $model->companyContacts['phone_4']; ?>">
                  <span id="phone33" style="color:#003366;float: left;display: none; cursor: default; width:100%; margin-bottom: 10px;"></span>
                </a>
                <div id="block-phone3" style="display: none;">
                  <span class="ph01" id="phone43" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;">
                  </span>
                  <a class="ph02" href="tel:<?php echo $model->companyContacts['phone_4']; ?>">
                    <span id="phone53" style="color:#003366;float: left;width:100%; cursor: default; margin-bottom: 10px;"></span>
                  </a>
                </div>
              </div>
            <?php } ?>
          </div>
          <?php endif; ?>
          <div>
            <h4><?= Yii::t('CompanyModule.company', '')?></h4>
            <a class="a-red" href="mailto:<?= $model->owner->attributes['email']?>"><?= $model->owner->attributes['email']?></a>
          </div>
          <div id="company_view">
            <?php
            if ($model->companyContacts['facebook'] ||
              $model->companyContacts['instagram']) {
              echo '<h4>Мастер в соцсетях:</h4>';
            }
            if ($model->companyContacts['facebook']) {
              echo '<a href="'.$model->companyContacts['facebook'].'" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a>';
            }
            if ($model->companyContacts['instagram']) {
              echo '<a href="'.$model->companyContacts['instagram'].'" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a>';
            }
            ?>
          </div>
        </div>
      </div>
      <!--Рейтинг -->
      <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12">
        <div class="company-rating">
          <h4><?= Yii::t('CompanyModule.company', 'rating')?> <span class="mark mark-order">
          <?php
          echo '<span>'.CompanyReit::getReit($model->id).'</span>';
          ?>%
          </span></h4>
          <a href="javascript:PopUpShow('popup1')"><?= Yii::t('CompanyModule.company', 'grade')?></a>
          <hr />
          <div class="reiting-placeholder">
            <?php
              if (CompanyReit::getReitCategory($model->id)) {
                echo '<a href="javascript:PopUpShow(\'popup2\')" style="color: red;">';
                echo Yii::t('CompanyModule.company', 'Посмотреть рейтинг');
                echo '</a>';
              }
            ?>
          </div>
        </div>
        <?php $this->widget('application.modules.store.widgets.ProductSocialWidget', ['assets'=>$this->mainAssets, 'uploadsAsset'=>$this->uploadsAsset]);?>
      </div>
      <!--Рейтинг -->
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="tab js-company">
          <!-- Nav tabs -->
          <ul class="nav" >
            <li class="active">
              <a href="#description" data-toggle="tab"><?=Yii::t('CompanyModule.company', 'full description')?></a>
            </li>
            <li>
              <a href="#busyness_line" data-toggle="tab"><?=Yii::t('CompanyModule.company', 'busyness_line')?></a>
            </li>
          </ul>
          <!-- Tab panes -->
          <div class="tab-content">
			<?php 
	      			$mob_desc = '';
	      			if (!empty($model->activities)) {
		      			$mob_desc = strip_tags($model->activities, '<p><span><br><ul><ol><li>');
		      			$mob_desc = substr($mob_desc, 0, 300);
		      			$mob_desc = rtrim($mob_desc, "!,.-");
		      			$mob_desc = substr($mob_desc, 0, strrpos($mob_desc, ' '));
		      			$mob_desc .= '...';
		      		}
	      		?>
	      		<div id="mobile_descr">
	      			<?php 
				echo '<p>'.$mob_desc.'</p>';
	      		        ?>
	      		</div>
                        <div id="open_block_one" onclick="openmobile()" style="margin-bottom:15px">Подробнее</div>
            <div role="tabpanel" class="tab-pane active" id="description">
              <?php
              if (!empty($model->activities)) {
                echo '<meta content="' . strip_tags($model->activities) . '">';
                echo strip_tags($model->activities, '<p><span><br><ul><ol><li>');
              }
              ?>
            </div>
		<script>(function(){
		if(+screen.width<=568 && +screen.width>=320){
			document.getElementById('description').style.display='none';
		}
		})();
		function openmobile(str){
		document.getElementById('mobile_descr').style.display='none';
		document.getElementById('open_block_one').style.display='none';
		document.getElementById('description').style.display='block';
		};
	    </script>
            <div role="tabpanel" class="tab-pane" id="busyness_line">
              <?php
              foreach (array($model['main_line'],$model['busyness_line1'],$model['busyness_line2']) as $item) {
                if ($item) {
                  $item = StoreCategory::model()->findByPk($item);
                  echo '<a href="'.Yii::app()->createUrl($item['slug']).'"><p>'.'✔ '. $item['name'] . '</p></a>';
                }
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if($gallery !==null && $gallery->status > 0):?>
  <section class="section-color" style="width: 100%;overflow: hidden;">
    <div class="container">
      <div class="row">
        <?php $this->widget(
          'gallery.widgets.GalleryWidget',
          ['galleryId' => $gallery->id,'view'=>'gallery-index','gallery'=>$gallery]
        ); ?>
      </div>
    </div>
  </section>
<?php endif;?>

<section class="section">
  <?php $this->widget('application.modules.company.widgets.AnotherCompanyProductsWidget',
    [
      'company'=>$model->id,
      'type' => 1,
      'text' => Yii::t('site','product'),
    ]); ?>
  <?php $this->widget('application.modules.company.widgets.AnotherCompanyProductsWidget',
    [
      'company'=>$model->id,
      'type' => 2,
      'text' => Yii::t('site','service'),
    ]); ?>
</section>
<?php $this->widget('application.modules.company.widgets.CompanyRatingWidget',array('company'=>$model)); ?>
<?php $this->widget('application.modules.company.widgets.CompanyCommentWidget',array('company'=>$model)); ?>
<div class="col-sm-12">
  <?php $this->widget('application.modules.seo.widgets.SeoBlockCategoryLinksWidget', ['categories' => explode(',',$model['busyness_line'])]); ?>
</div>
<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->createUrl('/js/domstroy_2018.js')); ?>
<div id="Modal2" class="modalDialog1">
  <div>
    <img id="img_cancel" style="width:18px; position: absolute; right: 5px; cursor: pointer;" src="/images/button_cancel.png" onclick="CloseModal2(this)" alt="Закрыть окно">
    <h4 style="color: #003366;text-align: center;line-height: 1.1; font-weight: bold;" id="name_product_w"></h4>
    <span style="margin-top:15px; color:#003366; font-size:12px;">Пожайлуста, скажите продавцу, что Вы нашли это обьявление на Строительном портале ДомСтрой</span></br>
    <a id="clicktel">
      <h4 id="h4_phonemodal" style="text-align: center;color: #003366; cursor: default;"></h4>
    </a>
    <h4 id="h5_phonemodal" style="text-align: center;color: #003366; cursor: default;"></h4>
    <div style="margin-top: 30px;">
      <div class="btn btn-primary btn-block-back" id="success" data-company="<?php echo $model->id; ?>" onclick="CloseModal2(this)" style="background-color: #009900; font-size: 14px;">
        Успешный звонок
      </div>
      <div class="btn btn-primary btn-block-back" id="unsuccess" data-company="<?php echo $model->id; ?>" onclick="CloseModal2(this)" style="background-color: #990000;float:right;font-size:14px;">
        Не дозвонился
      </div>
    </div>
  </div>
</div>