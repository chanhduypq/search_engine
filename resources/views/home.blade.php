@extends('layouts.app')

@section('content')
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
        <div id="result" class="list-group row" style="display: <?php if (isset($result) && count($result) > 0) echo 'block'; else echo 'none'; ?>;">
            <!--<h1>Result</h1>-->
            <?php if (isset($result) && count($result) > 0): ?>
                <?php foreach ($result as $item): ?>
                <!-- item -->
                    <div class="item  col-xs-3 col-lg-3">
                        <div class="thumbnail">
                            <div class="main-image">
                                <a href="{{ $item['product_url'] }}" target="_blanks"><img class="group list-group-image" src="{{ str_replace('g_100-w-st_', 'g_200-w-st_', $item['image']) }}" alt="" /></a>
                            </div>
                            <div class="caption">
                                <h4 class="group inner list-group-item-heading"><a href="#" target="_blanks">{{ $item['site'] }}</a></h4>
                                 <p class="group inner list-group-item-text">
                                    <a class="title" title="{{ $item['product_name'] }}" href="{{ $item['product_url'] }}" target="_blank">{{ $item['product_name'] }}</a></p>
                                <div class="row price">
                                    <div class="col-xs-12">
                                        <?php if( $item['sale_price']!=''): ?>
                                            <span class="sale-price">{{ $item['sale_price'] }}</span>
                                            <span class="origin-price">{{ $item['price'] }}</span>
                                        <?php else: ?>
                                            <span class="current-price">{{ $item['price'] }}</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row store">
                                    <div class="col-xs-12">
                                        <?php if(strpos($item['product_url'], 'lazada.sg')!==false): ?>
                                             <span class="pull-right"><a href="javascript:void(0);" class="lazada-store" data-url="{{ $item['product_url'] }}" target="_blank">{{ $item['store_name'] }}</a></span>
                                        <?php elseif(strpos($item['product_url'], 'ezbuy.sg')!==false): ?>
                                            <span class="pull-right"><a href="javascript:void(0);" class="ezbuy-store" data-url="{{ $item['product_url'] }}" target="_blank">{{ $item['store_name'] }}</a></span>
                                        <?php else: ?>
                                            <span class="pull-right"><a href="{{ $item['store_url'] }}" target="_blank">{{ $item['store_name'] }}</a></span>
                                        <?php endif; ?>
                                        <div class="lds-ring" style="display: none;"><div></div><div></div><div></div><div></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- end item -->
                <?php  endforeach;  ?>
            <?php 
            endif;
            ?>


        </div>
    </form>


  <script type="text/javascript">
      jQuery(function ($){
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
@stop