<?php if (isset($result) && count($result) > 0): ?>
    <?php foreach ($result as $item): ?>
    <!-- item -->
        <div class="item  col-xs-3 col-lg-3 product-item" data-site="{{ $item['site'] }}">
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
                            <?php if( $item['sale_price']!='' && $item['sale_price']!='$0'): ?>
                                <span class="sale-price item-price">{{ $item['sale_price'] }}</span>
                                <span class="origin-price">{{ $item['price'] }}</span>
                            <?php else: ?>
                                <span class="current-price item-price">{{ $item['price'] }}</span>
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
else: 
    echo 'no result'; 
endif;
?>
    <input type="hidden" id="min_price" value="{{ $min_price }}"/>
    <input type="hidden" id="max_price" value="{{ $max_price }}"/>

