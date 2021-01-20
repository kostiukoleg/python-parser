<div id="block41">
	<div class="car-bg">
			<?php 
			    $sql = "SELECT * FROM `".PREFIX."page` WHERE title='variants'";
			    $res = DB::query($sql);
			    while ($row = DB::fetchRow($res)) { ?>
			<div class="tittle-blockar"><?php echo html_entity_decode($row[6]); ?></div>
			<?php }
			?> 
<?php if (!empty($data['newProducts'])): ?>

    <div class="m-p-products latest">
        <div class="title"><a href="<?php echo SITE; ?>/group?type=latest">Новинки</a></div>
        <div class="m-p-products-slider">
            <div class="<?php echo count($data['newProducts']) > 3 ? "m-p-products-slider-start" : "" ?>">
                <?php foreach ($data['newProducts'] as $item): ?>
                    <div itemscope itemtype="https://schema.org/Product" class="product-wrapper">
                        <div class="product-stickers">
                            <?php
                            //echo $item['recommend'] ? '<span class="sticker-recommend">Хит!</span>' : '';
                            //echo $item['new'] ? '<span class="sticker-new">Новинка</span>' : '';
                            ?>
                        </div>
                        <div class="product-image">
                            <?php if (!empty($item['variant_exist'])): ?>
                                <span class='variants-text'><i class='fa fa-bookmark-o'></i> Есть варианты</span>
                            <?php endif; ?>
                            <a href="<?php echo $item["link"] ?>">
                                <?php echo mgImageProduct($item); ?>
                            </a>
                        </div>
			<?php if (!class_exists('MyDesiresPlugin')): ?>[addtowishlist product=<?php echo $item['id']; ?>]<?php endif; ?>
                        <?php if (!class_exists('Rating')): ?>
                            <div class="mg-rating">
                                [rating id = "<?php echo $item['id'] ?>"]
                            </div>
                        <?php endif; ?>
                        <!--<div class="product-code">Артикул: <?php echo $item["code"] ?></div>-->
                        <div class="product-name">
                            <a itemprop="url" href="<?php echo $item["link"] ?>"><h2 itemprop="name"><?php echo $item["title"] ?></h2></a>
                        </div>
			<?php if(!class_exists('QuickView')): ?>
            			[quick-view id="<?php echo $item['id']?>"]
        	<?php endif; ?>
			<div class="product-description"></div>
			<div class="product-footer">
                        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="product-price">
                            <span itemprop="price" class="product-default-price"><?php echo priceFormat($item["price"]) ?> </span><span itemprop="priceCurrency" class="currency" content='UAH'><?php echo $data['currency']; ?></span>
                        </div>
 			<div class="product-buttons">
                                <!--Кнопка, кототорая меняет свое значение с "В корзину" на "Подробнее"-->
                                <?php echo $item[$data['actionButton']] ?>
                                <?php 
                                //echo $item['actionCompare'] 
                                ?>
				[buy-click id="<?php echo $item['id']?>" count="<?php echo $item['count']?>" variant="<?php echo $item['variants']?>"]
                        </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<?php endif; ?>

<?php if (!empty($data['recommendProducts'])): ?>
    <div class="m-p-products recommend">
        <div class="title"><a href="<?php echo SITE; ?>/group?type=recommend">Хит продаж</a></div>
        <div class="m-p-products-slider">
            <div class="<?php echo count($data['recommendProducts']) > 3 ? "m-p-products-slider-start" : "" ?>">
                <?php foreach ($data['recommendProducts'] as $item): ?>
                    <div itemscope itemtype="https://schema.org/Product" class="product-wrapper">
                        <!--<div class="product-stickers">
                            <?php
                            echo $item['recommend'] ? '<span class="sticker-recommend">Хит!</span>' : '';
                            echo $item['new'] ? '<span class="sticker-new">Новинка</span>' : '';
                            ?>
                        </div>-->
                        <div class="product-image">
                            <?php if (!empty($item['variant_exist'])): ?>
                                <span class='variants-text'><i class='fa fa-bookmark-o'></i> Есть варианты</span>
                            <?php endif; ?>
                            <a href="<?php echo $item["link"] ?>">
                                <?php echo mgImageProduct($item); ?>
                            </a>
                        </div>
			<?php if (!class_exists('MyDesiresPlugin')): ?>[addtowishlist product=<?php echo $item['id']; ?>]<?php endif; ?>
                        <?php if (!class_exists('Rating')): ?>
                            <div class="mg-rating">
                                [rating id = "<?php echo $item['id'] ?>"]
                            </div>
                        <?php endif; ?>
                        <!--<div class="product-code">Артикул: <?php echo $item["code"] ?></div>-->
                        <div class="product-name">
                            <a itemprop="url" href="<?php echo $item["link"] ?>"><h2 itemprop="name"><?php echo $item["title"] ?></h2></a>
                        </div>
			<?php if(!class_exists('QuickView')): ?>
            		[quick-view id="<?php echo $item['id']?>"]
        		<?php endif; ?>
			<div class="product-description"></div>
			<div class="product-footer">
                        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="product-price">
                            <span itemprop="price" class="product-default-price"><?php echo priceFormat($item["price"]) ?> </span><span itemprop="priceCurrency" class="currency" content='UAH'><?php echo $data['currency']; ?></span>

                            
                        </div>
			<div class="product-buttons">
                                <!--Кнопка, кототорая меняет свое значение с "В корзину" на "Подробнее"-->
                                <?php echo $item[$data['actionButton']] ?>
                                <?php //echo $item['actionCompare'] ?>
				[buy-click id="<?php echo $item['id']?>" count="<?php echo $item['count']?>" variant="<?php echo $item['variants']?>"]
                        </div>
			</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<?php endif; ?>

<?php if (!empty($data['saleProducts'])): ?>
    <div class="m-p-products sale">
        <div class="title"><a href="<?php echo SITE; ?>/group?type=sale">Распродажа</a></div>
        <div class="m-p-products-slider">
            <div class="<?php echo count($data['saleProducts']) > 3 ? "m-p-products-slider-start" : "" ?>">
                <?php foreach ($data['saleProducts'] as $item): ?>
                    <div itemscope itemtype="https://schema.org/Product" class="product-wrapper">
                        <!--<div class="product-stickers">
                            <?php
                            echo $item['recommend'] ? '<span class="sticker-recommend">Хит!</span>' : '';
                            echo $item['new'] ? '<span class="sticker-new">Новинка</span>' : '';
                            ?>
                        </div>-->
                        <div class="product-image">
                            <?php if (!empty($item['variant_exist'])): ?>
                                <span class='variants-text'><i class='icon-bookmark'></i> Есть варианты</span>
                            <?php endif; ?>
                            <a href="<?php echo $item["link"] ?>">
                                <?php echo mgImageProduct($item); ?>
                            </a>
                        </div>
			<?php if (!class_exists('MyDesiresPlugin')): ?>[addtowishlist product=<?php echo $item['id']; ?>]<?php endif; ?>
                        <?php if (!class_exists('Rating')): ?>
                            <div class="mg-rating">
                                [rating id = "<?php echo $item['id'] ?>"]
                            </div>
                        <?php endif; ?>
                        <!--<div class="product-code">Артикул: <?php echo $item["code"] ?></div>-->
                        <div class="product-name">
                            <a itemprop="url" href="<?php echo $item["link"] ?>"><h2 itemprop="name"><?php echo $item["title"] ?></h2></a>
                        </div>
			<?php if(!class_exists('QuickView')): ?>
            		[quick-view id="<?php echo $item['id']?>"]
        		<?php endif; ?>
			<div class="product-description"></div>
			<div class="product-footer">
                        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="product-price">
				<span class="product-old-price"><?php echo $item["old_price"] ?> <?php echo $data['currency']; ?></span>
                            <span  itemprop="price" class="product-default-price">
                                <?php echo priceFormat($item["price"]) ?> </span><span class="currency" itemprop="priceCurrency" content='UAH'><?php echo $data['currency']; ?>
                            </span>

                        </div>
			<div class="product-buttons">
                                <!--Кнопка, кототорая меняет свое значение с "В корзину" на "Подробнее"-->
                                <?php echo $item[$data['actionButton']] ?>
                                <?php //echo $item['actionCompare'] ?>	
				[buy-click id="<?php echo $item['id']?>" count="<?php echo $item['count']?>" variant="<?php echo $item['variants']?>"]
                        </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<?php endif; ?>
	</div>
</div>