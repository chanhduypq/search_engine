<?php if (isset($result) && count($result) > 0) { ?>
    <?php foreach ($result as $item) { ?>
        <div style="color:#555;" class="list-group-item">
            <div class="photo">
                <img src="{{ $item['image'] }}" style="width: 100px;"/>
            </div>
            <div class="info">   
                <!--<h3 style="text-align: center;width: 100%;">{{ $item['site'] }}</h3>-->
                <div class="name">
                    <a target="_blank" href="{{ $item['product_url'] }}">{{ $item['product_name'] }}</a>
                </div>
                <div class="price">{{ $item['sale_price'] }}</div>
                <div class="price">{{ $item['price'] }}</div>
                <div class="store_name<?php if(strpos($item['product_url'], 'lazada.sg')!==FALSE) echo ' lazada';?>">
                    <?php 
                    if(strpos($item['product_url'], 'lazada.sg') === FALSE && strpos($item['product_url'], 'ezbuy.sg') === FALSE) { ?>
                        <a target="_blank" href="{{ $item['store_url'] }}">{{ $item['store_name'] }}</a>
                    <?php 
                    }
                    else{?>
                        <a href="javascript:void(0);">{{ $item['store_name'] }}</a>
                    <?php 
                    }
                    ?>
                </div>
            </div>
            <div style="clear: both;"></div>                        
        </div>

        <?php
    }
    ?>
    <?php
} else {
     echo 'no result'; 
}
?>