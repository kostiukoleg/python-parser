<style>
.horizontal{
    max-height: 240px;
}
.horizontal img{
    margin-top:10px;
    max-height: 140px;
}
.price2{
    text-align: center;
}
.vertical img{
    max-height: 100px;
    margin-top: -10px;
}
.horizontal .materials-inner{
    padding:0;
}
.horizontal .materials-text {
    padding-bottom: 0px;
}
.horizontal > div > div.materials-text.clearfix > div.executor > div > p.price2{

}
#yw2 > div.row.items > div.horizontal > div > div.materials-text.clearfix > div > div > div:nth-child(2) > p:nth-child(1){
    float:right;
}
#yw2 > div.row.items > div > div > div.materials-text.clearfix > div > div > div:nth-child(2) > p:nth-child(1) > span{
    color: #5fb543;
    font-size:20px;
    font-weight:bold;
    text-shadow: 1px 1px 0px #fff, -2px 1px 0px rgba(0,0,0,0.15);
}
#yw2 > div.row.items > div.horizontal > div > div.materials-text.clearfix > div > div > div:nth-child(2) > p:nth-child(2){
    float: right;
    margin-top: 30px;
}
.price2{
    min-width: 220px;
}
@media (max-width: 1165px) {
    .price2{min-width: 0px;}
}
@media (max-width: 996px) {
    .horizontal {max-height: 1000px;}
    #yw2 > div.row.items > div.horizontal > div > div.materials-text.clearfix > div > div > div:nth-child(2) > p:nth-child(1){float:left;width: 100%;text-align: left;}
}
@media (max-width: 768px) {
    .materials-text {margin-top:140px;font-size: 18px;text-align: center;}
    #yw2 > div.row.items > div.horizontal > div > div.materials-text.clearfix > div > div > div:nth-child(2) > p:nth-child(1){text-align:center;}
    #yw2 > div.row.items > div.horizontal > div > div.materials-text.clearfix > div > div > div:nth-child(2) > p:nth-child(2){width:100%;}
    .coints {top:0px;}
    .price2{min-width: 220px;}
}
</style>
<?php
	$this->title = 'ДомСтрой -поиск';
?>
<section class="materials section-color">
<div class="container">
<div class="row" style="margin: 0px; padding-left: 20px;">
	<?php if ($all_pos > 0){ ?>
	<div class="col-sm-12">
		<div class="block_find" style="padding: 5px;background-color: #f1efef;border-bottom: 1px solid #ccc;">
			<div id="all_pos" style="padding:5px;text-align: left; float:left; cursor:pointer;" onclick="Allpos()">
				<span class="sel_span" style="color:#244A64;">Все результаты - <?php echo $all_pos;?>,</span>
			</div>
			<div style="padding:5px; text-align: left; float:left;cursor:pointer;" onclick="AllProducts()">
				<span class="sel_span" style="color:#244A64;">Товаров - <?php echo $num_products;?>, </span>
			</div>
			<div style="padding:5px; text-align: left;cursor:pointer;" onClick="AllService()">
				<span class="sel_span" style="color:#244A64;">Услуг - <?php echo $num_service;?></span>
			</div>
		</div>
	</div>
	<div class="col-sm-12">
		<h2 id="title_search">Результаты поиска товаров и услуг по запросу - "<?php echo $request;?>"</h2>
		<h2 id="title_search_product" style="display: none;">Результаты поиска товаров по запросу - "<?php echo $request;?>"</h2>
		<h2 id="title_search_service" style="display: none;">Результаты поиска услуг по запросу - "<?php echo $request;?>"</h2>
		<input type="hidden" id="request" value="<?php echo $request; ?>">
		<input type="hidden" id="region_select" value="0">
		<input type="hidden" id="city_select" value="0">
		<input type="hidden" id="category_select" value="0">
		<input type="hidden" id="pricefrom" value="0">
		<input type="hidden" id="priceto" value="0">
		<input type="hidden" id="page" value="0">
		<input type="hidden" id="page_num" value="1">
		<input type="hidden" id="view" value="list">
		<input type="hidden" id="sup_type" value="0">
		<input type="hidden" id="category_id" value="0">
	</div>
	<?php } ?>
	<div class="row">
		<?php if ($all_pos > 0){ ?>
		<div class="col-sm-3" style="padding-top:25px;">
			<h5>Найденые предложения в категориях</h5>
			<div id="category_block" class="block_find" style="padding: 5px;background-color: #f1efef;border-bottom: 1px solid #ccc;color: #214965;height: 220px;overflow: hidden;line-height: 1.1;">
				<?php foreach ($category_list as $key=>$c_l) {
					echo '<li class="select_cat" id="id_'.$key.'" style="height:30px; list-style-type: none;cursor:pointer;color: #3498db;" data-id="'.$key.'" data-name="'.$c_l['name'].'" onclick="SelectCategory(this)">'.$c_l['name'].' - ('.$c_l['count'].')</li></br>';
				} ?>
			</div>
			<?php if(count($category_list>5)){ ?>
				<div id="cat_hidden" style="text-align: center;">
					<p style="cursor:pointer;" onclick="AllCat()">Показать все категории</p>
				</div>
				<div id="cat_vasible" style="text-align: center;display:none;">
					<p style="cursor:pointer;" onclick="AllCatHide()">Скрыть</p>
				</div>
			<?php } ?>
			<div class="block_find" style="padding: 5px;background-color: #f1efef;border-bottom: 1px solid #ccc;">
				<h5>Выберите область</h5>
				<select id="region_selector" class="form-control" onchange="SelectRegion(this)">
					<option value="0" selected>---</option>
					<?php foreach ($list_regions as $key => $l_r) { ?>
						<option value="<?php echo $key; ?>" data-name="<?php echo $l_r['title']; ?>"><?php echo $l_r['title']; ?> (<?php echo $l_r['count']; ?>)</option>
					<?php } ?>
				</select>
			</div>
			<?php  ?>
			<div class="block_find" style="padding: 5px;background-color: #f1efef;border-bottom: 1px solid #ccc;">
				<h5>Выберите город:</h5>
				<select id="city_selector" disabled="disabled" class="form-control" onchange="SelectCity(this)">
					<option value="0">---</option>
					<?php foreach ($list_city as $key => $l_c) { ?>
						<option value="<?php echo $key; ?>" data-name="<?php echo $l_c['title']; ?>"><?php echo $l_c['title']; ?> (<?php echo $l_c['count']; ?>)</option>
					<?php } ?>
				</select>
			</div>
			<div class="block_find" style="padding: 5px;background-color: #f1efef;border-bottom: 1px solid #ccc;">
				<h5>Диапазон цен:</h5>
				<div class="row" style="margin: 0px;">
					<div class="col-sm-6">
						<input class="form-control" placeholder="от" type="number" name="price_from" id="price_from" value="0" min="0">
					</div>
					<div class="col-sm-6">
						<input class="form-control" placeholder="от" type="number" name="price_to" id="price_to" value="0" min="0">
					</div>
					<div class="col-sm-6">

					</div>
				</div>
				<div class="row" style="margin-top: 10px;">
					<div class="btn_price" style="width: 50%;margin: auto;text-align: center;padding:10px;background-color:#ccc;border-radius: 5px;cursor:pointer;" onclick="SelectPrice()">
						<span>Применить</span>
					</div>
				</div>
			</div>
		</div>
	    <div class="col-sm-9" style="margin-bottom: 50px;">
	    	<div id="grid_and_list" class="row clearfix hidden-xs hidden-sm">
                <div class="pull-left" style="display: none; float:left;">
                    <button id="grid" class="btn glyphicon glyphicon-th-large" onclick="SelectGrid()"></button>
                    <button id="list" class="btn glyphicon glyphicon-align-justify active" onclick="SelectList()"></button>
                </div>
				<div class="select_filters">
					<p id="reset_category" style="padding: 5px;background-color: #00CD6F;width: fit-content;border-radius: 6px;color: #fff;font-weight: bold;float: left;margin-left: 10px; display:none;"><span id="name_category_res"></span><span style="margin-left: 10px;font-size: 15px;cursor: pointer;" onclick="ResetCategory()">х</span></p>
					<p id="reset_region" style="padding: 5px;background-color: #00CD6F;width: fit-content;border-radius: 6px;color: #fff;font-weight: bold;float: left;margin-left: 10px; display:none;"><span id="name_region_res"></span><span style="margin-left: 10px;font-size: 15px;cursor: pointer;" onclick="ResetRegion()">х</span></p>
					<p id="reset_city" style="padding: 5px;background-color: #00CD6F;width: fit-content;border-radius: 6px;color: #fff;font-weight: bold;float: left;margin-left: 10px; display:none;"><span id="name_city_res"></span><span style="margin-left: 10px;font-size: 15px;cursor: pointer;" onclick="ResetCity()">х</span></p>
					<p id="reset_price" style="padding: 5px;background-color: #00CD6F;width: fit-content;border-radius: 6px;color: #fff;font-weight: bold;float: left;margin-left: 10px; display:none;"><span id="name_price_res"></span><span style="margin-left: 10px;font-size: 15px;cursor: pointer;" onclick="ResetPrice()">х</span></p>
				</div>
            </div>
			<section id="all_results_search" style="display: none;">

	        </section>
            <?php if($num_products > 0){ ?>
	        <section id="prosucts_search">
	        	<h2>Товары - <?php echo $num_products; ?></h2>
	        	<input type="hidden" id="count_tovar" value="<?php echo $num_products; ?>">
	        	<?php
	        	if (count($result_search_product)>0){
	        		echo '<div id="yw2" class="list-view">';
	        		echo '<div class="row items">';
	        		//Выводим первых пять найденых товаров
	            	foreach ($result_search_product as $key => $p_s) {
						if ($p_s['price'] == 0) {
							$price = 'Договорная';
						} else{
							if (!empty($p_s['value'])) {
								$price = number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн';
							}
							else{
								$price = number_format($p_s['price'], 2, ',', ' ').' грн';
							}
						}
						echo '<div class="materials-box col-sm-4 top horizontal">';
						echo '<div class="materials-inner" style="position: relative;">';
						echo '<div class="materials-img" style="min-height: 80px;">';
						echo '<a href="/product/'.$p_s['slug'].'.html">';
						if ((!empty($p_s['image']))) {
							echo '<img style="" class="img-responsive" src="https://domstroy.com.ua/uploads/store/product/'.$p_s['image'].'" alt="'.$p_s['name'].'" title="'.$p_s['name'].'" />';
						} else{
							echo '<img style="height:100%;" class="img-responsive" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png" alt="'.$p_s['name'].'" title="'.$p_s['name'].'">';
						}
						echo '</a>';
						if ($p_s['is_special'] > 0) {
							echo '<div><div class="mark mark-order" style="position: absolute;top: 5%;left: -1%; background-color: red; color:white;">Акция</div></div>';
						}
						if ($p_s['top_level'] > 0) {
						    echo '<div style="float: right;margin-right: 90px; margin-top:30px;"><span class="coints" style="*margin-left: 80%;">'.$p_s['top_level'].'</span></div>';
						}
						echo '</div>';
						echo '<div class="materials-text clearfix">';
						echo '<a href="/product/'.$p_s['slug'].'.html">';
						echo '<span style="margin: 0; padding: 0;font-weight: bold;" class="title">'.$p_s['name'].'</span>';
						echo '</a>';
						echo '</br>';
						echo '</br>';
						echo '<span>Город: </span><span style="padding-top: 7px;padding-right: 12px;padding-bottom: 7px;border-radius: 5px;font-weight:bold;">'.$p_s['title'].'</span>';
						echo '<div class="executor" style="padding-top: 10px;">';
						echo '<ul>';
						echo '<li>Продавец <p><b>'.$p_s['company_name'].'</b></p></li>';
						echo '</ul>';
						echo '<div class="row" style="margin:0;padding:0;">';
						echo '<div class="col-sm-6" style="float: left;min-width: 220px;padding: 0;">';
						if($p_s['plan'] !== '5' && $p_s['plan'] !== '6' && $p_s['plan'] !== '21'){
						echo '<span style="font-weight: bold;">Контакты:</span></br>';
						if ($p_s['phone'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone'].'">';
						    echo '<span id="phone1_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_0_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="0" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						if ($p_s['phone_2'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_2'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone_2'].'">';
						    echo '<span id="phone1_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_2'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_1_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="1" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_2'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						if ($p_s['phone_3'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_3'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone_3'].'">';
						    echo '<span id="phone1_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_3'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_2_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="2" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_3'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						if ($p_s['phone_4'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_4'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone_4'].'">';
						    echo '<span id="phone1_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_4'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_3_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="3" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_4'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						}
						echo '</div>';
						echo '<div>';
						echo '<p class="price2" style="font-size:18px;"><span style="color:red;">';
						if ($p_s['price'] == 0) {
							echo 'Договорная';
						} else{
							if (!empty($p_s['value'])) {
								echo ' '.number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн</span></p>';
							}
							else{
								echo ' '.number_format($p_s['price'], 2, ',', ' ').' грн</span></p>';
							}
						}
						echo '<p class="price2">';
						echo '<button style="float: right;float:unset;margin-bottom: 20px;" class="add-to-cart" onclick="cart.add('.$p_s['id'].'); return false;">Купить</button>';
						echo '</p>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
	            	}
		            	echo '<div class="materials-box col-sm-4 top horizontal" style="text-align: center;">';
		            	if ($num_products > 5) {
		            		echo '<span style="background-color: #2a4864;color: #fff!important;padding-left: 15px;padding-right: 15px;padding-top: 15px;padding-bottom: 15px;border-radius: 5px;" onclick="AllProducts()">Показать все найденые товары</span>';
		            	}
		            	echo '</div>';
	            }
	            echo '</div>';
	            echo '</div>';
	        	?>
	        </section>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- товар медий -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-6474835912470070"
     data-ad-slot="9992023016"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
			<?php } ?>

	        <section id="service_search">
	        	<h2>Услуги - <?php echo $num_service; ?></h2>
	        	<?php
	        	if (count($result_search_servise)>0){
	        		echo '<div id="yw2" class="list-view">';
	        		echo '<div class="row items">';
	            	foreach ($result_search_servise as $key => $p_s) {
            		//вычисляем цену
					if ($p_s['price'] == 0) {
						$price = 'Договорная';
					} else{
						if (!empty($p_s['value'])) {
							$price = number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн';
						}
						else{
							$price = number_format($p_s['price'], 2, ',', ' ').' грн';
						}
					}
						echo '<div class="materials-box col-sm-4 top horizontal">';
						echo '<div class="materials-inner" style="position: relative;">';
						echo '<div class="materials-img" style="min-height: 80px;">';
						echo '<a href="/product/'.$p_s['slug'].'.html">';
						if ((!empty($p_s['image']))) {
							echo '<img style="" class="img-responsive" src="https://domstroy.com.ua/uploads/store/product/'.$p_s['image'].'" alt="'.$p_s['name'].'" title="'.$p_s['name'].'" />';
						} else{
							echo '<img style="height:100%;" class="img-responsive" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png" alt="'.$p_s['name'].'" title="'.$p_s['name'].'">';
						}
						echo '</a>';
						if ($p_s['is_special'] > 0) {
							echo '<div><div class="mark mark-order" style="position: absolute;top: 5%;left: -1%; background-color: red; color:white;">Акция</div></div>';
						}
						if ($p_s['top_level'] > 0) {
						    echo '<div style="float: right;margin-right: 90px; margin-top:30px;"><span class="coints" style="*margin-left: 80%;">'.$p_s['top_level'].'</span></div>';
						}
						echo '</div>';
						echo '<div class="materials-text clearfix">';
						echo '<a href="/product/'.$p_s['slug'].'.html">';
						echo '<span style="margin: 0; padding: 0;font-weight: bold;" class="title">'.$p_s['name'].'</span>';
						echo '</a>';
						echo '</br>';
						echo '</br>';
						echo '<span>Город: </span><span style="padding-top: 7px;padding-right: 12px;padding-bottom: 7px;border-radius: 5px;font-weight:bold;">'.$p_s['title'].'</span>';
						echo '<div class="executor" style="padding-top: 10px;">';
						echo '<ul>';
						echo '<li>Продавец <p><b>'.$p_s['company_name'].'</b></p></li>';
						echo '</ul>';
						echo '<div class="row" style="margin:0;padding:0;">';
						echo '<div class="col-sm-6" style="float: left;min-width: 220px;padding: 0;">';
						if($p_s['plan'] !== '5' && $p_s['plan'] !== '6' && $p_s['plan'] !== '21'){
						echo '<span style="font-weight: bold;">Контакты:</span></br>';
						if ($p_s['phone'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone'].'">';
						    echo '<span id="phone1_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_0_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="0" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						if ($p_s['phone_2'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_2'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone_2'].'">';
						    echo '<span id="phone1_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_2'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_1_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="1" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_2'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						if ($p_s['phone_3'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_3'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone_3'].'">';
						    echo '<span id="phone1_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_3'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_2_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="2" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_3'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						if ($p_s['phone_4'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_4'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone_4'].'">';
						    echo '<span id="phone1_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_4'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_3_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="3" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_4'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						}
						echo '</div>';
						echo '<div>';
						echo '<p class="price2" style="font-size:18px;"><span style="color:red;">';
						if ($p_s['price'] == 0) {
							echo 'Договорная';
						} else{
							if (!empty($p_s['value'])) {
								echo ' '.number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн</span></p>';
							}
							else{
								echo ' '.number_format($p_s['price'], 2, ',', ' ').' грн</span></p>';
							}
						}
						echo '<p class="price2">';
						echo '<button style="float: right;float:unset;margin-bottom: 20px;" class="add-to-cart" onclick="cart.add('.$p_s['id'].'); return false;">Купить</button>';
						echo '</p>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
	            	}
	            	echo '<div class="materials-box col-sm-4 top horizontal" style="text-align: center;">';
	            	if ($num_service > 5) {
	            		echo '<span style="background-color: #2a4864;color: #fff!important;padding-left: 15px;padding-right: 15px;padding-top: 15px;padding-bottom: 15px;border-radius: 5px;" onclick="AllService()">Показать все найденые услуги</span>';
	            	}
	            	echo '</div>';
	            }
	           	echo '</div>';
	            echo '</div>';
	        	?>
	        </section>

            <?php if($num_products == 0){ ?>
	        <section id="prosucts_search">
	        	<h2>Товары - <?php echo $num_products; ?></h2>
	        	<input type="hidden" id="count_tovar" value="<?php echo $num_products; ?>">
	        	<?php
	        	if (count($result_search_product)>0){
	        		echo '<div id="yw2" class="list-view">';
	        		echo '<div class="row items">';
	        		//Выводим первых пять найденых товаров
	            	foreach ($result_search_product as $key => $p_s) {
	            		//вычисляем цену
						if ($p_s['price'] == 0) {
							$price = 'Договорная';
						} else{
							if (!empty($p_s['value'])) {
								$price = number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн';
							}
							else{
								$price = number_format($p_s['price'], 2, ',', ' ').' грн';
							}
						}
						echo '<div class="materials-box col-sm-4 top horizontal">';
						echo '<div class="materials-inner" style="position: relative;">';
						echo '<div class="materials-img" style="min-height: 80px;">';
						echo '<a href="/product/'.$p_s['slug'].'.html">';
						if ((!empty($p_s['image']))) {
							echo '<img style="" class="img-responsive" src="https://domstroy.com.ua/uploads/store/product/'.$p_s['image'].'" alt="'.$p_s['name'].'" title="'.$p_s['name'].'" />';
						} else{
							echo '<img style="height:100%;" class="img-responsive" src="https://domstroy.com.ua/uploads/thumbs/avatars/10000x10000_avatar.png" alt="'.$p_s['name'].'" title="'.$p_s['name'].'">';
						}
						echo '</a>';
						if ($p_s['is_special'] > 0) {
							echo '<div><div class="mark mark-order" style="position: absolute;top: 5%;left: -1%; background-color: red; color:white;">Акция</div></div>';
						}
						if ($p_s['top_level'] > 0) {
						    echo '<div style="float: right;margin-right: 90px; margin-top:30px;"><span class="coints" style="*margin-left: 80%;">'.$p_s['top_level'].'</span></div>';
						}
						echo '</div>';
						echo '<div class="materials-text clearfix">';
						echo '<a href="/product/'.$p_s['slug'].'.html">';
						echo '<span style="margin: 0; padding: 0;font-weight: bold;" class="title">'.$p_s['name'].'</span>';
						echo '</a>';
						echo '</br>';
						echo '</br>';
						echo '<span>Город: </span><span style="padding-top: 7px;padding-right: 12px;padding-bottom: 7px;border-radius: 5px;font-weight:bold;">'.$p_s['title'].'</span>';
						echo '<div class="executor" style="padding-top: 10px;">';
						echo '<ul>';
						echo '<li>Продавец <p><b>'.$p_s['company_name'].'</b></p></li>';
						echo '</ul>';
						echo '<div class="row" style="margin:0;padding:0;">';
						echo '<div class="col-sm-6" style="float: left;min-width: 220px;padding: 0;">';
						if($p_s['plan'] !== '5' && $p_s['plan'] !== '6' && $p_s['plan'] !== '21'){
						echo '<span style="font-weight: bold;">Контакты:</span></br>';
						if ($p_s['phone'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone'].'">';
						    echo '<span id="phone1_0_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_0_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="0" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						if ($p_s['phone_2'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_2'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone_2'].'">';
						    echo '<span id="phone1_1_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_2'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_1_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="1" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_2'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						if ($p_s['phone_3'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_3'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone_3'].'">';
						    echo '<span id="phone1_2_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_3'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_2_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="2" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_3'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						if ($p_s['phone_4'] !=''){
						    echo '<div style="width: 100%;">';
						    echo '<span id="phone0_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;">'.substr_replace($p_s['phone_4'],'xxx-xx-xx', 8).'&nbsp;</span>';
						    echo '<a href="tel:'.$p_s['phone_4'].'">';
						    echo '<span id="phone1_3_'.$p_s['company_id'].'_'.$p_s['id'].'" style="color:#003366;float: left;display: none; cursor: default; width:100%;">'.$p_s['phone_4'].'</span>';
						    echo '</a>';
						    echo '<span id="phone2_3_'.$p_s['company_id'].'_'.$p_s['id'].'" data-number="3" data-product="'.$p_s['id'].'" data-company="'.$p_s['company_id'].'" data-phone="'.$p_s['phone_4'].'" data-name="'.$p_s['name'].'" data-companyname="'.$p_s['company_name'].'" data-price="'.$price.'" style="color:#003366; border-bottom: 1px dotted;" onClick="VisiblePhone(this)"> показать</span>';
						    echo '</div>';
						}
						}
						echo '</div>';
						echo '<div>';
						echo '<p class="price2" style="font-size:18px;"><span style="color:red;">';
						if ($p_s['price'] == 0) {
							echo 'Договорная';
						} else{
							if (!empty($p_s['value'])) {
								echo ' '.number_format($p_s['price']*$p_s['value'], 2, ',', ' ').' грн</span></p>';
							}
							else{
								echo ' '.number_format($p_s['price'], 2, ',', ' ').' грн</span></p>';
							}
						}
						echo '<p class="price2">';
						echo '<button style="float: right;float:unset;margin-bottom: 20px;" class="add-to-cart" onclick="cart.add('.$p_s['id'].'); return false;">Купить</button>';
						echo '</p>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
	            	}
		            	echo '<div class="materials-box col-sm-4 top horizontal" style="text-align: center;">';
		            	if ($num_products > 5) {
		            		echo '<span style="background-color: #2a4864;color: #fff!important;padding-left: 15px;padding-right: 15px;padding-top: 15px;padding-bottom: 15px;border-radius: 5px;" onclick="AllProducts()">Показать все найденые товары</span>';
		            	}
		            	echo '</div>';
	            }
	            echo '</div>';
	            echo '</div>';
	        	?>
	        </section>
			<?php } ?>
	    </div>
	    <?php } else { ?>
	    	<div class="col-sm-12">
	    		<h3>За запросом "<?php echo $request; ?>" - ничего не найдено. Перейдите на <a href="/">главную страницу</a> или уточните поиск</h3>
	    	</div>
	    <?php } ?>
    </div>
</div>
</div>

</section>
<!-- Модальное окно поиска категории -->
<div id="Modal1" class="modalDialog1">
	<div class="container" style="width: 120px; margin:20% auto; background: none;">
		<div class="contentBar">
	    	<div id="block_1" class="barlittle"></div>
	        <div id="block_2" class="barlittle"></div>
	        <div id="block_3" class="barlittle"></div>
	        <div id="block_4" class="barlittle"></div>
	        <div id="block_5" class="barlittle"></div>
	    </div>
	</div>
</div>
<p id="back-top" style="width:60px;position: fixed;bottom: 10px;left: 20px;z-index: 10;cursor: pointer;"><a href="#top"><span></span></a></p>

<style>
.select_cat:hover{
	opacity: 0.6;
}
.active_cat{
	font-weight: 900;
    font-size: 15px;
}
h5{
	font-weight: bold;
}
#title_search, #title_search_product, #title_search_service{
font-family: inherit;
    font-weight: 500;
    line-height: 1.1;
    color: inherit;
    font-size: 34px;
}
#list, #grid{
	font-size: 17px;
    color: #d8e0eb;
    padding: 13px 12px 8px;
    border: none;
    outline: none;
    background: transparent;
    display: inline-block;
}
#list:hover, #grid:hover{
	color: #284765;
}
#list.active{
	color: #284765;
	font-size: 17px;
}
#grid.active{
	color: #284765;
	font-size: 17px;
}
.sel_span:hover{
	color:#000;
}
.btn_price:hover{
	background-color: #969494;
	color:#fff;
}
.barlittle {
	background-color:#2187e7;
	background-image: -moz-linear-gradient(45deg, #2187e7 25%, #a0eaff);
	background-image: -webkit-linear-gradient(45deg, #2187e7 25%, #a0eaff);
	border-left:1px solid #111; border-top:1px solid #111; border-right:1px solid #333; border-bottom:1px solid #333;
	width:10px;
	height:10px;
	float:left;
	margin-left:5px;
    opacity:0.1;
	-moz-transform:scale(0.7);
	-webkit-transform:scale(0.7);
	-moz-animation:move 1s infinite linear;
	-webkit-animation:move 1s infinite linear;
}
#block_1{
 	-moz-animation-delay: .4s;
	-webkit-animation-delay: .4s;
 }
#block_2{
 	-moz-animation-delay: .3s;
	-webkit-animation-delay: .3s;
}
#block_3{
 	-moz-animation-delay: .2s;
	-webkit-animation-delay: .2s;
}
#block_4{
 	-moz-animation-delay: .3s;
	-webkit-animation-delay: .3s;
}
#block_5{
 	-moz-animation-delay: .4s;
	-webkit-animation-delay: .4s;
}
@-moz-keyframes move{
	0%{-moz-transform: scale(1.2);opacity:1;}
	100%{-moz-transform: scale(0.7);opacity:0.1;}
}
@-webkit-keyframes move{
	0%{-webkit-transform: scale(1.2);opacity:1;}
	100%{-webkit-transform: scale(0.7);opacity:0.1;}
}
</style>
<div id="ModalF" class="modalDialog1">
	<div>
		<img id="img_cancel" style="width:18px; position: absolute; right: 5px; cursor: pointer;" src="/images/button_cancel.png" onclick="CloseModalF(this)" alt="Закрыть окно">
		<p style="color: #003366;text-align: center;line-height: 1.1; font-weight: bold; font-size: 18px;" id="name_product_w"></p>
		<span>Цена: </span><span style="color:#009900;" id="price_m"></span></br>
		<span id="name_shop_m" style="font-weight: bold;"></span></br>
		<span style="margin-top:15px; color:#003366; font-size:12px;">Пожайлуста, скажите продавцу, что Вы нашли это обьявление на Строительном портале ДомСтрой</span></br>
		<a id="clicktel">
			<p id="h4_phonemodal" style="text-align: center;color: #003366; cursor: default;"></p>
		</a>
		<p id="h5_phonemodal" style="text-align: center;color: #003366; cursor: default;"></p>
		<div style="margin-top: 30px;">
			<div class="btn btn-primary btn-block-back" id="success" data-company="" onclick="CloseModalF(this)" style="background-color: #009900; font-size: 14px;">
				Успешный звонок
			</div>
			<div class="btn btn-primary btn-block-back" id="unsuccess" data-company="" onclick="CloseModalF(this)" style="background-color: #990000;float:right;font-size:14px;">
				Не дозвонился
			</div>
		</div>
	</div>
</div>
<script>
    function VisiblePhone(e){
        var id = e.id;
        var number = $("#"+id).data('number');
        var product = $("#"+id).data('product');
        var company = $("#"+id).data('company');
        var company_name = $("#"+id).data('companyname');
        var product_name = $("#"+id).data('name');
        var price = $("#"+id).data('price');
        var phone = $("#"+id).data('phone');
        //показываем модальное окно
        document.getElementById("name_product_w").innerHTML = product_name;
        document.getElementById("price_m").innerHTML = price;
        document.getElementById("name_shop_m").innerHTML = company_name;
        document.getElementById("h4_phonemodal").innerHTML = phone;
        document.getElementById("h5_phonemodal").innerHTML = phone;
        $("#success").attr('data-company', company);
        $("#unsuccess").attr('data-company', company);
        document.getElementById("ModalF").style.display = 'block';
        $("#phone0_"+number+"_"+company+"_"+product).css('display', 'none');
        $(e).css('display', 'none');
        $("#phone1_"+number+"_"+company+"_"+product).css('display', 'block');
        $.ajax({
         url: '/store/product/vievphone/'+company+'/0',
         type: 'GET',
        });
        ga('send', 'event', 'Клик по кнопке показать', 'Страницы категорий');return true;
    }
    function CloseModalF(e){
	var check = e.id;
	var company = $("#"+check+"").data('company');
	//Функция обработки при удачном звонке
	if (check == 'success') {
		$.ajax({
	       url: '/store/product/vievphone/'+company+'/1',
	       type: 'GET',
	   	});
	}
	//Функция обработки при неудачном звонке
	if (check == 'unsuccess') {
		$.ajax({
	       url: '/store/product/vievphone/'+company+'/2',
	       type: 'GET',
	   	});
	}

    	document.getElementById("ModalF").style.display = 'none';
    }
//Скрипт кнопки вверх
$(document).ready(function(){
	// появление/затухание кнопки #back-top
	$(function (){
		// прячем кнопку #back-top
		$("#back-top").hide();

		$(window).scroll(function (){
			if ($(this).scrollTop() > 150){
				$("#back-top").fadeIn();
			} else{
				$("#back-top").fadeOut();
			}
		});

		// при клике на ссылку плавно поднимаемся вверх
		$("#back-top a").click(function (){
			$("body,html").animate({
				scrollTop:0
			}, 800);
			return false;
		});
	});
});
document.getElementById("q_search").value = document.getElementById("request").value;
var t;
function up() {
  var top = Math.max(document.body.scrollTop,document.documentElement.scrollTop);
  if(top > 0) {
    window.scrollBy(0,-20);
    t = setTimeout('up()',2);
  } else clearTimeout(t);
  return false;
}
function AllCat(){
	document.getElementById("cat_hidden").style.display = 'none';
	document.getElementById("cat_vasible").style.display = 'block';
	$("#category_block").css("height", "");
}
function AllCatHide(){
	document.getElementById("cat_hidden").style.display = 'block';
	document.getElementById("cat_vasible").style.display = 'none';
	$("#category_block").css("height", "220px");
	up();
}
function SelectCategory(e){
	var id_category = $(e).data('id');
	var name_category = $(e).data('name');
	document.getElementById("category_id").value = id_category;
	document.getElementById("region_select").value = 0;
	document.getElementById("city_select").value = 0;
	document.getElementById("reset_region").style.display = 'none';
	document.getElementById("reset_city").style.display = 'none';
	sup_type = document.getElementById("sup_type").value;
    if (sup_type != 0) {
    	document.getElementById("page_num").value = 1;
    	AllPosition();
    }
    else{
    	SelectPrice();
    }
	document.getElementById("cat_hidden").style.display = 'block';
	document.getElementById("cat_vasible").style.display = 'none';
	$("#category_block").css("height", "220px");
	$(".select_cat").removeClass('active_cat');
	$("#id_"+id_category).addClass('active_cat');
	$("#reset_category").css('display', 'block');
	$("#name_category_res").text(name_category);
	up();
}
function ResetCategory(){
	$(".select_cat").removeClass('active_cat');
	$("#reset_category").css('display', 'none');
	$("#name_category_res").text('');
	document.getElementById("category_id").value = 0;
	sup_type = document.getElementById("sup_type").value;
    if (sup_type != 0) {
    	document.getElementById("page_num").value = 1;
    	AllPosition();
    }
    else{
    	SelectPrice();
    }
}

function SelectList(){
	document.getElementById("view").value = 'list';
	$("#grid").removeClass('active');
	$("#list").addClass('active');
	SelectPrice();
}
function SelectGrid(){
	document.getElementById("view").value = 'grid';
	$("#list").removeClass('active');
	$("#grid").addClass('active');
	SelectPrice();
}
function Allpos(){
	location.reload();
}

function SelectPrice(){
	//Записываем переменные
	document.getElementById("pricefrom").value = document.getElementById("price_from").value;
	document.getElementById("priceto").value = document.getElementById("price_to").value;
	//Вытягиваем все необходимое
	var request = document.getElementById("request").value;
	var page = document.getElementById("page").value;
	var view = document.getElementById("view").value;
	var sup_type = document.getElementById("sup_type").value;
	var region = document.getElementById("region_select").value;
	var city = document.getElementById("city_select").value;
	var price_from = document.getElementById("pricefrom").value;
	var price_to = document.getElementById("priceto").value;
	var category = document.getElementById("category_id").value;
	var name_price = price_from+' - '+price_to+' грн';
	//Определяем общее количество найденых позиций
	
    $.ajax({
        type: "POST",
        data: {
            'request': request,
            'page': page,
            'view' : view,
            'sup_type': sup_type,
            'region': region,
            'city': city,
            'price_from': price_from,
            'price_to': price_to,
            'category': category,
            '<?= Yii::app()->getRequest()->csrfTokenName;?>': '<?= Yii::app()->getRequest()->csrfToken;?>'
        },
        url: '<?= Yii::app()->createUrl('search/search/selectprice');?>',
        success: function (data) {
        	cat = JSON.parse(data);
	       	//$("#all_results_search").css('display', 'block');
			$("#region_selector").html('');
			$("#region_selector").empty().append(cat.region);
			if (cat.city!='') {
				$("#city_selector").removeAttr("disabled");
				$("#city_selector").html('');
				$("#city_selector").empty().append(cat.city);
			}
			$("#prosucts_search").html('');
			$("#prosucts_search").empty().append(cat.pr);
			$("#service_search").html('');
			$("#service_search").empty().append(cat.sr);
        },
		error: function(e){
			console.log(e);
		}
    });
    if (sup_type != 0) {
    	document.getElementById("page_num").value = 1;
    	AllPosition();
    }
    if ((price_from != 0)&(price_to != 0)) {
		$("#reset_price").css('display', 'block');
		$("#name_price_res").text(name_price);
    }
    up();
}

function ResetPrice(){
	$("#reset_price").css('display', 'none');
	$("#reset_price_res").text('');
	document.getElementById("pricefrom").value = 0;
	document.getElementById("price_from").value = 0;
	document.getElementById("priceto").value = 0;
	document.getElementById("price_to").value = 0;
	sup_type = document.getElementById("sup_type").value;
    if (sup_type != 0) {
    	document.getElementById("page_num").value = 1;
    	AllPosition();
    }
    else{
    	SelectPrice();
    }
}

function SelectRegion(e){
	//Записываем переменные
	document.getElementById("region_select").value = e.value;
	document.getElementById("city_select").value = 0;
	//Вытягиваем все необходимое
	var request = document.getElementById("request").value;
	var page = document.getElementById("page").value;
	var view = document.getElementById("view").value;
	var sup_type = document.getElementById("sup_type").value;
	var region = document.getElementById("region_select").value;
	var name_region = $(':selected', e).data('name');
	var city = document.getElementById("city_select").value;
	var price_from = document.getElementById("pricefrom").value;
	var price_to = document.getElementById("priceto").value;
	var category = document.getElementById("category_id").value;

	if (e.value == 0) {
		document.getElementById("city_select").value = 0;
		$("#city_selector").prop('disabled', 'disabled');
		$("#city_selector").html('');
	}
	console.log(request,page, view, sup_type, region, city, price_from, price_to, category);
	if (sup_type == 0) {
	    $.ajax({
	        type: "POST",
	        data: {
	            "request": request,
	            "page": page,
	            "view" : view,
	            "sup_type": sup_type,
	            "region": region,
	            "city": city,
	            "price_from": price_from,
	            "price_to": price_to,
	            "category": category,
	            "<?= Yii::app()->getRequest()->csrfTokenName;?>": "<?= Yii::app()->getRequest()->csrfToken;?>"
	        },
	        url: "<?= Yii::app()->createUrl('search/search/selectprice');?>",
	        success: function (data) {
	        	cat = JSON.parse(data);
		       	//$("#all_results_search").css('display', 'block');
				$("#region_selector").html('');
				$("#region_selector").empty().append(cat.region);
				if (cat.city!='') {
					$("#city_selector").removeAttr("disabled");
					$("#city_selector").html('');
					$("#city_selector").empty().append(cat.city);
				}
				$("#prosucts_search").html('');
				$("#prosucts_search").empty().append(cat.pr);
				$("#service_search").html('');
				$("#service_search").empty().append(cat.sr);
	        },
			error: function(e){
				console.log(e);
			}
	    });
	}

    if (sup_type == 1) {
    	document.getElementById("page_num").value = 1;
    	AllProducts();
    }
    if (sup_type == 2) {
    	document.getElementById("page_num").value = 1;
    	AllService()
    }
	$("#reset_region").css('display', 'block');
	$("#name_region_res").text(name_region);
	$("#reset_city").css('display', 'none');
	$("#reset_city_res").text('');
	document.getElementById("city_select").value = 0;
    up();
}

function SelectCity(e){
	//Записываем переменные
	document.getElementById("city_select").value = e.value;
	//Вытягиваем все необходимое
	var request = document.getElementById("request").value;
	var page = document.getElementById("page").value;
	var view = document.getElementById("view").value;
	var sup_type = document.getElementById("sup_type").value;
	var region = document.getElementById("region_select").value;
	var name_city = $(':selected', e).data('name');
	var city = document.getElementById("city_select").value;
	var price_from = document.getElementById("pricefrom").value;
	var price_to = document.getElementById("priceto").value;
	var category = document.getElementById("category_id").value;
    $.ajax({
        type: "POST",
        data: {
            'request': request,
            'page': page,
            'view' : view,
            'sup_type': sup_type,
            'region': region,
            'city': city,
            'price_from': price_from,
            'price_to': price_to,
            'category':category,
            '<?= Yii::app()->getRequest()->csrfTokenName;?>': '<?= Yii::app()->getRequest()->csrfToken;?>'
        },
        url: '<?= Yii::app()->createUrl('search/search/selectprice');?>',
        success: function (data) {
        	cat = JSON.parse(data);
	       	//$("#all_results_search").css('display', 'block');
			$("#region_selector").html('');
			$("#region_selector").empty().append(cat.region);
			if (cat.city!='') {
				$("#city_selector").removeAttr("disabled");
				$("#city_selector").html('');
				$("#city_selector").empty().append(cat.city);
			}
			$("#prosucts_search").html('');
			$("#prosucts_search").empty().append(cat.pr);
			$("#service_search").html('');
			$("#service_search").empty().append(cat.sr);
        },
		error: function(e, data){
			comsole.log(data);
			console.log(e);
		}
    });
 	$("#reset_city").css('display', 'block');
	$("#name_city_res").text(name_city);
    if (sup_type != 0) {
    	document.getElementById("page_num").value = 1;
    	AllPosition();
    }
    up();
}

function ResetCity(){
	$("#reset_city").css('display', 'none');
	$("#reset_city_res").text('');
	document.getElementById("city_select").value = 0;
	sup_type = document.getElementById("sup_type").value;
    if (sup_type != 0) {
    	document.getElementById("page_num").value = 1;
    	AllPosition();
    }
    else{
    	SelectPrice();
    }
}

function ResetRegion(){
	$("#reset_region").css('display', 'none');
	$("#reset_region_res").text('');
	$("#reset_city").css('display', 'none');
	$("#reset_city_res").text('');
	document.getElementById("region_select").value = 0;
	document.getElementById("city_select").value = 0;
	sup_type = document.getElementById("sup_type").value;
    if (sup_type != 0) {
    	document.getElementById("page_num").value = 1;
    	AllPosition();
    }
    else{
    	SelectPrice();
    }
}

function SelectPage(e){
	up();
	document.getElementById("page_num").value = $(e).data('page');
	//document.getElementById("page_num").value = $(e).data('page');
	AllPosition();
}
function AllProducts(){
	up();
	document.getElementById("sup_type").value = 1;
	document.getElementById("title_search").style.display = 'none';
	document.getElementById("title_search_service").style.display = 'none';
	document.getElementById("title_search_product").style.display = 'block';
	document.getElementById("all_pos").style.display = 'block';
	$(".pull-left").css('display', 'block');
	AllPosition();
}
function AllService(){
	up();
	document.getElementById("sup_type").value = 2;
	document.getElementById("title_search").style.display = 'none';
	document.getElementById("title_search_product").style.display = 'none';
	document.getElementById("title_search_service").style.display = 'block';
	document.getElementById("all_pos").style.display = 'block';
	$(".pull-left").css('display', 'block');
	AllPosition();
}

function AllPosition(){
	$("#prosucts_search").css('display', 'none');
	$("#service_search").css('display', 'none');
	$("#prosucts_search").css('position', 'absolute');
	$("#service_search").css('position', 'absolute');
	$("#all_results_search").css('display', 'block');
	var request = document.getElementById("request").value;
	var page_num = document.getElementById("page_num").value;
	var view = document.getElementById("view").value;
	var sup_type = document.getElementById("sup_type").value;
	var region = document.getElementById("region_select").value;
	var city = document.getElementById("city_select").value;
	var price_from = document.getElementById("pricefrom").value;
	var price_to = document.getElementById("priceto").value;
	var category = document.getElementById("category_id").value;
	console.log(city);
	//Запрос если мы на странице поиска товаров или услуги
    $.ajax({
        type: "POST",
        data: {
            'request': request,
            'view' : view,
            'sup_type': sup_type,
            'page_num': page_num,
            'region': region,
            'city': city,
            'price_from': price_from,
            'price_to': price_to,
            'category': category,
            '<?= Yii::app()->getRequest()->csrfTokenName;?>': '<?= Yii::app()->getRequest()->csrfToken;?>'
        },
        url: '<?= Yii::app()->createUrl('search/search/products');?>',
        success: function (data) {
        	cat = JSON.parse(data);
	       	//$("#all_results_search").css('display', 'block');
			$("#region_selector").html('');
			$("#region_selector").empty().append(cat.region);
	       	$("#all_results_search").css('display', 'block');
			$("#all_results_search").html('');
			$("#all_results_search").empty().append(cat.html);
			$("#category_block").html('');
			$("#category_block").empty().append(cat.category);

				$("#city_selector").removeAttr("disabled");
				$("#city_selector").html('');
				$("#city_selector").empty().append(cat.city);

        },
		error: function(e){
			console.log(e);
		}
    });
}
</script>