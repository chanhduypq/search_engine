@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.0/css/bootstrap-slider.min.css">
    <div class="loading" id="loading" style="display: none;">Loading&#8230;</div>
    
    <form id="frm">

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
            
            <div class="col-md-6">
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

            <div class="col-md-6">
              <div class="price-fillter pull-right">
                <strong>Filter by price:</strong> <strong>$ 0</strong> <input id="ex2" type="text" style="visibility: hidden;" class="span2" value="" data-slider-min="0" data-slider-max="1000" data-slider-step="5" data-slider-value="[0,1000]"/> <strong>$ 1000+</strong>
              </div>
            </div>

          </div>
        </div>
        <div id="result" class="list-group row" style="display: <?php if (isset($result) && count($result) > 0) echo 'block'; else echo 'none'; ?>;">
            @include('result')
        </div>
    </form>


  <script type="text/javascript">


      jQuery(function ($){

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
                  alert("Please enter keywork.");
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
      });
  </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.0/bootstrap-slider.min.js"></script>

<script type="text/javascript">


    
   
</script>
@stop