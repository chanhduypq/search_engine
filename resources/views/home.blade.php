@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.0/css/bootstrap-slider.min.css">
    <div class="loading" id="loading" style="display: none;">Loading&#8230;</div>
    
    <form id="frm" style="display: none;">

        <!--<h2>Custom Search</h2>-->
        <div id="custom-search-input" style="margin-top: 50px;">
            <div class="input-group col-md-4">
                <input placeholder="Please enter keyword" type="text" class="search-query form-control" id="keywork" name="keywork" value="<?php echo $keywork;?>"/>
                <li class="input-group-btn">
                    <button class="btn btn-danger" type="submit">
                        <li class="glyphicon glyphicon-search"></li>
                    </button>
                </li>
            </div>
        </div>
        <div class="fillter-products">
          <div class="row">
            
            <div class="col-md-5">
              <div class="input-group categories-fillter">
                <strong>Filter by marketplaces: </strong>
                <select class="form-control categories categories-multiple-allproducts" style="visibility: hidden;"  name="categories[]" multiple="multiple">
                  <option value="qoo10">qoo10</option>
                  <option value="shopee">Shoppe</option>
                  <option value="lazada">Lazada</option>
                  <option value="carousell">carousell</option>
                  <option value="ezbuy">ezbuy</option>
                </select>
              </div>
            </div>

            <div class="col-md-5">
              <div class="price-fillter pull-right">
                <strong>Filter by price:</strong> <strong>$ {{ $min_price }}</strong> <input id="ex2" type="text" style="visibility: hidden;" class="span2" value="" data-slider-min="{{ $min_price }}" data-slider-max="{{ $max_price }}" data-slider-step="5" data-slider-value="[{{ $min_price }},{{ $max_price }}]"/> <strong>$ {{ $max_price }}</strong>
              </div>
            </div>
			
			<div class="col-md-2">
                <div class="pull-right">
                    <div class="input-group sort-by">
                        <select id="sort" class="form-control">
                            <option value="default">Sort by: (default)</option>
                            <option value="price_asc">Price (Low to hight)</option>
                            <option value="price_desc">Price (Hight to low)</option>
                            <option value="name_asc">Name (A to Z)</option>
                            <option value="name_desc">Name (Z to A)</option>
<!--                            <option value="site_asc">Marketplace (A to Z)</option>
                            <option value="site_desc">Marketplace (Z to A)</option>-->
                        </select>
                    </div>

                </div>
            </div>

          </div>
        </div>
        <div id="result" class="list-group row" style="display: <?php if (isset($result) && count($result) > 0) echo 'block'; else echo 'none'; ?>;">
            @include('result')
        </div>
    </form>

    <div id="dialog-form" title="Please enter the password." style="margin: 0 auto;text-align: center;">
        <input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all">
    </div>

  <script type="text/javascript">

        function authentication(){
             if($("#password").val()=='galvindavid'){
                 $.cookie('user_password','galvindavid');
                 dialog.dialog( "close" );
                 $("#frm").show();
             }
        }
        
      jQuery(function ($){
          
          if ($.cookie('user_password')!="galvindavid"){
                dialog = $( "#dialog-form" ).dialog({
                  autoOpen: true,
                  height: 200,
                  width: 350,
                  modal: true,
                  buttons: {
                    "Ok.": authentication
                  },
                  close: function() {
                  }
                });
                $(".ui-button.ui-corner-all.ui-widget.ui-button-icon-only.ui-dialog-titlebar-close").hide();
            }
            else{
                $("#frm").show();
            }

            $("#sort").val('default');
            $( '.categories-multiple-allproducts option' ).each(function() {
               var count_cat = $('.product-item[data-site='+$(this).val()+']').length;
               if(count_cat<=1){
                    $(this).html($(this).val()+' ('+count_cat+' product)');
               }
               else{
                    $(this).html($(this).val()+' ('+count_cat+' products)');
               }
            });


            //filter by slider
            var slider = new Slider('#ex2', {});
            $('#ex2').change(function(){

                var min_price = $('.min-slider-handle').attr('aria-valuenow');
                var max_price = $('.max-slider-handle').attr('aria-valuenow');

                $( '.product-item .item-price' ).each(function() {
                    var price = $(this).text().replace('$','').trim();
                    var product = $(this).closest('.product-item')
                    price = parseFloat(price);

                    if ( price>min_price && (price<max_price || price >= 1000 )  ){
                       
                        product.not('.select-category-hide').show();
                        product.removeClass('slide-price-hide');
                    }
                    else{
                        product.addClass('slide-price-hide');
                        product.hide();
                    }
                });

            });

            $('.categories-multiple-allproducts').multiselect(
                {
                    nonSelectedText: '- All Marketplace -',
                    buttonWidth: '200px',
                    onChange: function(option, checked) {

                        //filterByCategories
                        var current_option = $('.product-item[data-site='+option.val()+']');
                          if($('.categories-multiple-allproducts :selected').length==1 && checked==true){
                            $('.product-item').not(current_option).hide();
                            $('.product-item').not(current_option).addClass('select-category-hide');
                          }
                          else if($('.categories-multiple-allproducts :selected').length==0){
                            $('.product-item').not('.slide-price-hide').show();
                            $('.product-item').not('.slide-price-hide').removeClass('select-category-hide');
                          }
                          else{
                              if(checked==true){
                                current_option.removeClass('select-category-hide');
                                current_option.not('.slide-price-hide').show();
                              }
                              else{
                                current_option.addClass('select-category-hide');
                                current_option.hide();
                              }
                          }
                    }
                }
            );

          $("#frm").submit(function (e){
              e.preventDefault();
              if($.trim($("#keywork").val())==''){
                  alert("Please enter keyword.");
                  return;
              }
              $('#loading').show();
              $("#result").html('').hide();
              $.ajax({
                 url: "{{ route('search') }}"+"?keywork="+$.trim($("#keywork").val()),
                 type: 'GET',
                 success: function (data, textStatus, jqXHR) {
                    $('#loading').hide();
                    $("#result").show().html(data);  
                    
                    $("#sort").val('default');
                    
                    $(".input-group.categories-fillter").find('select').eq(0).remove();
                    $(".input-group.categories-fillter").find('div').eq(0).remove();
                    $(".input-group.categories-fillter").append('<select class="form-control categories categories-multiple-allproducts" style="visibility: hidden;"  name="categories[]" multiple="multiple">'+
                  '<option value="qoo10">qoo10</option>'+
                  '<option value="shopee">Shoppe</option>'+
                  '<option value="lazada">Lazada</option>'+
                  '<option value="carousell">carousell</option>'+
                  '<option value="ezbuy">ezbuy</option>'+
                '</select>');
                    $( '.categories-multiple-allproducts option' ).each(function() {
                           var count_cat = $('.product-item[data-site='+$(this).val()+']').length;
                           if(count_cat<=1){
                                $(this).html($(this).val()+' ('+count_cat+' product)');
                           }
                           else{
                                $(this).html($(this).val()+' ('+count_cat+' products)');
                           }
                        });

                    $('.categories-multiple-allproducts').multiselect(
                        {
                            nonSelectedText: '- All Marketplace -',
                            buttonWidth: '200px',
                            onChange: function(option, checked) {

                                //filterByCategories
                                var current_option = $('.product-item[data-site='+option.val()+']');
                                  if($('.categories-multiple-allproducts :selected').length==1 && checked==true){
                                    $('.product-item').not(current_option).hide();
                                    $('.product-item').not(current_option).addClass('select-category-hide');
                                  }
                                  else if($('.categories-multiple-allproducts :selected').length==0){
                                    $('.product-item').not('.slide-price-hide').show();
                                    $('.product-item').not('.slide-price-hide').removeClass('select-category-hide');
                                  }
                                  else{
                                      if(checked==true){
                                        current_option.removeClass('select-category-hide');
                                        current_option.not('.slide-price-hide').show();
                                      }
                                      else{
                                        current_option.addClass('select-category-hide');
                                        current_option.hide();
                                      }
                                  }
                            }
                        }
                    );
                    
                    min_price=$("#min_price").val();
                    max_price=$("#max_price").val();
                    $("#ex2").attr('data-slider-min',min_price).attr('data-slider-max',max_price).attr('value',min_price+","+max_price).attr('data-value',min_price+","+max_price).attr('data-slider-value',"["+min_price+","+max_price+"]");
                    $('.min-slider-handle').attr('aria-valuenow',min_price);
                    $('.max-slider-handle').attr('aria-valuenow',max_price);//.css('left','100%');
                    
                    $(".price-fillter.pull-right").find('strong').eq(1).html("$ "+min_price);
                    $(".price-fillter.pull-right").find('strong').eq(2).html("$ "+max_price);
                 }
              });
          });

          $("body").delegate(".lazada-store", "click", function(){
              var loadding = $(this).closest('.store').find('.lds-ring');
              loadding.show();
              var product_url=$(this).attr('data-url');
              $.ajax({
                 url: product_url,
                 type: 'GET',
                 success: function (data, textStatus, jqXHR) {
                    loadding.hide();
                    nodes=$.parseHTML(data);
                    url=$(nodes).find('div.seller-name__detail').eq(0).find('a').eq(0).attr('href');
                    if(url != undefined && url != 'undefined' && url !=''){
                        window.open('https:'+url,'_blank'); 
                    }
                    else{
                        window.open('https://lazada.sg','_blank'); 
                    }
                       
                 }
              });
          });
          

          $("body").delegate(".ezbuy-store", "click", function(){
              var loadding = $(this).closest('.store').find('.lds-ring');
              loadding.show();
              var product_url=$(this).attr('data-url');
              $.ajax({
                 url: "{{ route('getstoreurl') }}",
                 type: 'post',
                 data: {'url': product_url},
                 success: function (data, textStatus, jqXHR) {
                    loadding.hide();
                    if(textStatus=='success' && data!=''){
                       window.open(data,'_blank'); 
                    }
                 }
              });
          });
		  
          $("#sort").change(function (){
              if($("#result").html()==''||$.trim($("#keywork").val())==''){
                  return;
              }
              $('#loading').show();
              if($(this).val()=='default'){
                  name='default';
                  order='asc';
              }
              else if($(this).val()=='price_asc'){
                  name='price';
                  order='asc';
              }
              else if($(this).val()=='price_desc'){
                  name='price';
                  order='desc';
              }
              else if($(this).val()=='name_asc'){
                  name='product_name';
                  order='asc';
              }
              else if($(this).val()=='name_desc'){
                  name='product_name';
                  order='desc';
              }
              else if($(this).val()=='site_asc'){
                  name='site';
                  order='asc';
              }
              else if($(this).val()=='site_desc'){
                  name='site';
                  order='desc';
              }
              $.ajax({
                 url: "/sort/"+encodeURIComponent($.trim($("#keywork").val()))+"/"+name+"/"+order,                  
                 type: 'GET',
                 success: function (data, textStatus, jqXHR) {
                    $('#loading').hide();
                    $("#result").show().html(data);   
                    
                    
                    
                    title=$('.multiselect.dropdown-toggle.btn.btn-default').attr('title');
                    if(title!='- All Marketplace -'){
                        $('.product-item').addClass('select-category-hide').hide()
                        temp=title.split(',');
                        for(i=0;i<temp.length;i++){
                            site=temp[i].split('(');
                            site=$.trim(site[0]);
                            current_option = $('.product-item[data-site='+site+']');
                            current_option.removeClass('select-category-hide');
                            current_option.not('.slide-price-hide').show();
                        }
                    }
                    
                    var min_price = $('.min-slider-handle').attr('aria-valuenow');
                    var max_price = $('.max-slider-handle').attr('aria-valuenow');

                    $( '.product-item .item-price' ).each(function() {
                        var price = $(this).text().replace('$','').trim();
                        var product = $(this).closest('.product-item')
                        price = parseFloat(price);

                        if ( price>=min_price && (price<max_price || price >= 1000 )  ){

                            product.not('.select-category-hide').show();
                            product.removeClass('slide-price-hide');
                        }
                        else{
                            product.addClass('slide-price-hide');
                            product.hide();
                        }
                    });

                 }
              });
          });
      });
  </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.0/bootstrap-slider.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>

@stop