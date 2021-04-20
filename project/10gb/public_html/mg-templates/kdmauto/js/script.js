$(document).ready(function(){
  try {
    let myselected = localStorage.getItem('myselected');
    $( "select[name='prop[157][] '] option" ).each(function(key, value){
      if($(this).val().search(myselected) == -1) {
        $(this).css("display", "none");
      } else {
        $(this).css("display", "block");
      }
    });
  } catch (error) {
    console.log(error);
  }

  try {
    $("div.product-tabs-container div#tab1 table.tbl-v02").eq(2).html('<table class="tbl-v02"><colgroup><col style="width: 140px;"><col style="width: auto;"></colgroup><tbody><tr><th> Аббревиатура </th><td><p class="abbr"><span class="abbr-x"> Замена </span><span class="abbr-e"> Рекомендуется замена </span><span class="abbr-r"> Сколы/царапины </span><span class="abbr-w"> Ремонт </span><span class="abbr-m"> Съёмная деталь </span><span class="abbr-f"> Повреждения кузова </span></p></td></tr><tr style="display: none;"><th> Specials </th><td> Дефект сиденья, дефект материала внутренней части, дефект аварийной подушки, разгрузка двигателя, утечка моторного масла, шарнир двигателя, контрольная лампа двигателя, дефект миссии, дефект PS, дефект центровки, дефект глушителя, нижний шарнир, коррозия нижней части кузова </td></tr></tbody></table>');
  } catch (error) {
    console.log(error);
  }

  try {
    //Добавляем висоту прокрутки в localstorage
    if(window.location.href.search(/\/catalog/g) != -1){
      let pagination = document.getElementsByClassName("mg-pager");
      pagination[0].addEventListener("click", function(e){
        if(e.target.nodeName == "A"){
          pagination[0].scrollIntoView();
        }
      });
      pagination[1].addEventListener("click", function(e){
        if(e.target.nodeName == "A"){
          pagination[0].scrollIntoView();
        }
      });
      window.addEventListener("unload", function() {
        localStorage.scrolltop = window.pageYOffset;
      });
      window.scrollTo( 0, localStorage.scrolltop );
    }
  } catch (error) {
    console.log(error);
  }

  try {
    //Добавляем дату к аукциону
    lottedate = $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(7) > ul > li:nth-child(1) > label").text().match(/\d{2}\/\d{2}\/\d{4}/g);
    sellcardate = $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(7) > ul > li:nth-child(2) > label").text().match(/\d{2}\/\d{2}\/\d{4}/g);
    lotteslice = $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(7) > ul > li:nth-child(1) > label").text().search(/\d{2}\/\d{2}\/\d{4}/g);
    sellcarslice = $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(7) > ul > li:nth-child(2) > label").text().search(/\d{2}\/\d{2}\/\d{4}/g);
    lotteauction = $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(7) > ul > li:nth-child(1) > label").text().slice(0,lotteslice);
    sellcarauction = $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(7) > ul > li:nth-child(2) > label").text().slice(0,sellcarslice);
    lotteauctionspan = $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(7) > ul > li:nth-child(1) > label > span").contents()[0];
    if(lotteauctionspan){
      lotteauctionspan.textContent = lottedate;
    }
    sellcarauctionspan = $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(7) > ul > li:nth-child(2) > label > span").contents()[0];
    if(sellcarauctionspan){
      sellcarauctionspan.textContent = sellcardate;
    }
    lotteauctiontext = $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(7) > ul > li:nth-child(1) > label").contents()[1];
    if(lotteauctiontext){
      lotteauctiontext.textContent = lotteauction;
    }
    sellcarauctiontext = $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(7) > ul > li:nth-child(2) > label").contents()[1];
    if(sellcarauctiontext){
      sellcarauctiontext.textContent = sellcarauction;
    }
  } catch (error) {
    console.log(error);
  }
  
  //Доделуем фильтр чтоб при виборе марки фильтровались модели 
  $( "select[name='prop[156][] ']" ).change(function () {
    localStorage.setItem("myselected", $(this).val());
    myselected = localStorage.getItem('myselected');
    $("select[name='prop[157][] '] option").eq(0).prop('selected', true);
    $( "select[name='prop[157][] '] option" ).each(function(key, value){
      if($(this).val().search(myselected) == -1) {
        $(this).css("display", "none");
      } else {
        $(this).css("display", "block");
      }
    });
  });

  try {
    $("#tab1 table.tbl-v02 tr").eq(7).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(8).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(9).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(10).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(11).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(12).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(13).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(14).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(15).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(16).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(17).css("display", "none");
    $("#tab1 table.tbl-v02 tr").eq(18).css("display", "none");

    $("#tab1 table.tbl-v02").eq(1).find("tr").eq(7).css("display", "none");
    $("#tab1 table.tbl-v02").eq(1).find("tr").eq(8).css("display", "none");
    $("#tab1 table.tbl-v02").eq(1).find("tr").eq(9).css("display", "none");
    $("#tab1 table.tbl-v02").eq(1).find("tr").eq(10).css("display", "none");

    $("#tab1 table.tbl-v02").eq(2).find("tr").eq(1).css("display", "none");
    $("#tab1 > div:nth-child(2) > table > tbody > tr:nth-child(10)").css("display:none;");
    
    if($("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(9) h4").text() == "Оценка автомобиля"){
      $("body > div.wrapper.catalog-page > div:nth-child(2) > div:nth-child(1) > div > div.side-menu > div.filter-block > form > div.mg-filter-body > div > div:nth-child(9)").attr("id","carmark");
    }
  } catch (error) {
    console.log(error);
  }

  $("table").wrap("<div class='table-wrapper'/>");

  var reg_phone = /0\d{9}/g;
  var reg_phone2 = /0{1}1{9}|0{1}2{9}|0{1}3{9}|0{1}4{9}|0{1}5{9}|0{1}6{9}|0{1}7{9}|0{1}8{9}|0{1}9{9}|0{9}/g;
  $("input[name='phone']").eq(1).focusout(function(){
    var val_phone = $("input[name='phone']").eq(1).val();
    val_phone = val_phone.split(' ').join('').split('-').join('');
    val_phone = val_phone.replace(/\(/g, "").replace(/\)/g, "").replace(/^\+?3?8/g, "");
    if(!val_phone.match(reg_phone) || val_phone.match(reg_phone2) || val_phone == '' || val_phone.length != 10){
      alert("Введите правильный мобильный номер телефона");
      $("input[name='send']").attr("disabled", true);
      $("input[name='send']").css("background-color","#cccccc");
    }else{
      $("input[name='send']").removeAttr("disabled");
      $("input[name='send']").css("background-color","#d50000");
    }
  });
  //back ring
  $("input[name='phone']").eq(0).focusout(function(){
    var val_phone = $("input[name='phone']").eq(0).val();
    val_phone = val_phone.split(' ').join('').split('-').join('');
    val_phone = val_phone.replace(/\(/g, "").replace(/\)/g, "").replace(/^\+?3?8/, "");
    if(!val_phone.match(reg_phone) || val_phone.match(reg_phone2) || val_phone == '' || val_phone.length != 10){
      alert("Введите правильный мобильный номер телефона");
      $("div.content-modal-back-ring a.send-ring-button").css("background","linear-gradient(to bottom,  #f92517 0%,#ae0101 100%)");
      $("div.content-modal-back-ring a.send-ring-button").off('click');
      $("div.content-modal-back-ring a.send-ring-button").css("color","#ffffff");
    } else {
      $("div.content-modal-back-ring a.send-ring-button").css("color","#3F454B");
      $("div.content-modal-back-ring a.send-ring-button").css("background","linear-gradient(to bottom,  #feffff 0%,#f2efed 100%)");
      $("div.content-modal-back-ring a.send-ring-button").click(function() {   
        var name = $(this).parents('.content-modal-back-ring').find('input[name=name]');
        var comment = $(this).parents('.content-modal-back-ring').find('textarea[name=comment]');
        var phone = $(this).parents('.content-modal-back-ring').find('input[name=phone]');
        var city_id = $(this).parents('.content-modal-back-ring').find('input[name=city_id]');
        var mission = $(this).parents('.content-modal-back-ring').find('select[name=mission]');
        var from = $(this).parents('.content-modal-back-ring').find('select[name=from]');
        var to = $(this).parents('.content-modal-back-ring').find('select[name=to]');
        var date_callback = $(this).parents('.content-modal-back-ring').find('input[name=date_callback]');
        var captcha = $(this).parents('.content-modal-back-ring').find('input[name=capcha]');
        var time_callback = 'с ' + from.val() + ' до ' + to.val();
        if (from.parents('li').css('display') == 'none') {
          time_callback = '';
        }

        if (phone.val() == "") {
          $('.wrapper-modal-back-ring .error').remove();
          $('.title-modal-back-ring').after('<div class="error">Необходимо заполнить поля формы</div>')
          return false;
        }

        $('.send-ring-button').hide();
        $('.send-ring-button').before("<span class='loading-send-ring'>Подождите, идет отправка заявки...</span>");

        $.ajax({
          type: "POST",
          url: mgBaseDir + "/ajaxrequest",
          dataType: 'json',
          data: {
            mguniqueurl: "action/sendOrderRing", // действия для выполнения на сервере
            pluginHandler: 'back-ring',
            name: name.val(),
            comment: comment.val(),
            phone: phone.val(),
            city_id: city_id.val(),
            mission: mission.val(),
            date_callback: date_callback.val(),
            time_callback: time_callback,
            invisible: 1,
            status_id: 1,
            pub: 1,
            capcha: captcha.val(),        
          },
          success: function(response) {
            if (response.status != 'error') {
              $('.content-modal-back-ring').text('Ваша заявка №' + response.data.row.id + ' принята. Наши менеджеры свяжутся с вами!');        
              $('.send-ring-button').show();
              $('.loading-send-ring').remove();
              closeModal($('.wrapper-modal-back-ring'));
            } else {
              $('.wrapper-modal-back-ring .error').remove();
              $('.title-modal-back-ring').after(response.data.msg);
              $('.send-ring-button').show();
              $('.loading-send-ring').remove();
            }
          }
        });//END AJAX
    });//END CLICK
    }
  });//END back ring

  //FOOTER FORM
  $("input[name='phone2']").focusout(function(){
    var val_phone =  $("input[name='phone2']").val();
    val_phone = val_phone.split(' ').join('').split('-').join('');
    val_phone = val_phone.replace(/\(/g, "").replace(/\)/g, "").replace(/^\+?3?8/g, "");
    if(!val_phone.match(reg_phone) || val_phone.match(reg_phone2) || val_phone == '' || val_phone.length != 10){
      alert("Введите правильный мобильный номер телефона");
      $("input[name='send2']").attr("disabled", true);
      $("input[name='send2']").css("background-color","#cccccc");
    }else{
      $("input[name='send2']").removeAttr("disabled");
      $("input[name='send2']").css("background-color","#2196F3");
    }
  });//END FOOTER FORM

  $(".products-wrapper .product-wrapper").each(function(){
      var variants = $(this).find(".block-variants");
      if(variants.length){
        $(this).find(".product-image").prepend("<span class='variants-text'><i class='fa fa-bookmark-o'></i> Есть варианты</span>");
        $('.variants-text').show();
      }
  });

    $(".enter-on .open-link").on("click", function(e){
        e.preventDefault();
        $(this).parent().toggleClass("open");
    });

    $('html').click(function( event ){

        var target = $( event.target ).parents(".enter-on");

        if( !target.length ){
            $(".enter-on").removeClass("open");
        }
    });

  $(".zoom").on("click", function(){
    $(this).prev().trigger("click");
  });

  function productTabs(){
    var tabContainers = $('.product-tabs-container > div');
    tabContainers.hide().filter(':first').show();

    $('.product-tabs li a').click(function(){
      tabContainers.hide();
      tabContainers.filter(this.hash).fadeIn("fast");
      $('.product-tabs li').removeClass('active');
      $(this).parent().addClass('active');
      return false;
    }).filter(':first').click();
  }

  productTabs();

  function rememberView(){
    var className = localStorage["class"];
    //localStorage.clear();

    if(className === undefined){
      $(".btn-group .view-btn:first-child").addClass("active");
      localStorage.setItem('class', 'grid');
    } else{
      $('.btn-group .view-btn[data-type="' + className + '"]').addClass("active");
      $('.products-wrapper.catalog').addClass(className);
    }

    $(".btn-group .view-btn").on("click", function(e){
      e.preventDefault();
      var currentView = $(this).data('type');
      var product = $('.products-wrapper.catalog');
      product.removeClass("list grid");
      product.addClass(currentView);
      $('.btn-group .view-btn').removeClass("active");
      $(this).addClass("active");
      localStorage.setItem('class', $(this).data('type'));
      return false;
    });
  }

  rememberView();

  $(".show-hide-filters").on("click", function(){
    $(this).parent(".filter-block").toggleClass("show");
  });

  $(".close-icon").on("click", function(){
    $("body").removeClass("locked");
    $(this).parents(".menu-block").removeClass("open");
  });

  $(".mobile-toggle").on("click", function(){
    $("body").toggleClass("locked");
    $(this).parent(".menu-block").toggleClass("open");
  });

  $("body").on("click", ".menu-block.open .toggle", function(){
    $(this).parents("li").toggleClass("open");
  });

  var owl = $(".m-p-products-slider-start");

  owl.owlCarousel({
    items: 3, //10 items above 1000px browser width
    itemsDesktop: [1100, 3], //5 items between 1000px and 901px
    itemsDesktopSmall: [900, 2], // betweem 900px and 601px
    itemsTablet: [600, 2], //2 items between 600 and 0
    itemsMobile: [400, 1], // itemsMobile disabled - inherit from itemsTablet option
    pagination: false,
    navigation: true
  });

  var mobileMenuParent = $(".mg-menu > li").has("ul");
  mobileMenuParent.append('<span class="toggle"></span>');
  mobileMenuParent.addClass("has-menu");
  var horizontalMenuParent = $(".mg-main-menu > li").has("ul");
  horizontalMenuParent.append('<span class="toggle"></span>');

  var slider_width = $('.menu-block').width() + 2;
  var deviceWidth = $(window).width();

  /*Mobile menu*/
  $(".top-menu-list li .slider_btn").on("click", function(){
    $(this).parent("li").toggleClass("open");
  });

  $(".menu-toggle").on("click", function(){
    $(this).parent(".top-bar").toggleClass("open");
  });

  $(".mg-main-menu-toggle").on("click", function(){
    $(this).parent(".mg-main-menu-holder").toggleClass("open");
  });

  $(".mg-main-menu .toggle").on("click", function(){
    $(this).parent("li").toggleClass("open");
  });

  /*Fix mobile top menu position if login admin*/
  if($("body").hasClass("admin-on-site")){
    $("body").find(".mobile-top-panel").addClass("position-fix");
  }
});