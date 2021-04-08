
<footer class="footer">
    <div class="container">
        <div class="row" style="width:100%;">
            <div class="col">
                <ul class="footer__box">
                    <li class="footer__item footer__logo-box">
                        <a href="/" class="logo"><span class="logo-first">ДомСтрой</span><span class="logo-last">Всё для строительства и ремонта</span></a>
                    </li>
                    <li class="footer__item footer__list-box-small">
                        <ul class="footer__nav">
                            <li class="footer__nav__item"><a href="<?=Yii::app()->createUrl('company')?>"><?=Yii::t('footer','Companies')?></a></li>
                            <li class="footer__nav__item"><a href="<?=Yii::app()->createUrl('master')?>"><?=Yii::t('footer','Masters')?></a></li>
                            <li class="footer__nav__item"><a href="<?=Yii::app()->createUrl('blogs/poleznye-sovety')?>"><?=Yii::t('footer','Advices')?></a></li>
                            <li class="footer__nav__item"><a href="<?=Yii::app()->createUrl('contacts')?>"><?=Yii::t('footer','contacts')?></a></li>
                            <li class="footer__nav__item"><a href="/page<?=Yii::app()->createUrl('karta-sayta')?>"><?=Yii::t('footer','Sitemap')?></a></li>
                        </ul>
                    </li>
                    <li class="footer__item footer__list-box">
                        <ul class="footer__nav">
                            <li class="footer__nav__item"><a href="/page<?=Yii::app()->createUrl('what-is-top')?>"><?=Yii::t('footer','Why we')?></a></li>
                            <li class="footer__nav__item"><a href="<?=Yii::app()->createUrl('post/domstroy-rasshiryaet-vozmozhnosti-dlya-razvitiya-biznesa.html')?>"><?=Yii::t('footer','Packages for companies and masters')?></a></li>
                            <li class="footer__nav__item"><a href="/page<?=Yii::app()->createUrl('reklama-na-sayte')?>"><?=Yii::t('footer','Reklama na sayte')?></a></li>
                            <li class="footer__nav__item"><a href="/page<?=Yii::app()->createUrl('polzovatelskie-soglasheniya')?>"><?=Yii::t('footer','User agreements')?></a></li>
                            <li class="footer__nav__item"><a href="/page<?=Yii::app()->createUrl('politika-konfidencialnosti')?>"><?=Yii::t('footer','Politics of personal data')?></a></li>
                        </ul>
                    </li>
                    <li class="footer__item socials">
                        <?php
                            $this->widget('application.modules.homepage.widgets.FooterSocialWidget',
                            ['assets'=>$this->mainAssets, 'uploadsAsset'=>$this->uploadsAsset]);
                        ?>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row" style="color:#fff;width:100%;">
            <a href="https://domstroy.com.ua/" style="color:#fff;font-weight: bold;padding-top:10px;padding-bottom: 10px;">Строительный портал</a> ДомСтрой: все товары и услуги для ремонта и стройки. Каталог проверенных строительных компаний и мастеров. Удобный поиск, цены и рейтинги.
        </div> 
    </div>
</footer>
<?php
        Yii::app()->getClientScript()->registerCssFile($this->mainAssets . '/css/yupe.css');
        Yii::app()->getClientScript()->registerCssFile($this->mainAssets . '/css/owl.carousel.css');
        Yii::app()->getClientScript()->registerCssFile($this->mainAssets . '/css/owl.theme.css');
        Yii::app()->getClientScript()->registerCssFile($this->mainAssets . '/css/owl.transitions.css');
        Yii::app()->getClientScript()->registerCssFile($this->mainAssets . '/css/magnific-popup.css');
        Yii::app()->getClientScript()->registerCssFile($this->mainAssets . '/css/jquery-ui.min.css');
        Yii::app()->getClientScript()->registerCssFile($this->mainAssets . '/css/jquery-ui.theme.min.css');
        Yii::app()->getClientScript()->registerCssFile($this->mainAssets. '/css/main.css');
        Yii::app()->getClientScript()->registerCssFile($this->mainAssets. '/css/materials.css');
        Yii::app()->getClientScript()->registerCssFile($this->mainAssets . '/css/media.css');
        Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/cart.js');
        Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/copyright12.js');
        Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/bootstrap-notify.js');
        Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/jquery.li-translit.js');
        Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/jquery.magnific-popup.js');
        Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/jquery-ui.min.js');
        Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/owl.carousel.js');
        Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/common.js');
	Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/seohide.js');
    ?>
<script>
function EditMetaTag(){
    $("#block_meta_tag").css('display', 'block');
}
function CloseBlockEditMeta(){
    $("#block_meta_tag").css('display', 'none');
}
//Scripts for mobile design
$(document).ready(function(){
    if(+screen.width<=568 && +screen.width>=320){
	if($("#social_widget")){
    		$(".section-color>div.container").append($("#social_widget"));
	}
	if($(".section-transparent>div.container>div.row:nth-child(2)>div.col-md-9.col-sm-7.col-xs-12>div:first-child")){
    		$(".section-transparent>div.container>div.row:nth-child(2)>div.col-md-9.col-sm-7.col-xs-12>div:first-child").append($("#company_view"));
	}
if(document.querySelector(".nav")){
	document.querySelector(".nav").addEventListener("click", function(e){
	if (e.target instanceof HTMLAnchorElement) {
	if(e.target.getAttribute('href')!=="#description"){
    		$("#description").hide();
		$("#mobile_descr").hide();
    		$("#open_block_one").hide();
	}else if(e.target.getAttribute('href')=="#description"){
		//$("#description").show();
    		$("#mobile_descr").show();
    		$("#open_block_one").show();
	}
	}
	});
}

        $("body").append($("div#riainfo_fa593a2073a6dc04e3e6a2c35ffa5994"));
        $("div.add-img img").first().insertBefore($("div#yw3>div.items>div:eq(1)"));
        $("div.add-img img").first().insertBefore($("div#yw3>div.items>div:eq(3)"));

        $("div#add-toggle>div.add-img img").first().insertBefore($("div#yw2>div.row.items>div.materials-box:eq(1)"));
        $("div#add-toggle>div.add-img img").first().insertBefore($("div#yw2>div.row.items>div.materials-box:eq(3)"));
        //Услуги
        $("section.services div.col ul.services__people").first().append($("section.popular div.col ul.popular__banners li:last-child"));
        $("body > div.content.clearfix > section.section-transparent > div > div:nth-child(2) > div.col-lg-3.col-md-3.col-sm-5.col-xs-12").prepend($("body > div.content.clearfix > section.section-transparent > div > div:nth-child(2) > div.col-md-9.col-sm-7.col-xs-12 > div.col-lg-6.col-md-6.col-sm-12.col-xs-12.margin-top-30 > div:nth-child(4)"));
	$("body > div.content.clearfix > section.section-transparent > div > div:nth-child(2) > div.col-lg-3.col-md-3.col-sm-5.col-xs-12").prepend($("body > div.content.clearfix > section.section-transparent > div > div:nth-child(2) > div.col-md-9.col-sm-7.col-xs-12 > div.col-lg-6.col-md-6.col-sm-12.col-xs-12.margin-top-30 > div:nth-child(3)"));
	$("body > div.content.clearfix > section.section-transparent > div > div:nth-child(2) > div.col-lg-3.col-md-3.col-sm-5.col-xs-12").prepend($("body > div.content.clearfix > section.section-transparent > div > div:nth-child(2) > div.col-md-9.col-sm-7.col-xs-12 > div.col-lg-6.col-md-6.col-sm-12.col-xs-12.margin-top-30 > div:nth-child(1)"));
    }
});
  // document.getElementById("item_2").style.display = 'block';
  // document.getElementById("item_3").style.display = 'block';
</script>
<!--<script async src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>-->
<script>
    const googleTranslateConfig = {
    lang: "ru",
};

function TranslateInit() {

    let code = TranslateGetCode();
    // Находим флаг с выбранным языком для перевода и добавляем к нему активный класс
    $('[data-google-lang="' + code + '"]').addClass('language__img_active');

    if (code == googleTranslateConfig.lang) {
        // Если язык по умолчанию, совпадает с языком на который переводим
        // То очищаем куки
        TranslateClearCookie();
    }

    // Инициализируем виджет с языком по умолчанию
    new google.translate.TranslateElement({
        pageLanguage: googleTranslateConfig.lang,
    });

    // Вешаем событие  клик на флаги
    $('[data-google-lang]').click(function () {
        TranslateSetCookie($(this).attr("data-google-lang"))
        // Перезагружаем страницу
        window.location.reload();
    });
}

function TranslateGetCode() {
    // Если куки нет, то передаем дефолтный язык
    let lang = ($.cookie('googtrans') != undefined && $.cookie('googtrans') != "null") ? $.cookie('googtrans') : googleTranslateConfig.lang;
    return lang.substr(-2);
}

function TranslateClearCookie() {
    $.cookie('googtrans', null);
    $.cookie("googtrans", null, {
        domain: "." + document.domain,
    });
}

function TranslateSetCookie(code) {
    // Записываем куки /язык_который_переводим/язык_на_который_переводим
    $.cookie('googtrans', "/auto/" + code);
    $.cookie("googtrans", "/auto/" + code, {
        domain: "." + document.domain,
    });
}
</script>
<!--<script async src="https://translate.google.com/translate_a/element.js?cb=TranslateInit"></script>-->
<script>  
$(function () {  
    $('#viber').on('click', function () {
    ga('send', 'event', 'ViberClick', 'Click');     
        var data = {};
    	data['url'] = window.location.href;
        data[yupeTokenName] = yupeToken;    
            $.ajax({
                url: '<?=Yii::app()->createUrl("/user/statistics/viber")?>',
                type: "POST",
                dataType: "json",
                data: data,
                cache: false,
                xhrFields: {
                    withCredentials: true
                },
                success: function (data) {
                    console.log(data);
                },
                error: function (data) {
                    console.log(data)
                }
            });
    });
    $('button[data-target="#callbackModal"]').on('click', function () {
    // ga('send', 'event', 'ViberClick', 'Click');     
        var data = {};
    	data['url'] = window.location.href;
        data[yupeTokenName] = yupeToken;    
            $.ajax({
                url: '<?=Yii::app()->createUrl("/user/statistics/callback")?>',
                type: "POST",
                dataType: "json",
                data: data,
                cache: false,
                xhrFields: {
                    withCredentials: true
                },
                success: function (data) {
                    console.log(data);
                },
                error: function (data) {
                    console.log(data)
                }
            });
    });
});
</script>
<script>
      //load scripts
	var scr = {"scripts":[       
        {"src" : "https://translate.google.com/translate_a/element.js?cb=TranslateInit", "async" : true},
        {"src" : "https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js", "async" : true}
        ]};!function(t,n,r){"use strict";var c=function(t){if("[object Array]"!==Object.prototype.toString.call(t))return!1;for(var r=0;r<t.length;r++){var c=n.createElement("script"),e=t[r];c.src=e.src,c.async=e.async,n.body.appendChild(c)}return!0};t.addEventListener?t.addEventListener("load",function(){c(r.scripts);},!1):t.attachEvent?t.attachEvent("onload",function(){c(r.scripts)}):t.onload=function(){c(r.scripts)}}(window,document,scr);
</script>
<script>
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({
'event':'ipEvent',
'ipAddress' : '<?=$_SERVER["REMOTE_ADDR"]?>'
});
</script>
<?php
//Yii::app()->getClientScript()->registerScriptFile($this->mainAssets . '/js/seohide.js');
?>