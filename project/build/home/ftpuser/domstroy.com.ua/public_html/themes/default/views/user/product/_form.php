<?php
/**
 * @var $this ProductBackendController
 * @var $model Product
 * @var $form \yupe\widgets\ActiveForm
 * @var ImageGroup $imageGroup
 */
?>
<!-- Это нам нужно для запуска функции в профиле мастера -->
<?php if ($model->id) { ?>
	<input type="hidden" id="model_id" value="1">
<? } else{ ?>
	<input type="hidden" id="model_id" value="0">
<?php } ?>
<!-- Это нам нужно для запуска функции в профиле мастера -->
<?php
$form = $this->beginWidget(
  '\yupe\widgets\ActiveForm',
  [
    'id' => 'product-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    'type' => 'vertical',
    'htmlOptions' => ['enctype' => 'multipart/form-data', 'class' => ''],
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
<!-- Проверка на допустимое количество обьявлений -->
<?= $form->errorSummary($model); ?>
<!-- Проверка на допустимое количество обьявлений -->
<div class="tab-content">
  <fieldset style="border-bottom: 1px solid #ccc">
  	<!-- Название товара -->
    <div class="row">
      <div class="col-sm-8 col-xs-12">
          <?= $form->textFieldGroup($model, 'name',[
            'tooltip' => array(
              'label' => '?',
              'context' => 'info',
              'size' => '',
              'htmlOptions' => array(
                'class' => 'cabinet_product_popover',
                'data-title' => Yii::t('site','help-tooltip'),
                'data-placement' => 'top',
                'data-content' => $model->getAttributeDescription('name'),
                'data-toggle' => 'popover',
                'data-html'=> true,
              ),
            ),
          ]); ?>
      </div>
      <div  class="col-sm-4 col-xs-12">
		<?= $form->textFieldGroup($model, 'sku',[
		'labelOptions'=>[
		  'class'=>'tooltip-label',
		  'data-text'=>$model->getAttributeDescription('sku')
		],
		'tooltip' => array(
		  'label' => '?',
		  'context' => 'info',
		  'size' => '',
		  'htmlOptions' => array(
		    'class' => 'cabinet_product_popover',
		    'data-title' => Yii::t('site','help-tooltip'),
		    'data-placement' => 'top',
		    'data-content' => $model->getAttributeDescription('sku'),
		    'data-toggle' => 'popover',
		    'data-html'=> true,
		  ),
		)
		]); ?>
      </div>

    <div class="row">
    	<div class="col-sm-9 col-xs-12">
        <!-- Блок вывода селекта товары/услуги -->
	        <?php
	        //Если редактирование товара компании
	        if (($model->id)&&($model->company->sup_type === '1')) {?>
	            <div class="form-group has-success">
	           	  <div class="col-sm-4 col-xs-12" style="float: left;">
		              <label class="control-label" for="Product_sup_type">Товар/услуга <span class="required">*</span></label>
	          	  </div>
	          	  <div class="col-sm-8 col-xs-12">
		              <select class="form-control" placeholder="Товар/услуга" id="Product_sup_ty" required onchange="SelectType(this)">
		              	<?php if ($model->attributes['sup_type'] == 1){ ?>
			                <option selected value="1">Товар</option>
			                <option value="2">Услуга</option>
			                <input type="hidden" name="Product[sup_type]" id="Product_sup_type" value="1">
			                <input type="hidden" name="sup_type_id" id="sup_type_id" value="1">
		                <?php } else if ($model->attributes['sup_type'] == 2) { ?>
							<option value="1">Товар</option>
			                <option selected value="2">Услуга</option>
			                <input type="hidden" name="Product[sup_type]" id="Product_sup_type" value="2">
			                <input type="hidden" name="sup_type_id" id="sup_type_id" value="2">
		                <?php } ?>
		              </select>
		              <div class="help-block error" id="Product_sup_type_em_" style="display:none">
		              </div>
	              </div>
	            </div>
	            <input type="hidden" name="sup_type_id" id="sup_type_id" value="">
            <?php }
            //Если редактирование услуги мастера
            else if (($model->id)&&($model->company->sup_type === '2')){ ?>
	            <div class="form-group has-success" style="display: none;">
	           	  <div class="col-sm-4 col-xs-12" style="float: left; display: none;">
		              <label class="control-label" for="Product_sup_type">Товар/услуга <span class="required">*</span></label>
	          	  </div>
	          	  <div class="col-sm-8 col-xs-12" style="display: none;">
		              <select class="form-control" placeholder="Товар/услуга" id="Product_sup_ty" required onchange="SelectType(this)">
							<option value="1">Товар</option>
			                <option selected value="2">Услуга</option>
			                <input type="hidden" name="Product[sup_type]" id="Product_sup_type" value="2">
			                <input type="hidden" name="sup_type_id" id="sup_type_id" value="2">
		              </select>
		              <div class="help-block error" id="Product_sup_type_em_" style="display:none">
		              </div>
	              </div>
	            </div>
	            <input type="hidden" name="sup_type_id" id="sup_type_id" value="2">
            <?php }
	        //Если добавление нового товара компании
	        else if ($model->company->sup_type === '1'){ ?>
	            <div class="form-group has-success">
	           	  <div class="col-sm-4 col-xs-12" style="float: left;">
		              <label class="control-label" for="Product_sup_type">Товар/услуга <span class="required">*</span></label>
	          	  </div>
	          	  <div class="col-sm-8 col-xs-12">
		              <select class="form-control" placeholder="Товар/услуга" id="Product_sup_ty" required onchange="SelectType(this)">
		                <option selected disabled>Выберите тип предложения</option>
		                <option value="1">Товар</option>
		                <option value="2">Услуга</option>
		              </select>
		              <input type="hidden" name="sup_type_id" id="sup_type_id" value="0">
		              <div class="help-block error" id="Product_sup_type_em_" style="display:none">
		              </div>
	              </div>
	            </div>
	            <input type="hidden" name="Product[sup_type]" id="Product_sup_type" value="">
	        <?php }
	        //Если добавление новой услуги мастера
	        else if ($model->company->sup_type === '2'){ ?>
	            <div class="form-group has-success" style="display: none;">
	           	  <div class="col-sm-4 col-xs-12" style="float: left;">
		              <label class="control-label" for="Product_sup_type">Товар/услуга <span class="required">*</span></label>
	          	  </div>
	          	  <div class="col-sm-8 col-xs-12">
		              <select class="form-control" placeholder="Товар/услуга" id="Product_sup_ty" required onchange="SelectType(this)">
		                <option disabled>Выберите тип предложения</option>
		                <option value="1">Товар</option>
		                <option selected value="2">Услуга</option>
		              </select>
		              <input type="hidden" name="sup_type_id" id="sup_type_id" value="2">
		              <div class="help-block error" id="Product_sup_type_em_" style="display:none">
		              </div>
	              </div>
	            </div>
	            <input type="hidden" name="Product[sup_type]" id="Product_sup_type" value="2">
	        <?php } ?>
        </div>
    </div>

    <div class="row">
    	<div class="col-sm-9 col-xs-12" id="categories">
       	  	<div class="col-sm-4 col-xs-12" style="float: left;">
               <label class="control-label" for="Product_sup_type">Главная категория<span class="required">*</span></label>
      	  	</div>
      	  	<div class="col-sm-8 col-xs-12" id="majorcategory">
				<!-- Если идет редактирование -->
				<?php if ($model->id){ ?>
				<select class="form-control" placeholder="Главная категория" id="Product_sup_type1" data-findselect="1" required onchange="SelectCat(this)">
						<?php foreach ($categories_list1 as $measur) {
							if ($measur['id'] == $categories_major) {
								echo '<option selected value="'.$measur['id'].'">'.$measur['name'].'</option>';
							}else{
							echo '<option value="'.$measur['id'].'">'.$measur['name'].'</option>';
							} ?>
					<?php } ?>
              	</select>
              	<input type="hidden" name="Product[category_id]" id="category_tovar" value="<?php echo $categories_finish; ?>" />
				<!-- Если идет редактирование-->
				<?php }
				else { ?>
				<!-- Если добавление -->
					<?php if ($model->company->sup_type === '2') { ?>
						<select class="form-control" placeholder="Главная категория" id="Product_sup_type1" data-findselect="1" required disabled="disabled" onchange="SelectCat(this)">
	                		<option selected disabled>Выберите главную категорию</option>
		              	</select>
		              	<input type="hidden" name="Product[category_id]" id="category_tovar" value="" />
					<?php }
					else { ?>
					<select class="form-control" placeholder="Главная категория" id="Product_sup_type1" data-findselect="1" required disabled="disabled" onchange="SelectCat(this)">
	                	<option selected disabled>Выберите главную категорию</option>
	              	</select>
	              	<input type="hidden" name="Product[category_id]" id="category_tovar" value="" />
					<!-- Если добавление -->
					<?php } ?>
				<?php } ?>
          	</div>
      	</div>
    </div>

	<?php if ($model->id){
		if ($categories_list2!=0) { ?>
	        <div class="row" id="cat1">
	        	<div class="col-sm-9 col-xs-12">
		       	  	<div class="col-sm-4 col-xs-12" style="float: left;">
		               <label class="control-label" for="Product_sup_type">Подкатегория<span class="required">*</span></label>
		      	  	</div>
		      	  	<div class="col-sm-8 col-xs-12" id="category1">
		              	<select class="form-control" placeholder="Подкатегория1" id="Product_sup_type2" data-findselect="2" required onchange="SelectCat(this)">
							<?php foreach ($categories_list2 as $measur) {
	  							if ($measur['id'] == $categories1) {
	  								echo '<option selected value="'.$measur['id'].'">'.$measur['name'].'</option>';
	  							}else{
									echo '<option value="'.$measur['id'].'">'.$measur['name'].'</option>';
	  							} ?>
							<?php } ?>
		              	</select>
		          	</div>
	          	</div>
	        </div>
		<?php }
		else{ ?>
		    <div class="row" id="cat1" style="display: none;">
		    	<div class="col-sm-9 col-xs-12">
		       	  	<div class="col-sm-4 col-xs-12" style="float: left;">
		               <label class="control-label" for="Product_sup_type">Подкатегория<span class="required">*</span></label>
		      	  	</div>
		      	  	<div class="col-sm-8 col-xs-12" id="category1">
		              	<select class="form-control" placeholder="Подкатегория1" id="Product_sup_type2" data-findselect="2" required disabled="disabled" onchange="SelectCat(this)">
		                	<option selected disabled>Выберите подкатегорию</option>
		              	</select>
		          	</div>
		      	</div>
		    </div>
	<?php }
	} else { ?>
    <div class="row" id="cat1">
    	<div class="col-sm-9 col-xs-12">
       	  	<div class="col-sm-4 col-xs-12" style="float: left;">
               <label class="control-label" for="Product_sup_type">Подкатегория<span class="required">*</span></label>
      	  	</div>
      	  	<div class="col-sm-8 col-xs-12" id="category1">
              	<select class="form-control" placeholder="Подкатегория1" id="Product_sup_type2" data-findselect="2" required disabled="disabled" onchange="SelectCat(this)">
                	<option selected disabled>Выберите подкатегорию</option>
              	</select>
          	</div>
      	</div>
    </div>
	<?php } ?>
		<?php if ($model->id){
			if ($categories_list3!=0) { ?>
		        <div class="row" id="cat2">
		        	<div class="col-sm-9 col-xs-12">
			       	  	<div class="col-sm-4 col-xs-12" style="float: left;">
			               <label class="control-label" for="Product_sup_type">Подкатегория<span class="required">*</span></label>
			      	  	</div>
			      	  	<div class="col-sm-8 col-xs-12" id="category2">
			              	<select class="form-control" placeholder="Подкатегория2" id="Product_sup_type3" data-findselect="3" required onchange="SelectCat(this)">
								<?php foreach ($categories_list3 as $measur) {
		  							if ($measur['id'] == $categories2) {
		  								echo '<option selected value="'.$measur['id'].'">'.$measur['name'].'</option>';
		  							}else{
										echo '<option value="'.$measur['id'].'">'.$measur['name'].'</option>';
		  							} ?>
								<?php } ?>
			              	</select>
			          	</div>
		          	</div>
		        </div>
			<?php }
			else{ ?>
		        <div class="row" id="cat2" style="display: none;">
		        	<div class="col-sm-9 col-xs-12">
			       	  	<div class="col-sm-4 col-xs-12" style="float: left;">
			               <label class="control-label" for="Product_sup_type">Подкатегория<span class="required">*</span></label>
			      	  	</div>
			      	  	<div class="col-sm-8 col-xs-12" id="category2">
			              	<select class="form-control" placeholder="Подкатегория2" id="Product_sup_type3" data-findselect="3" required disabled="disabled" onchange="SelectCat(this)">
			                	<option selected disabled>Выберите подкатегорию</option>
			              	</select>
			          	</div>
		          	</div>
		        </div>
			<?php }
		} else { ?>
        <div class="row" id="cat2">
        	<div class="col-sm-9 col-xs-12">
	       	  	<div class="col-sm-4 col-xs-12" style="float: left;">
	               <label class="control-label" for="Product_sup_type">Подкатегория<span class="required">*</span></label>
	      	  	</div>
	      	  	<div class="col-sm-8 col-xs-12" id="category2">
	              	<select class="form-control" placeholder="Подкатегория2" id="Product_sup_type3" data-findselect="3" required disabled="disabled" onchange="SelectCat(this)">
	                	<option selected disabled>Выберите подкатегорию</option>
	              	</select>
	          	</div>
          	</div>
        </div>
    <?php } ?>
	<?php if ($model->id){
		if ($categories_list4!=0) { ?>
	        <div class="row" id="cat3">
	        	<div class="col-sm-9 col-xs-12">
		       	  	<div class="col-sm-4 col-xs-12" style="float: left;">
		               <label class="control-label" for="Product_sup_type">Подкатегория<span class="required">*</span></label>
		      	  	</div>
		      	  	<div class="col-sm-8 col-xs-12" id="category3">
		              	<select class="form-control" placeholder="Подкатегория2" id="Product_sup_type4" data-findselect="4" required onchange="SelectCat(this)">
							<?php foreach ($categories_list4 as $measur) {
	  							if ($measur['id'] == $categories3) {
	  								echo '<option selected value="'.$measur['id'].'">'.$measur['name'].'</option>';
	  							}else{
									echo '<option value="'.$measur['id'].'">'.$measur['name'].'</option>';
	  							} ?>
							<?php } ?>
		              	</select>
		          	</div>
	          	</div>
	        </div>
		<?php }
		else{ ?>
		    <div class="row" id="cat3" style="display: none;">
		    	<div class="col-sm-9 col-xs-12">
		       	  	<div class="col-sm-4 col-xs-12" style="float: left;">
		               <label class="control-label" for="Product_sup_type">Подкатегория<span class="required">*</span></label>
		      	  	</div>
		      	  	<div class="col-sm-8 col-xs-12" id="category3">
		              	<select class="form-control" placeholder="Подкатегория3" id="Product_sup_type4" data-findselect="4" required disabled="disabled" onchange="SelectCat(this)">
		                	<option selected disabled>Выберите подкатегорию</option>
		              	</select>
		          	</div>
		      	</div>
		    </div>
		<?php }
	} else { ?>
    <div class="row" id="cat3">
    	<div class="col-sm-9 col-xs-12">
       	  	<div class="col-sm-4 col-xs-12" style="float: left;">
               <label class="control-label" for="Product_sup_type">Подкатегория<span class="required">*</span></label>
      	  	</div>
      	  	<div class="col-sm-8 col-xs-12" id="category3">
              	<select class="form-control" placeholder="Подкатегория3" id="Product_sup_type4" data-findselect="4" required disabled="disabled" onchange="SelectCat(this)">
                	<option selected disabled>Выберите подкатегорию</option>
              	</select>
          	</div>
      	</div>
    </div>
    <?php } ?>
    <div class="row">
    	<div class="col-sm-9 col-xs-12" id="div_search" style="font-size: 10px;cursor: pointer;display:none;" onclick="SearchCat()">
			<span style="float: right;margin-right: 15px;color: #6565db; font-size: 0px;">Не смогли найти подходящую категорию?</span>
		</div>
    </div>
  	<fieldset style="border-bottom: 1px solid #ccc">
  		<div class="row visible-xs">
  			<?= $form->checkBoxGroup($model, 'is_special'); ?>
  		</div>
  		<div class="row">
  			<div class="row" style="margin-left: 10px; padding: 0px">
	  			<div class="col-sm-3 col-xs-10">
					<?= $form->numberFieldGroup($model, 'price',[
					  'widgetOptions' => [
					    'htmlOptions' => [
					      'step' => 'any',
					      'min' => '0',
					    ]
					  ]
					]); ?>
	  			</div>
	  		    <div class="col-sm-3 col-xs-10">
					<?= $form->numberFieldGroup($model, 'old_price',[
					  'widgetOptions' => [
					    'htmlOptions' => [
					      'step' => 'any',
					      'min' => '0',
					    ]
					  ]
					]); ?>
	  			</div>
		        <div class="row" style="padding:0px;margin-left: 10px;">
					<div class="col-sm-3 col-xs-10">
		  				<label class="control-label" for="Product_price">Единица измерения</label>
		  				<span class="required">*</span>
		  				<!-- Если редактирование товара -->
		  				<?php if ($model->id){ ?>
		  					<select class="form-control" placeholder="Единица" name="Product[measure]" id="edinica" required onchange="SelectEdIzm(this)">
		  						<?php foreach ($measures as $measur) {
		  							if ($measur['id'] == $model->attributes['measure']) {
		  								echo '<option selected value="'.$measur['id'].'">'.$measur['name'].'</option>';
		  							}else{
										echo '<option value="'.$measur['id'].'">'.$measur['name'].'</option>';
		  							} ?>
								<?php } ?>
							</select>
						<!-- Если добавление товара -->
		  				<?php } else { ?>
							<select class="form-control" placeholder="Единица" name="Product[measure]" id="edinica" required disabled="disabled" onchange="SelectEdIzm(this)">
								<option selected disabled value="0">---</option>
							</select>
		  				<?php } ?>

		  			</div>
		        </div>
  			</div>
  			<div class="col-sm-1 hidden-xs" style="margin-top: 0px;margin-bottom: 0px;" id="is_special">
	           <?= $form->checkBoxGroup($model, 'is_special'); ?>
	        </div>

				<div class="col-sm-4 col-xs-12 hidden">
					<?= $form->slugFieldGroup($model, 'slug', [
					'sourceAttribute' => 'name',
					'tooltip' => array(
					'label' => '?',
					'context' => 'info',
					'size' => '',
					'htmlOptions' => array(
					'class' => 'cabinet_product_popover',
					'data-title' => Yii::t('site','help-tooltip'),
					'data-placement' => 'top',
					'data-content' => $model->getAttributeDescription('slug'),
					'data-toggle' => 'popover',
					'data-html'=> true,
					),
					),
					//'hint'=>'Заполняется автоматически.'
					]); ?>
				</div>
	            <input type="hidden" id="finish_cat" value="<?php if(isset($categories_finish)){ echo $categories_finish; } ?>">
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

  		</div>
  		<div class="row">
  			<?php
  			//Если редактирование товара компании блок его наличия
  			if (($model->id)&&($model->company->sup_type === '1')) {?>
			<div class="col-sm-3" id="stock">
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
			<?php } else {?>
			<div class="col-sm-3" id="stock" style="display: none;">
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
			<?php } ?>
  		</div>
  	</fieldset>
	<?php if (($model->id)&&(!empty($attribute))) { ?>

		<fieldset id="attribute_pan" style="border-bottom: 1px solid #ccc;">
		<label>Характеристики товара</label>
		<div class="row" style="margin:0px;" id="attributes-panel">
		<?php foreach ($attribute as $at) {
			if ($at['type'] == 2){
				echo '<div class="row" style="margin:0px; padding-left:15px;padding-right:5px;">';
				echo '<div class="col-sm-1"></div>';
				echo '<div class="col-sm-5 col-xs-12">';
				echo '<label class="control-label" for="Attribute_'.$at['name'].'">'.$at['title'].'</label>';
				if ($at['required'] == 1) {
				    echo '<span class="required">*</span>';
				}
				echo '</div>';
				echo '<div class="col-sm-5 col-xs-12">';
				if (isset($at['required'])&&($at['required'] == 1)) {
					echo '<select class="form-control" placeholder="'.$at['title'].'" name="Attribute['.$at['id_attribute'].']" id="Attribute_'.$at['id_attribute'].' required="required"">';
				}
				else{
					echo '<select class="form-control" placeholder="'.$at['title'].'" name="Attribute['.$at['id_attribute'].']" id="Attribute_'.$at['id_attribute'].'>';
				}
				if (!empty($attr_f)) {
					echo '<option selected value="0"></option>';
					foreach ($at['value'] as $v) {
						if ((isset($attr_f[$at['id_attribute']]))&&(!empty($attr_f[$at['id_attribute']]))) {
							if($v['id']==$attr_f[$at['id_attribute']]){
								echo '<option selected value="'.$v['id'].'">'.$v['value'].'</option>';
							}
							else{
								echo '<option value="'.$v['id'].'">'.$v['value'].'</option>';
							}
						}
						else{
							echo '<option value="'.$v['id'].'">'.$v['value'].'</option>';
						}
					}
				}
				else{
					echo '<option selected value="0"></option>';
					foreach ($at['value'] as $v) {
						echo '<option value="'.$v['id'].'">'.$v['value'].'</option>';
					}
				}
				echo '</select>';
				echo '</div>';
				echo '<div class="col-sm-1"></div>';
				echo '</div>';
			}
			else if($at['type'] == 6){
				echo '<div class="row">';
				echo '<div class="col-sm-1"></div>';
				echo '<div class="col-sm-5 col-xs-12">';
				echo '<label class="control-label" for="Attribute_'.$at['name'].'">'.$at['title'].'</label>';
				if ($at['required'] == 1) {
				    echo '<span class="required">*</span>';
				}
				echo '</div>';
				echo '<div class="col-sm-5 col-xs-12">';
				if ($at['required'] == 1) {
					if (!empty($attr_f[$at['id_attribute']])) {
						echo '<input step="any" min="0" class="form-control" placeholder="'.$at['title'].'" name="Attribute['.$at['id_attribute'].']" id="Attribute_'.$at['id_attribute'].'" type="number" value="'.$attr_f[$at['id_attribute']].'" required="required">';
					}
				    else{
				    	echo '<input step="any" min="0" class="form-control" placeholder="'.$at['title'].'" name="Attribute['.$at['id_attribute'].']" id="Attribute_'.$at['id_attribute'].'" type="number" value="" required="required">';
				    }
				}
				else{
					if (!empty($attr_f[$at['id_attribute']])) {
						echo '<input step="any" min="0" class="form-control" placeholder="'.$at['title'].'" name="Attribute['.$at['id_attribute'].']" id="Attribute_'.$at['id_attribute'].'" type="number" value="'.$attr_f[$at['id_attribute']].'">';
					}
					else{
						echo '<input step="any" min="0" class="form-control" placeholder="'.$at['title'].'" name="Attribute['.$at['id_attribute'].']" id="Attribute_'.$at['id_attribute'].'" type="number" value="">';
					}

				}
				echo '</div>';
				echo '<div class="col-sm-1"></div>';
				echo '</div>';
			}
		} ?>
		</div>
		</fieldset>
	<?php } else{ ?>
		<fieldset id="attribute_pan" style="border-bottom: 1px solid #ccc; display: none;">
			<label>Характеристики товара</label>
			<div class="row" id="attributes-panel">
			</div>
		</fieldset>
	<?php } ?>
	<!-- Сюда выводятся при наличии характеристики товаров -->
  	<fieldset style="border-bottom: 1px solid #ccc">
  		<label>Описание товара <span class="required">*</span></label>
  		<h5 id="descr_error">Минимальное описание товара должно иметь 350 символов.</h5>
	  	<!-- Описание товара/услуги -->
		<div class='row'>
			<div class="col-sm-12 <?= $model->hasErrors('description') ? 'has-error' : ''; ?>">
			<?php $this->widget(
			$this->module->getVisualEditor(),
			[
			  'model' => $model,
			  'attribute' => 'description',
			  'options'=>array(
			    'buttonsHide'=> array(
			      'deleted',
			      'image', 'link', 'file', 'horizontalrule', 'align'
			    ),
			  ),
			]
			); ?>
			<p class="help-block"></p>
			<span id="block"></span>
			<?= $form->error($model, 'description'); ?>
			</div>

		</div>
	</fieldset>
	<fieldset>
		<div class="row">
			<?php
		  		echo '<fieldset style="border-bottom: 1px solid #ccc">';
		        	echo $form->textFieldGroup($model, 'video',array('label'=>'Видео YouTube. Для вставки кода Вашего видео перейдите на страницу в Ютубе и скопируйте с адресной строки ссилку на видео'));
		    	echo '</fieldset>';
	    	?>
		</div>
	</fieldset>
  	<fieldset style="border-bottom: 1px solid #ccc">
		<label>Фото товара (не более 4 файлов, размер одного файла не должен превышать 5 МБ)</label></br>
		<h5>Максимальный размер одного фото: 5 Мб.	Форматы фото: JPEG, JPG, PNG. Не стоит указывать на фото номера телефонов, адрес эл. почты или ссылки на другие сайты. Для нормального отображения Вашего изображение на нашем сайте старайтесь загружать картинки с максимально равными шириной и высотой (старайтесь не загружать картинки с шириной намного выше чем высота так как при отображении они будут сжиматся по ширине).</h5>
		<div class="row" style="margin-bottom: 10px;">
			<form id="frm">
	        	<input style="width: 320px; margin:auto;" type="file" id="uploadbtn" multiple />
	        </form>
		</div>
		<div class="row">

			<?php if ($model->id) { ?>

				<!-- Блок первой картинки -->
				<div class="col-sm-3 col-xs-12" id="img-0">
					<?php if(!empty($model->attributes['image'])){ ?>
						<div class="row" style="text-align: justify;">
							<input type="checkbox" id="checkbox_1" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%;" onclick="VyborMag(this)">
							<label style="margin-top: 0px; margin-top: 6px;">Выбрать главным</label>
						</div>
						<div class="row" style="height: 190px;"">
							<img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/store/product/190x190_<?php echo $model->attributes['image'];?>">
							<input type="hidden" name="Product[image0]" value="0">
							<input type="hidden" id="isset_img0" value="1">
						</div>
						<div class="row" style="text-align:center; margin-bottom:5px;cursor:pointer;" onclick="DeleteImage(1)">
							<span>Удалить изображение</span>
						</div>
					<?php } else { ?>
						<input type="checkbox" id="checkbox_1" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)">
						<img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png">
						<input type="hidden" name="Product[image0]" value="0">
						<input type="hidden" id="isset_img0" value="0">
					<?php } ?>
				</div>
				<!-- Блок первой картинки -->

				<!-- Блок второй картинки -->
				<div class="col-sm-3 col-xs-12" id="img-1">
					<?php if(!empty($model->attributes['image1'])){ ?>
						<div class="row" style="text-align: justify;">
							<input type="checkbox" id="checkbox_2" name="Product[checkbox_2]" value="0" style="float: left; width:20%;" onclick="VyborMag(this)">
							<label style="margin-top: 0px; margin-top: 6px;">Выбрать главным</label>
						</div>
						<div class="row" style="height: 190px;"">
							<img style="width:100%;" src="https://domstroy.com.ua/uploads/store/product/<?php echo $model->attributes['image1'];?>">
							<input type="hidden" name="Product[image1]" value="0">
							<input type="hidden" id="isset_img1" value="1">
						</div>
						<div class="row" style="text-align:center; margin-bottom:5px;cursor:pointer;" onclick="DeleteImage(2)">
							<span>Удалить изображение</span>
						</div>
					<?php } else { ?>
						<input type="checkbox" id="checkbox_2" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)">
						<img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png">
						<input type="hidden" name="Product[image1]" value="0">
						<input type="hidden" id="isset_img1" value="0">
					<?php } ?>
				</div>
				<!-- Блок второй картинки -->

				<!-- Блок третьей картинки -->
				<div class="col-sm-3 col-xs-12" id="img-2">
					<?php if(!empty($model->attributes['image2'])){ ?>
						<div class="row" style="text-align: justify;">
							<input type="checkbox" id="checkbox_3" name="Product[checkbox_3]" value="0" style="float: left; width:20%;" onclick="VyborMag(this)">
							<label style="margin-top: 0px; margin-top: 6px;">Выбрать главным</label>
						</div>
						<div class="row" style="height: 190px;"">
							<img style="width:100%;" src="https://domstroy.com.ua/uploads/store/product/<?php echo $model->attributes['image2'];?>">
							<input type="hidden" name="Product[image2]" value="0">
							<input type="hidden" id="isset_img2" value="1">
						</div>
						<div class="row" style="text-align:center; margin-bottom:5px;cursor:pointer;" onclick="DeleteImage(3)">
							<span>Удалить изображение</span>
						</div>
					<?php } else { ?>
						<input type="checkbox" id="checkbox_3" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)">
						<img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png">
						<input type="hidden" name="Product[image2]" value="">
						<input type="hidden" id="isset_img2" value="0">
					<?php } ?>
				</div>
				<!-- Блок третьей картинки -->

				<!-- Блок четвертой картинки -->
				<div class="col-sm-3 col-xs-12" id="img-3">
					<?php if(!empty($model->attributes['image3'])){ ?>
						<div class="row" style="text-align: justify;">
							<input type="checkbox" id="checkbox_4" name="Product[checkbox_4]" value="0" style="float: left; width:20%;" onclick="VyborMag(this)">
							<label style="margin-top: 0px; margin-top: 6px;">Выбрать главным</label>
						</div>
						<div class="row" style="height: 190px;">
							<img style="width:100%;" src="https://domstroy.com.ua/uploads/store/product/<?php echo $model->attributes['image3'];?>">
							<input type="hidden" name="Product[image3]" value="0">
							<input type="hidden" id="isset_img3" value="1">
						</div>
						<div class="row" style="text-align:center; margin-bottom:5px;cursor:pointer;" onclick="DeleteImage(4)">
							<span>Удалить изображение</span>
						</div>
					<?php } else { ?>
						<input type="checkbox" id="checkbox_4" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)">
						<img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png">
						<input type="hidden" name="Product[image3]" value="">
						<input type="hidden" id="isset_img3" value="0">
					<?php } ?>
				</div>
				<!-- Блок четвертой картинки -->

			<?php } else { ?>
			<div class="col-sm-3 col-xs-12" id="img-0">
				<input type="checkbox" id="checkbox_1" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)">
				<img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png">
				<input type="hidden" name="Product[image0]" value="">
				<input type="hidden" id="isset_img0" value="0">
			</div>
			<div class="col-sm-3 col-xs-12"  id="img-1">
				<input type="checkbox" id="checkbox_2" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)">
				<img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png">
				<input type="hidden" name="Product[image1]" value="">
				<input type="hidden" id="isset_img1" value="0">
			</div>
			<div class="col-sm-3 col-xs-12"  id="img-2">
				<input type="checkbox" id="checkbox_3" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)">
				<img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png">
				<input type="hidden" name="Product[image2]" value="">
				<input type="hidden" id="isset_img2" value="0">
			</div>
			<div class="col-sm-3 col-xs-12"  id="img-3">
				<input type="checkbox" id="checkbox_4" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)">
				<img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png">
				<input type="hidden" name="Product[image3]" value="">
				<input type="hidden" id="isset_img3" value="0">
			</div>
			<?php } ?>
			<input type="hidden" name="Product[select_image_magor]" id="select_image_magor" value="1">
		</div>

 	</fieldset>
  	<fieldset style="border-bottom: 1px solid #ccc;" id="status_tovara">
  		<div class="row">
			<div class="col-sm-4 col-xs-12">
				<?= $form->dropDownListGroup(
					$model,
					'status',
					[
						'widgetOptions' => [
							'data' => $model->getStatusList($model->company->is_active),
						],
						'tooltip' => array(
							'label' => '?',
							'context' => 'info',
							'size' => '',
							'htmlOptions' => array(
								'class' => 'cabinet_product_popover',
								'data-title' => Yii::t('site','help-tooltip'),
								'data-placement' => 'top',
								'data-content' => $model->getAttributeDescription('status'),
								'data-toggle' => 'popover',
								'data-html'=> true,
							),
						)
					]
					);
				?>
			</div>
		</div>
	  <div class="row">
	    <div class="col-sm-12 center-text">
	      <?php $this->widget(
	        'bootstrap.widgets.TbButton',
	        [
	          'buttonType' => 'submit',
	          'label' => $model->getIsNewRecord() ? Yii::t('StoreModule.store', 'Add product and continue') : Yii::t(
	            'StoreModule.store',
	            'Save product and continue'
	          ),
	        ]
	      ); ?>
	    </div>
	  </div>
  	</fieldset>
</div>
	<fieldset style="border-bottom: 1px solid #ccc">

	</fieldset>
</div>

<?php $this->endWidget(); ?>

<!-- Модальное окно отображения ошибок заполнения формы -->
<div id="Modal2" class="modalDialog1">
     <div>
          <img id="img_cancel" style="width:18px; position: absolute; right: 5px; cursor: pointer;" src="/images/button_cancel.png" onclick="CloseModal2(this)" alt="Закрыть окно">
          <p style="color: red; font-size: 16px; font-weight: bold; margin-top: 30px; text-align: center;">Убидитесь что все обязательные поля заполнены</p>
          <div style="margin-top: 30px;">
               <p style="color: #000;" id="valid_name_tovar"></p>
               <p style="color: #000;" id="valid_category"></p>
               <p style="color: #000;" id="valid_edinica"></p>
               <p style="color: #000;" id="valid_attribute"></p>
               <p style="color: #000;" id="valid_description"></p>
          </div>
          <div style="margin-top: 30px;">
               <p style="text-align: center; font-size: 12px;">Также, за помощью, Вы можете обратится к своему менеджеру.</p>
          </div>
     </div>
</div>
<!-- Модальное окно поиска категории -->

<!-- Модальное окно поиска категории -->
<div id="Modal1" class="modalDialog1">
     <div>
          <img id="img_cancel" style="width:18px; position: absolute; right: 5px; cursor: pointer;" src="/images/button_cancel.png" alt="Закрыть модальное окно" onclick="CloseModal1(this)" alt="Закрыть окно">
          <h3>Поиск категории - даный функционал в разработке</h3>
          <input class="form-control" id="id_search" placeholder="Начните вводить значение" type="text" value="" onkeyup="SearchAjaxCat()">
          <div id="select_search" style="background-color: #fff;border-radius: 3px;">
          <ul style="line-height: 1.5;margin-left: -40px;">
               <li class="li_search" style="list-style: none; padding-left: 5px;">1-е значение</li>
               <li class="li_search" style="list-style: none; padding-left: 5px;">2-е значение</li>
               <li class="li_search" style="list-style: none; padding-left: 5px;">3-е значение</li>
               <li class="li_search" style="list-style: none; padding-left: 5px;">4-е значение</li>
               <li class="li_search" style="list-style: none; padding-left: 5px;">5-е значение</li>
          </ul>
          </div>
          <div style="margin-top: 30px;">
               <p style="text-align: center;">Также, за помощью, Вы можете связатся со своим менеджером.</p>
          </div>
     </div>
</div>
<!-- Модальное окно поиска категории -->
<style type="text/css">
#result img {
  width: 34%;
}
.li_search:hover{
	background-color: #ccc;
	color: #000;
	font-weight: bold;
}
</style>
<?php
  Yii::app()->getClientScript()->registerCssFile($this->getModule()->getAssetsUrl() . '/css/store-backend.css');
  Yii::app()->getClientScript()->registerCssFile(Yii::app()->createUrl("/css/cropper.min.css"));
?>
<!-- Скрипты для обработки действий в форме добавления или редактирования товаров или услуг -->
<script>
    var old_price = document.getElementById("Product_old_price");
    old_price.onblur = function() {
        if(old_price.value>0){
            if(!document.getElementById("Product_is_special").checked) {
                $("#Product_is_special").attr( "checked", "checked");
                $("#ytProduct_is_special").attr( "value", "1");
            }
        }
    }
	var element_suptype = document.getElementById("sup_type_id");
	var model_id = document.getElementById("model_id").value;
	if (model_id == 0) {
		if (element_suptype.value == 2){
			SelectType(element_suptype);
		}
	}
	//Здесь скрипт проверки валидности заполнения полей формы
	$('#product-form').submit(function(){
		var error_validate = 0;
		var error_description = '';
		var error_name = '';
		var error_edinica = '';
		var error_attributes = '';
		var error_categories = '';
		//Проверяем описание товара или услуги
		var description = document.getElementById("Product_description").value;
		var s1=description.replace(/<.*?>/g, "");
		var s_total = 350 - s1.length;
		if (s1.length<350) {
			$(".redactor-editor").css('border-color', 'red');
			if (s_total>0) {
				document.getElementById("descr_error").innerHTML = 'Осталось добавить к описанию '+s_total+' символов';
				var error_description = '- в описании не достает '+s_total+' символов';
			}
	  		error_validate = 1;
		}
		else{
			document.getElementById("descr_error").innerHTML = '';
			$(".redactor-editor").css('border-color', '#000');
			var error_description = '';
		}

		var name = document.getElementById("Product_name").value;
	  //Проверяем стационарные поля
	  if (document.getElementById("Product_name").value == '') {
	  	$("#Product_name").css('border-color', 'red');
	  	error_name = '- не указано имя товара/услуги';
	  	error_validate = 1;
	  }
	  else{
	  	$("#Product_name").css('border-color', '#ccc');
	  	error_name = '';
	  }
	  var cat = document.getElementById("finish_cat").value;
	  if (cat == '') {
	  	error_validate = 1;
	  	error_categories = '- не выбрана конечная категория';
	  }
	  else{
	  	error_categories = '';
	  }
	  if (document.getElementById("edinica").value == '0') {
	  	$("#edinica").css('border-color', 'red');
	  	error_validate = 1;
	  	error_edinica = '- не указано единицу измерения';
	  }
	  else{
	  	$("#edinica").css('border-color', '#ccc');
	  	error_edinica = '';
	  }

	  $('.select_required').each(function(){
	  	if ($(this).val() == 0) {
	  		$(this).css('border-color', 'red');
	  		error_validate = 1;
	  		error_attributes = '- не указаны все обязательные характеристики товара';
	  	}
	  	else{
	  		$(this).css('border-color', '#ccc');
	  		error_attributes = '';
	  	}
	  });
	  if (error_validate == 0){
	  	return true;
	  }
	  else{
	  	console.log(error_attributes);
	  	$('html, body').animate({scrollTop: 0},500);
	  	if (error_description != '') {
	  		document.getElementById("valid_description").innerHTML = error_description;
	  		document.getElementById("valid_description").style.display = 'block';
	    }
	    else{
	    	document.getElementById("valid_description").style.display = 'none';
	    }
	    if (error_name != '') {
	    	document.getElementById("valid_name_tovar").innerHTML = error_name;
	    	document.getElementById("valid_name_tovar").style.display = 'block';
	    }
	    else{
	    	document.getElementById("valid_name_tovar").style.display = 'none';
	    }
	    if (error_edinica != '') {
	    	document.getElementById("valid_edinica").innerHTML = error_edinica;
	    	document.getElementById("valid_edinica").style.display = 'block';
	    }
	    else{
	    	document.getElementById("valid_edinica").style.display = 'none';
	    }
	    if (error_attributes != '') {
			document.getElementById("valid_attribute").innerHTML = error_attributes;
			document.getElementById("valid_attribute").style.display = 'block';
	    }
	    else{
	    	document.getElementById("valid_attribute").style.display = 'none';
	    }
	    if (error_categories != '') {
			document.getElementById("valid_category").innerHTML = error_categories;
			document.getElementById("valid_category").style.display = 'block';
	    }
	    else{
	    	document.getElementById("valid_category").style.display = 'none';
	    }
	  	$("#Modal2").css('display', 'block');
	  	return false;
	  }
	});


	function OpenBlank(){
		alert('Пока не работает))))');
	}
	//Открывания модального окна для поиска категории
	function SearchCat(){
    	$("#Modal1").css('display', 'block');
	}
	function CloseModal1(){
		$("#Modal1").css('display', 'none');
	}
	function CloseModal2(){
		$("#Modal2").css('display', 'none');
	}
	function SearchAjaxCat(){
		var string_search = document.getElementById("id_search").value;
		var sup_type = document.getElementById("sup_type_id").value;
		var sear = string_search+'_'+sup_type;
		if (string_search!='') {
			$.get('<?= Yii::app()->createUrl('user/product/getcatsearch/');?>/' + sear, function (data) {
        	$("#select_search").empty().append(data);
			});
		}
	}
	//Выбор главных категорий по типу товар/услуга
	function SelectType(e) {
		//AJAX - запит
		$.get('<?= Yii::app()->createUrl('user/product/getmagorcategory/');?>/' + e.value, function (data) {
        	$("#Product_sup_type1").empty().append(data);
      	});
      	document.getElementById("finish_cat").value = '';
      	$("#Product_sup_type1").removeAttr("disabled");
      	$("#Product_sup_type2").empty().append('<option selected disabled>Выберите подкатегорию</option>');
      	$("#Product_sup_type2").prop('disabled', true);
      	$("#Product_sup_type3").empty().append('<option selected disabled>Выберите подкатегорию</option>');
      	$("#Product_sup_type3").prop('disabled', true);
      	$("#Product_sup_type4").empty().append('<option selected disabled>Выберите подкатегорию</option>');
      	$("#Product_sup_type4").prop('disabled', true);
      	$("#cat1").css('display', 'block');
      	$("#cat2").css('display', 'block');
      	$("#cat3").css('display', 'block');
      	$("#div_search").css('display', 'block');
      	$("#block-shablon").css('display', 'none');
      	//Вывод единиц измерения
      	$.get('<?= Yii::app()->createUrl('user/product/getedinicy/');?>/' + e.value, function (data) {
        	$("#edinica").empty().append(data);
        	$("#edinica").removeAttr("disabled");
      	});
      	//Прячем или показываем блок опубликовано/в архиве
      	if (e.value == 1) {
      		$("#stock").css('display', 'block');

      	}
      	if (e.value == 2) {
      		$("#stock").css('display', 'none');
      		$("#attribute_pan").css('display', 'none');
      		$("#attributes-panel").html('');
      	}
      	document.getElementById("Product_sup_type").value = e.value;
      	document.getElementById("sup_type_id").value = e.value;

	}
	//Выбор категорий
	function SelectCat(e){
		//AJAX - запит
		var id = e.id;
		var id_category = e.value;
		var numb = $("#"+id).data('findselect');

		//Удаляем все узлы начиная от текущей
		if (numb>0) {
			for (var i = numb; i < 4; i++) {
				$("#cat_input"+i).remove();
				document.getElementById("finish_cat").value = '';
				//Здесь записываем айди конечной категории в скрытое поле
			}
		}
		$("#categories").append('<input type="hidden" id="cat_input'+numb+'" name="categories['+numb+']" value="' + id_category + '" />');
		var numbfirst = numb+1;
		var id_type = document.getElementById("sup_type_id").value;
		$.get('<?= Yii::app()->createUrl('user/product/getcategory/');?>/' + e.value, function (data) {
			if (data!=''){
				$("#Product_sup_type"+numbfirst).empty().append(data);
				$("#Product_sup_type"+numbfirst).removeAttr("disabled");
				for (var i = numb; i < 4; i++) {
					$("#cat"+i).css('display', 'block');
					$("#Product_sup_type"+(i+2)).prop('disabled', true);
      				$("#Product_sup_type"+(i+2)).empty().append('<option selected disabled>Выберите подкатегорию</option>');
				}

				$("#block-shablon").css('display', 'none');
				$("#attribute_pan").css('display', 'none');
				$("#div_search").css('display', 'block');
			}
			if (data=='') {
				for (var i = numb; i < 4; i++) {
					$("#cat"+i).css('display', 'none');
					//Здесь записываем айди конечной категории в скрытое поле
					document.getElementById("finish_cat").value = id_category;
				}
				//Здесь еще вытягиваем и выводим параметры соотвтетственно от категории
				//Здесь проверяем наличия и выводим кнопку стандартного описания
				$.get('<?= Yii::app()->createUrl('user/product/getshabloncategory/');?>/' + id_category, function (data) {
					if (data == 'false') {
						//Если описания нету скрываем блок с кнопкой
						$("#block-shablon").css('display', 'none');
					}
					if (data !='false') {
						//Если описания есть открываем блок с кнопкой
						$("#block-shablon").css('display', 'block');
						document.getElementById("shablon_text").value = data;
					}
				});
				document.getElementById("category_tovar").value = id_category;
				//Если выбран тип товары то ищем и подтягиваем характеристики
				if (id_type == 1) {
					document.getElementById("finish_cat").value = id_category;
					$.get('<?= Yii::app()->createUrl('user/product/getattributecategory/');?>/' + id_category, function (data) {
					if (data !='') {
						$("#attribute_pan").css('display', 'block');
						$("#attributes-panel").html('');
						$("#attributes-panel").empty().append(data);
					}
					if (data == '') {
						$("#attribute_pan").css('display', 'none');
						$("#attributes-panel").html('');
					}
					});

				}
				$("#div_search").css('display', 'none');
			}
      	});
	}
	//Функция добавления шаблона описания
	function OpenBlank(){
		var id_shablon = document.getElementById("shablon_text").value;
	}
</script>
	<script>
		//Функция контроля флажков главной фотографии
		function VyborMag(e){
			var check = e.id;
			if ((check == 'checkbox_1')&&(e.checked == true)){
				//Скидываем все остальные
				document.getElementById("checkbox_1").value = 1;
				document.getElementById("checkbox_2").checked = false;
				document.getElementById("checkbox_2").value = 0;
				document.getElementById("checkbox_3").checked = false;
				document.getElementById("checkbox_3").value = 0;
				document.getElementById("checkbox_4").checked = false;
				document.getElementById("checkbox_4").value = 0;
				document.getElementById("select_image_magor").value = 1;
			}
			if ((check == 'checkbox_2')&&(e.checked == true)){
				//Скидываем все остальные
				document.getElementById("checkbox_2").value = 1;
				document.getElementById("checkbox_1").checked = false;
				document.getElementById("checkbox_1").value = 0;
				document.getElementById("checkbox_3").checked = false;
				document.getElementById("checkbox_3").value = 0;
				document.getElementById("checkbox_4").checked = false;
				document.getElementById("checkbox_4").value = 0;
				document.getElementById("select_image_magor").value = 2;
			}
			if ((check == 'checkbox_3')&&(e.checked == true)){
				//Скидываем все остальные
				document.getElementById("checkbox_3").value = 1;
				document.getElementById("checkbox_1").checked = false;
				document.getElementById("checkbox_1").value = 0;
				document.getElementById("checkbox_2").checked = false;
				document.getElementById("checkbox_2").value = 0;
				document.getElementById("checkbox_4").checked = false;
				document.getElementById("checkbox_4").value = 0;
				document.getElementById("select_image_magor").value = 3;
			}
			if ((check == 'checkbox_4')&&(e.checked == true)){
				//Скидываем все остальные
				document.getElementById("checkbox_4").value = 1;
				document.getElementById("checkbox_1").checked = false;
				document.getElementById("checkbox_1").value = 0;
				document.getElementById("checkbox_2").checked = false;
				document.getElementById("checkbox_2").value = 0;
				document.getElementById("checkbox_3").checked = false;
				document.getElementById("checkbox_3").value = 0;
				document.getElementById("select_image_magor").value = 4;
			}
		}
		var $ = jQuery.noConflict();
		$(document).ready(function() {
			var errMessage = 0;
			var dataArray = [];
			var image1 = document.getElementById("isset_img0").value;
	   		var image2 = document.getElementById("isset_img1").value;
	   		var image3 = document.getElementById("isset_img2").value;
	   		var image4 = document.getElementById("isset_img3").value;
	   		$("#uploadbtn").on('change', function() {
				var n = 0;
				var files = $(this)[0].files;
		   		if (image1!=0) {n = n+1;}
		   		if (image2!=0) {n = n+1;}
		   		if (image3!=0) {n = n+1;}
		   		if (image4!=0) {n = n+1;}
	   			if (n ==4) {
	   				alert('Не больше 4 изображений!');
	   				return false;
	   			}
	   			else{
	   				//Вызываем функцию загрузки изображений на предосмотр
	   				loadInView(files, n);
	   			}
	   		})
	   		function loadInView(files, n) {
	   			var maxSize = 5000000; //Макстмальный размер файла
	   			var n = files.length;
	   			var error_size = 0;
	   			$.each(files, function(index, file) {
	   				//проверяем на соответствие типа файла
	   				if (files[index].type !== "image/jpg" && files[index].type !== "image/jpeg" && files[index].type !== "image/png") {
	   					alert('Разрешается загружать только изображения в формате JPG, JPEG или PNG!');
	   				} else if (files[index].size/1024 > 5120) {
	   					alert('Разрешается загружать только изображения не более 5М');
	   				}
	   				else{
	   					//console.log(files[index].type.match('image.*'));
	   					//console.log(files[index].size);
	   					//Проверяем на соответствие разрешенного размера файла
	   					if (n>0) {if (files[0]['size']>maxSize) {error_size = 1; }}
				   		if (n>1) {if (files[1]['size']>maxSize) {error_size = 1; }}
				   		if (n>2) {if (files[2]['size']>maxSize) {error_size = 1; }}
				   		if (n>3) {if (files[3]['size']>maxSize) {error_size = 1; }}
				   		if (n>4) {
							alert('Разрешается загружать не более 4-х изображений!');
				   			return false;
				   		}
				   		// Проверяем на максимальное количество файлов
				   		if(error_size == 0){
				   			//Прошли все проверки
							// Создаем новый экземпляра FileReader
							var fileReader = new FileReader();
								// Инициируем функцию FileReader
								fileReader.onload = (function(file) {

									return function(e) {
										// Помещаем URI изображения в массив
										dataArray.push({name : file.name, value : this.result});
										addImage((dataArray.length-1));
									};

								})(files[index]);
							// Производим чтение картинки по URI
							fileReader.readAsDataURL(file);
				   		}
				   		else{
				   			alert('Разрешается загружать изображения размером не больше '+maxSize/1000000+' МБ!');
				   			return false;
				   		}

	   					//if (!files[index]['size']<maxSize) {
	   					//	alert('Разрешается загружать изображения не больше '+maxSize/1000000+' МБ!');
	   					//}
	   				}
	   			})
	   		}
			// Процедура добавления эскизов на страницу
			function addImage(ind) {
				// Если индекс отрицательный значит выводим весь массив изображений
				if (ind < 0 ) {
				start = 0; end = dataArray.length;
				} else {
				// иначе только определенное изображение
				start = ind; end = ind+1; }
				// Цикл для каждого элемента массива
				for (i = start; i < end; i++) {
					var image1 = document.getElementById("isset_img0").value;
			   		var image2 = document.getElementById("isset_img1").value;
			   		var image3 = document.getElementById("isset_img2").value;
			   		var image4 = document.getElementById("isset_img3").value;
					if (image1 == '0') {
						//Удаляем вместимое и записываем новое
						$('#img-0').html('');
						$('#img-0').append('<div class="row" style="text-align: justify;"><input type="checkbox" id="checkbox_1" name="Product[checkbox_1]" checked="checked" value="1" style="float: left; width:20%;" onclick="VyborMag(this)"><label style="margin-top: 0px; margin-top: 6px;">Выбрать главным</label></div>	<div id="img1" class="image col-sm-2 col-xs-6" style="background: url('+dataArray[i].value+'); background-size: cover; width:100%; height:190px;"></div><input type="hidden" name="Product[image0]" value="'+dataArray[i].value+'"><input type="hidden" id="isset_img0" value="1"><div class="row" style="text-align:center; margin-bottom:5px;cursor:pointer;" onclick="DeleteImage(1)"><span>Удалить изображение</span></div>');
					}
					else if (image2 == '0') {
						$('#img-1').html('');
						$('#img-1').append('<div class="row" style="text-align: justify;"><input type="checkbox" id="checkbox_2" name="Product[checkbox_2]" value="0" style="float: left; width:20%;" onclick="VyborMag(this)"><label style="margin-top: 0px; margin-top: 6px;">Выбрать главным</label></div>	<div id="img2" class="image col-sm-2 col-xs-6" style="background: url('+dataArray[i].value+'); background-size: cover; width:100%; height:190px;"></div><input type="hidden" name="Product[image1]" value="'+dataArray[i].value+'"><input type="hidden" id="isset_img1" value="1"><div class="row" style="text-align:center; margin-bottom:5px;cursor:pointer;" onclick="DeleteImage(2)"><span>Удалить изображение</span></div>');
					}
					else if (image3 == '0') {
						$('#img-2').html('');
						$('#img-2').append('<div class="row" style="text-align: justify;"><input type="checkbox" id="checkbox_3" name="Product[checkbox_3]" value="0" style="float: left; width:20%;" onclick="VyborMag(this)"><label style="margin-top: 0px; margin-top: 6px;">Выбрать главным</label></div><div id="img3" class="image col-sm-2 col-xs-6" style="background: url('+dataArray[i].value+'); background-size: cover; width:100%; height:190px;"></div><input type="hidden" name="Product[image2]" value="'+dataArray[i].value+'"><input type="hidden" id="isset_img2" value="1"><div class="row" style="text-align:center; margin-bottom:5px;cursor:pointer;" onclick="DeleteImage(3)"><span>Удалить изображение</span></div>');
					}
					else if (image4 == '0') {
						$('#img-3').html('');
						$('#img-3').append('<div class="row" style="text-align: justify;"><input type="checkbox" id="checkbox_4" name="Product[checkbox_4]" value="0" style="float: left; width:20%;" onclick="VyborMag(this)"><label style="margin-top: 0px; margin-top: 6px;">Выбрать главным</label></div>	<div id="img4" class="image col-sm-2 col-xs-6" style="background: url('+dataArray[i].value+'); background-size: cover; width:100%; height:190px;"></div><input type="hidden" name="Product[image3]" value="'+dataArray[i].value+'"><input type="hidden" id="isset_img3" value="1"><div class="row" style="text-align:center; margin-bottom:5px;cursor:pointer;" onclick="DeleteImage(4)"><span>Удалить изображение</span></div>');
					}
					else{
						alert('Вы не можете больше загружать изображения. Максимально допустимое значение 4!');
				   		return false;
					}
				}
				return false;
			}
		})
		function DeleteImage(i){
			if (i == 1) {
				$('#img-0').html('');
				$('#img-0').append('<input type="checkbox" id="checkbox_1" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)"><img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png"><input type="hidden" name="Product[image0]" value="">				<input type="hidden" id="isset_img0" value="0">');
			}
			if (i == 2) {
				$('#img-1').html('');
				$('#img-1').append('<input type="checkbox" id="checkbox_2" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)"><img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png"><input type="hidden" name="Product[image1]" value="">				<input type="hidden" id="isset_img1" value="0">');
			}
			if (i == 3) {
				$('#img-2').html('');
				$('#img-2').append('<input type="checkbox" id="checkbox_3" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)"><img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png"><input type="hidden" name="Product[image2]" value="">				<input type="hidden" id="isset_img2" value="0">');
			}
			if (i == 4) {
				$('#img-3').html('');
				$('#img-3').append('<input type="checkbox" id="checkbox_4" checked="checked" name="Product[checkbox_1]" value="0" style="float: left; width:20%; display: none;" onclick="VyborMag(this)"><img style="width:100%;" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png"><input type="hidden" name="Product[image3]" value="">				<input type="hidden" id="isset_img3" value="0">');
			}
		}
	</script>
