<?php
/**
 * @var $this ProductBackendController
 * @var $model Product
 * @var $form \yupe\widgets\ActiveForm
 * @var ImageGroup $imageGroup
 */
?>
<?php Yii::app()->getClientScript()->registerCssFile($this->getModule()->getAssetsUrl().'/css/store-backend.css'); ?>

<ul class="nav nav-tabs">
  <li class="active"><a href="#common" data-toggle="tab"><?= Yii::t("StoreModule.store", "Common"); ?></a></li>
  <li><a href="#attributes" data-toggle="tab"><?= Yii::t("StoreModule.store", "Attributes"); ?></a></li>
  <li><a href="#images" data-toggle="tab"><?= Yii::t("StoreModule.store", "Images"); ?></a></li>
  <li><a href="#variants" data-toggle="tab"><?= Yii::t("StoreModule.store", "Variants"); ?></a></li>
  <li><a href="#stock" data-toggle="tab"><?= Yii::t("StoreModule.store", "Stock"); ?></a></li>
  <li><a href="#seo" data-toggle="tab"><?= Yii::t("StoreModule.store", "SEO"); ?></a></li>
  <li><a href="#linked" data-toggle="tab"><?= Yii::t("StoreModule.store", "Linked products"); ?></a></li>
</ul>

<?php
$form = $this->beginWidget(
  '\yupe\widgets\ActiveForm',
  [
    'id' => 'product-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    'type' => 'vertical',
    'htmlOptions' => ['enctype' => 'multipart/form-data', 'novalidate' => true, 'class' => 'well'],
    'clientOptions' => [
      'validateOnSubmit' => true,
    ],
  ]
); ?>

<div class="alert alert-info">
  <?= Yii::t('StoreModule.store', 'Fields with'); ?>
  <span class="required">*</span>
  <?= Yii::t('StoreModule.store', 'are required'); ?>
</div>

<?= $form->errorSummary($model); ?>


<div class="tab-content">
  <div class="tab-pane active" id="common">
    <div class="row">
      <div class="col-sm-3 col-xs-12">
        <?= $form->dropDownListGroup(
          $model,
          'sup_type',
          [
            'widgetOptions' => [
              'data' => [
                1=>"Товар",
                2=>"Услуга",
              ],
            ],
          ]
        ); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-9">
        <?= $form->select2Group(
          $model,
          'company_id',
          [
            'widgetOptions' => [
              'data' => Company::model()->getFormattedById(),
              'htmlOptions' => [
                'empty' => '---',
                'encode' => false,
              ],
            ],
          ]
        ); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-3">
        <?= $form->textFieldGroup($model, 'sku'); ?>
      </div>
      <div class="col-sm-3">
        <?= $form->dropDownListGroup(
          $model,
          'status',
          [
            'widgetOptions' => [
              'data' => $model->getStatusList(),
            ],
          ]
        ); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-7">
        <?= $form->select2Group(
          $model,
          'category_id',
          [
            'widgetOptions' => [
              // 'data' => StoreCategoryHelper::formattedListFinal(),
              'data' => StoreCategory::model()->getFormattedById(),
              'htmlOptions' => [
                'empty' => '---',
                'encode' => false,
              ],
            ],
          ]
        ); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-7">
        <?= $form->textFieldGroup($model, 'name'); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-7">
        <?= $form->slugFieldGroup($model, 'slug', ['sourceAttribute' => 'name']); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-2">
        <?= $form->textFieldGroup($model, 'price'); ?>
      </div>
        <?php
          if (Currency::getCurrenciesByCompany($model->company_id)) {
            echo '<div class="col-sm-3">';
            echo $form->dropDownListGroup(
              $model,
              'currency_id',
              [
                'label' => 'Валюта',
                'widgetOptions' => [
                  'data' => Currency::getCurrenciesByCompany($model->company_id),
                  'htmlOptions' => [
                    'empty' => 'грн',
                  ],
                ],           
              ]
            );
            echo '</div>';
          }
        ?>
      <div class="col-sm-3">
        <br/>
        <?= $form->checkBoxGroup($model, 'is_special'); ?>
      </div>
      <div class="col-sm-4 col-xs-12">
        <?= $form->dropDownListGroup($model, 'measure',[
          'widgetOptions'=>[
            'data'=> MeasurementsNamesHelper::formattedListService() + MeasurementsNamesHelper::formattedListProduct(),
          ]
        ]); ?>
      </div>
       <!-- <div class="col-sm-2">
        <?/*= $form->textFieldGroup($model, 'discount_price'); */?>
      </div>
      <div class="col-sm-2">
        <?/*= $form->textFieldGroup($model, 'discount'); */?>
      </div>-->
    </div>

    <!--<div class="row">
      <div class="col-sm-7">
        <div class="panel-group">
          <div class="panel panel-default">
            <div class="panel-heading">
              <a class="panel-title collapsed" data-toggle="collapse" data-parent="#accordion_price"
                 href="#collapse_price">
                <?/*= Yii::t("StoreModule.store", 'Additional price'); */?>
              </a>
            </div>
            <div id="collapse_price" class="panel-collapse collapse" style="height: 0px;">
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-4">
                    <?/*= $form->textFieldGroup($model, 'purchase_price'); */?>
                  </div>
                  <div class="col-sm-4">
                    <?/*= $form->textFieldGroup($model, 'average_price'); */?>
                  </div>
                  <div class="col-sm-4">
                    <?/*= $form->textFieldGroup($model, 'recommended_price'); */?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>-->
    <div class='row'>
      <div class="col-sm-12">&nbsp;</div>
    </div>
<!--    <div class='row'>-->
<!--      <div class="col-sm-7">-->
<!--        <div class="form-group">-->
<!--          --><?php //$this->widget(
//            'store.widgets.BackendCategoryTreeWidget',
//            [
//              'selectedCategories' => $model->getCategoriesId(),
//              'id' => 'category-tree',
//            ]
//          ); ?>
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->

    <div class='row'>
      <div class="col-sm-7">
<div class="row">
  <div class="col-md-12">
    <label class="control-label">Фото</label>
  </div>
  <div id="image_preview" class="col-md-6" style="float: left;display: <?php echo ($model->image) ? 'block' : 'none' ;
  ?> ; max-width: 300px; max-height: 300px; width: 300px; height: 300px;">
  <img src="
  <?php if(file_exists(str_replace("protected","public",Yii::app()->basePath).'/uploads/store/product/'.$model->image)){
      echo Yii::app()->createUrl('/uploads/store/product/'.$model->image);
  }else{

      echo Yii::app()->createUrl('/uploads/thumbs/avatars/10000x10000_avatar.png');
  }  ?>
            " id="image" style="width: 300px; height: 300px;">
  </div>
  <div class="col-md-6" style="float:right">
    <input id="miniatura_file" type="file" name="Product[image]" accept="image/*" style="display: inline;border: none;" onchange="miniatura(this);"
    >
  </div>
  <div class="col-md-6" style="float:right">
    <?php
    if ($model->image) {
      echo '<label><input type="checkbox" name="delete-file">Удалить фото</label>';
    }
    ?>
  </div>
  <div class="col-md-6">
    <div id="show_miniatura" class="col-md-12" style="float: right;display: none;">
    <h3>Есть возможность обрезать</h3>
    <p>
      <button type="button" id="button">Обрезать</button>
    </p>
    <div id="result">
    </div>
    </div>
  </div>
</div>
        <?php if (!$model->getIsNewRecord() && $model->image): ?>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="delete-file"> <?= Yii::t(
                'YupeModule.yupe',
                'Delete the file'
              ) ?>
            </label>
          </div>
        <?php endif; ?>

        <? /*= $form->fileFieldGroup(
          $model,
          'image',
          [
            'widgetOptions' => [
              'htmlOptions' => [
                'onchange' => 'readURL(this);',
              ],
            ],
          ]
        );*/ ?>
      </div>
    </div>
    <?php
      echo $form->textFieldGroup($model, 'video',array('label'=>'Видео (код YouTube)'));
    ?>

    <div class='row'>
      <div class="col-sm-12 <?= $model->hasErrors('description') ? 'has-error' : ''; ?>">
        <?= $form->labelEx($model, 'description'); ?>
        <?php $this->widget(
          $this->module->getVisualEditor(),
          [
            'model' => $model,
            'attribute' => 'description',
          ]
        ); ?>
        <p class="help-block"></p>
        <?= $form->error($model, 'description'); ?>
      </div>
    </div>
  </div>

  <div class="tab-pane" id="stock">
    <div class="row">
      <div class="col-sm-3">
        <?= $form->dropDownListGroup(
          $model,
          'in_stock',
          [
            'widgetOptions' => [
              'data' => $model->getInStockList(),
            ],
          ]
        ); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <?= $form->dropDownListGroup(
          $model,
          'producer_id',
          [
            'widgetOptions' => [
              'data' => Producer::model()->getFormattedList(),
              'htmlOptions' => [
                'empty' => '---',
              ],
            ],
          ]
        ); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-2">
        <?//= $form->textFieldGroup($model, 'length'); ?>
      </div>
      <div class="col-sm-2">
        <?//= $form->textFieldGroup($model, 'width'); ?>
      </div>
      <div class="col-sm-2">
        <?//= $form->textFieldGroup($model, 'height'); ?>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-2">
        <?//= $form->textFieldGroup($model, 'weight'); ?>
      </div>

      <div class="col-sm-2">
        <?//= $form->numberFieldGroup($model, 'quantity'); ?>
      </div>
    </div>

  </div>

  <div class="tab-pane" id="images">
    <div class="row form-group">
      <div class="col-xs-2">
        <?= Yii::t("StoreModule.store", "Images"); ?>
      </div>
      <div class="col-xs-2">
        <button id="button-add-image" type="button" class="btn btn-default"><i class="fa fa-fw fa-plus"></i>
        </button>
      </div>
      <div class="col-sm-3 col-sm-offset-5 text-right">
        <button type="button" data-toggle="modal" data-target="#image-groups" class="btn btn-primary">
          <?= Yii::t("StoreModule.store", "Image groups"); ?>
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <?php $imageModel = new ProductImage(); ?>
        <div id="product-images">
          <div class="image-template hidden form-group">
            <div class="row">
              <div class="col-xs-6 col-sm-2">
                <label for=""><?= Yii::t("StoreModule.store", "File"); ?></label>
                <input type="file" class="image-file"/>
              </div>
              <div class="col-xs-5 col-sm-3">
                <label for=""><?= Yii::t("StoreModule.store", "Image title"); ?></label>
                <input type="text" class="image-title form-control"/>
              </div>
              <div class="col-xs-5 col-sm-3">
                <label for=""><?= Yii::t("StoreModule.store", "Image alt"); ?></label>
                <input type="text" class="image-alt form-control"/>
              </div>
              <div class="col-xs-6 col-sm-3">
                <label for=""><?= Yii::t("StoreModule.store", "Group"); ?></label>
                <?= CHtml::dropDownList('', null, ImageGroupHelper::all(), [
                  'empty' => Yii::t('StoreModule.store', '--choose--'),
                  'class' => 'form-control image-group image-group-dropdown',
                ]) ?>
              </div>
              <div class="col-xs-2 col-sm-1" style="padding-top: 24px">
                <button class="button-delete-image btn btn-default" type="button"><i
                    class="fa fa-fw fa-trash-o"></i></button>
              </div>
            </div>
          </div>
        </div>

        <?php if (!$model->getIsNewRecord() && $model->images): ?>
          <table class="table table-hover">
            <thead>
            <tr>
              <th></th>
              <th><?= Yii::t("StoreModule.store", "Image title"); ?></th>
              <th><?= Yii::t("StoreModule.store", "Image alt"); ?></th>
              <th><?= Yii::t("StoreModule.store", "Group"); ?></th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model->images as $image): ?>
              <tr>
                <td>
                  <img src="<?= $image->getImageUrl(100, 100); ?>" alt="" class="img-responsive"/>
                </td>
                <td>
                  <?= CHtml::textField('ProductImage['.$image->id.'][title]', $image->title,
                    ['class' => 'form-control']) ?>
                </td>
                <td>
                  <?= CHtml::textField('ProductImage['.$image->id.'][alt]', $image->alt,
                    ['class' => 'form-control']) ?>
                </td>
                <td>
                  <?= CHtml::dropDownList(
                    'ProductImage['.$image->id.'][group_id]',
                    $image->group_id,
                    ImageGroupHelper::all(),
                    [
                      'empty' => Yii::t('StoreModule.store', '--choose--'),
                      'class' => 'form-control image-group-dropdown',
                    ]
                  ) ?>
                </td>
                <td class="text-center">
                  <a data-id="<?= $image->id; ?>" href="<?= Yii::app()->createUrl(
                    '/store/productBackend/deleteImage',
                    ['id' => $image->id]
                  ); ?>" class="btn btn-default product-delete-image"><i
                      class="fa fa-fw fa-trash-o"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="tab-pane" id="attributes">
    <div id="attributes-panel"></div>
  </div>

  <div class="tab-pane" id="seo">
    <div class="row">
      <div class="col-sm-7">
        <?= $form->textFieldGroup($model, 'title'); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-7">
        <?= $form->textFieldGroup($model, 'meta_title'); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-7">
        <?= $form->textFieldGroup($model, 'meta_keywords'); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-7">
        <?= $form->textAreaGroup($model, 'meta_description'); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-7">
        <?= $form->textFieldGroup($model, 'meta_canonical'); ?>
      </div>
    </div>
  </div>

  <div class="tab-pane" id="variants">
    <div class="row">
      <div class="col-sm-12 form-group">
        <label class="control-label" for=""><?= Yii::t("StoreModule.store", "Attribute"); ?></label>

        <div class="form-inline">
          <div class="form-group">
            <select id="variants-type-attributes" class="form-control"></select>
            <a href="#" class="btn btn-default" id="add-product-variant"><?= Yii::t(
                "StoreModule.store",
                "Add"
              ); ?></a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="variant-template variant">
            <table>
              <thead>
              <tr>
                <td><?= Yii::t("StoreModule.store", "Attribute"); ?></td>
                <td><?= Yii::t("StoreModule.store", "Value"); ?></td>
                <td><?= Yii::t("StoreModule.store", "Price type"); ?></td>
                <td><?= Yii::t("StoreModule.store", "Price"); ?></td>
                <td><?= Yii::t("StoreModule.store", "SKU"); ?></td>
                <td><?= Yii::t("StoreModule.store", "Order"); ?></td>
                <td></td>
              </tr>
              </thead>
              <tbody id="product-variants">
              <?php foreach ((array)$model->variants as $variant): ?>
                <?php $this->renderPartial('_variant_row', ['variant' => $variant]); ?>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="tab-pane" id="linked">
    <?php if ($model->getIsNewRecord()): ?>
      <?= Yii::t("StoreModule.store", "First you need to save the product."); ?>
    <?php else: ?>
      <?= $this->renderPartial('_link_form', ['product' => $model, 'searchModel' => $searchModel]); ?>
    <?php endif; ?>
  </div>
</div>

<br/>

<?php $this->widget(
  'bootstrap.widgets.TbButton',
  [
    'buttonType' => 'submit',
    'context' => 'primary',
    'label' => $model->getIsNewRecord() ? Yii::t('StoreModule.store', 'Добавить товар и продолжить') : Yii::t(
      'StoreModule.store',
      'Добавить товар и продолжить'
    ),
  ]
); ?>

<?php $this->widget(
  'bootstrap.widgets.TbButton',
  [
    'buttonType' => 'submit',
    'htmlOptions' => ['name' => 'submit-type', 'value' => '/backend/store/product/index'],
    'label' => $model->getIsNewRecord() ? Yii::t('StoreModule.store', 'Добавить товар и закрыть') : Yii::t(
      'StoreModule.store',
      'Добавить товар и закрыть'
    ),
  ]
); ?>

<?php $this->endWidget(); ?>

<?php $this->renderPartial('_image_groups_modal', ['imageGroup' => $imageGroup]) ?>

<script>
  $(function () {

    $('#product-form').submit(function () {
      var productForm = $(this);
      $('#category-tree a.jstree-clicked').each(function (index, element) {
        productForm.append('<input type="hidden" name="categories[]" value="' + $(element).data('category-id') + '" />');
      });
    });

    var typeAttributes = {};

    function updateVariantTypeAttributes() {
      var typeId = $('#product-type').val();
      if (typeId) {
        $.getJSON('<?= Yii::app()->createUrl('/store/productBackend/typeAttributes');?>/' + typeId, function (data) {
          typeAttributes = data;
          var select = $('#variants-type-attributes');
          select.html("");
          $.each(data, function (key, value) {
            select.append($("<option></option>")
              .attr("value", value.id)
              .text(value.title));
          });
        });
      }
    }

    updateVariantTypeAttributes();

    $("#add-product-variant").click(function (e) {
      e.preventDefault();
      var attributeId = $('#variants-type-attributes').val();
      var variantAttribute = typeAttributes.filter(function (el) {
        return el.id == attributeId;
      }).pop();
      var tbody = $('#product-variants');
      $.get('<?= Yii::app()->createUrl('/store/productBackend/variantRow');?>/' + attributeId, function (data) {
        tbody.append(data);
      });
    });

    $('#product-variants').on('click', '.remove-variant', function (e) {
      e.preventDefault();
      $(this).closest('tr').remove();
    });

    $('#product-type').on('change', function () {
      var typeId = $(this).val();
      if (typeId) {
        $('#attributes-panel').load('<?= Yii::app()->createUrl('/store/productBackend/typeAttributesForm');?>/' + typeId);
        updateVariantTypeAttributes();
      }
      else {
        $('#attributes-panel').html('');
        $('#variants-type-attributes').html('');
      }
    });

    $('#button-add-image').on('click', function () {
      var newImage = $("#product-images .image-template").clone().removeClass('image-template').removeClass('hidden');
      var key = $.now();

      newImage.appendTo("#product-images");
      newImage.find(".image-file").attr('name', 'ProductImage[new_' + key + '][name]');
      newImage.find(".image-title").attr('name', 'ProductImage[new_' + key + '][title]');
      newImage.find(".image-alt").attr('name', 'ProductImage[new_' + key + '][alt]');
      newImage.find(".image-group").attr('name', 'ProductImage[new_' + key + '][group_id]');

      return false;
    });

    $(this).closest('.product-image').remove();

    $('#product-images').on('click', '.button-delete-image', function () {
      $(this).closest('.row').remove();
    });

    $('.product-delete-image').on('click', function (event) {
      event.preventDefault();
      var blockForDelete = $(this).closest('tr');
      $.ajax({
        type: "POST",
        data: {
          'id': $(this).data('id'),
          '<?= Yii::app()->getRequest()->csrfTokenName;?>': '<?= Yii::app()->getRequest()->csrfToken;?>'
        },
        url: '<?= Yii::app()->createUrl('/store/productBackend/deleteImage');?>',
        success: function () {
          blockForDelete.remove();
        }
      });
    });

    function activateFirstTabWithErrors() {
      var tab = $('.has-error').parents('.tab-pane').first();
      if (tab.length) {
        var id = tab.attr('id');
        $('a[href="#' + id + '"]').tab('show');
      }
    }
    activateFirstTabWithErrors();
  });
</script>
<script>
function updateAjax2() {
  $('#attributes-panel').load('<?php echo Yii::app()->createUrl("/user/product/updateajax2/");?>' + '/' + $('select#Product_category_id').val() + '/'+ '<?= $model->id;?>' +'/');
}
$(document).ready(function(){ 
  if ($('select#Product_category_id').val()) {
    updateAjax2();
  }
});
$('select#Product_category_id').on('change', function (event) {
  updateAjax2();
});
</script>
<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->createUrl("/js/cropperjs/cropper.min.js"));?>
<script>
function miniatura (input) {
  if (input.files && input.files[0]) {
  var reader = new FileReader();
  reader.onload = function (e) {
    $('#image').attr('src', e.target.result);
    $('#miniatura_file').hide();
    $('#show_miniatura').show();
    $('#image_preview').show();
    var image = document.getElementById('image');
    var button = document.getElementById('button');
    var result = document.getElementById('result');
    var croppable = false;
    var cropper = new Cropper(image, {
    aspectRatio: 1,
    autoCropArea: 1,
    //viewMode: 1,
    ready: function () {
      croppable = true;
    }
    });
    button.onclick = function () {
    var croppedCanvas;
    var roundedCanvas;
    var roundedImage;
    if (!croppable) {
      return;
    }
    // Crop
    croppedCanvas = cropper.getCroppedCanvas({
      fillColor: '#fff',
    });

    // Round
    roundedCanvas = croppedCanvas;
    // Show
    roundedImage = document.createElement('img');
    roundedImage.src = roundedCanvas.toDataURL();
    result.innerHTML = '';
    result.appendChild(roundedImage);
    //custom
    hiddenInput = document.createElement('input');
    hiddenInput.type = "hidden";
    hiddenInput.name = "Product[imageBase64]";
    hiddenInput.value = roundedCanvas.toDataURL();
    result.appendChild(hiddenInput);
    };
  };
  reader.readAsDataURL(input.files[0]);
  }
}
</script>
<style type="text/css">
#result img {
  width: 34%;
}
</style>
<?php
  Yii::app()->getClientScript()->registerCssFile(Yii::app()->createUrl("/css/cropper.min.css"));
?>