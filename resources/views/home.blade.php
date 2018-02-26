@extends('layouts.app')

@section('content')
<style>
    .pagination.pagination-lg li{
        padding: 5px;
    }
    #custom-search-input{
        margin-bottom: 20px;
    }
    .list-group.searched{
        border: 1px solid black;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        padding: 20px;
    }
    .list-group h1{
        display: none;
    }
    .list-group.searched h1{
        text-align: center;
        display: block;
    }
    .list-group-item a{
        word-break: break-all;
    }

</style>	
<div class="container">
    <div class="row">

        <form method="GET" action="{{ route('search') }}">
    
            <h2>Custom Search</h2>
            <div id="custom-search-input">
                <div class="input-group col-md-12">
                    <input type="text" class="  search-query form-control" name="search_terms" value="{{ $search_terms ?? null }}" />
                    <li class="input-group-btn">
                        <button class="btn btn-danger" type="submit">
                            <li class="glyphicon glyphicon-search"></li>
                        </button>
                    </li>
                </div>
            </div>

            <?php 
                if (isset($result->items) && count($result->items) > 0){?>
                <div class="list-group searched" style="width: 100%;">
                    <h1>Result</h1>
                  <?php foreach ($result->items as $item){?>
                    <div style="color:#555;" class="list-group-item">
                        <h4 class="list-group-item-heading">{{ $item->title }}</h4>
                        <a href="{{ $item->link }}" target="_blank">{{ $item->link }}</a>
                        <p class="list-group-item-text">{{ $item->snippet }}</p>
                    </div>
                  
                  <?php 
                  }
                  if ($count > 0) {?>
                    <div style="width: 100%;margin: 0 auto;text-align: center;">
                      <ul class="pagination pagination-lg">
                      <?php 
                        $numberPage = ceil($count / $NUMBER_ROW_PERPAGE);

                        $rangeCount = 3;

                        $rangeNumber = ceil($numberPage / $rangeCount);
                        $range = ceil($page / $rangeCount);
                        $start = $range * $rangeCount - ($rangeCount - 1);

                        if ($page == 1) {
                            $hrefPrev = "javascript:void(0)";
                            $hrefFirst = "javascript:void(0)";
                            $classPrevFirst = "active";
                        } else {
                            $hrefFirst=route('index.search', [
                                  'search_terms' => $search_terms,
                                  'page' => '1',
                              ]);
                            $hrefPrev=route('index.search', [
                                  'search_terms' => $search_terms,
                                  'page' => ($page - 1),
                              ]);
                            $classPrevFirst = "";
                        }

                        if ($page == $numberPage) {
                            $hrefNext = "javascript:void(0)";
                            $hrefLast = "javascript:void(0)";
                            $classNextLast = "active";
                        } else {
                            $hrefLast=route('index.search', [
                                  'search_terms' => $search_terms,
                                  'page' => $numberPage,
                              ]);
                            $hrefNext=route('index.search', [
                                  'search_terms' => $search_terms,
                                  'page' => ($page + 1),
                              ]);
                            $classNextLast = "";
                        }
                        ?>

                                <li class="<?php echo $classPrevFirst; ?>">
                                    <a href="<?php echo $hrefFirst; ?>">First</a>
                                </li>
                                <li class="<?php echo $classPrevFirst; ?>">
                                    <a href="<?php echo $hrefPrev; ?>">Previous</a>
                                </li>

                                <?php
                                for ($i = 1; $i <= 3 && $start <= $numberPage; $i++) {
                                    if ($page == $start) {
                                        $href = "javascript:void(0)";
                                        $class = 'active';
                                    } else {
                                        $href =route('index.search', [
                                              'search_terms' => $search_terms,
                                              'page' => $start,
                                          ]); 
                                        $class = '';
                                    }
                                    ?>
                                    <li class="<?php echo $class; ?>">
                                        <a href="<?php echo $href; ?>"><?php echo $start; ?></a>
                                    </li>
                                    <?php
                                    $start++;
                                }
                                ?>

                                <li class="<?php echo $classNextLast; ?>">
                                    <a href="<?php echo $hrefNext; ?>">Next</a>
                                </li>
                                <li class="<?php echo $classNextLast; ?>">
                                    <a href="<?php echo $hrefLast; ?>">Last</a>
                                </li>

                      </ul>
                    </div>
                        <?php
                    }
                    ?>
                </div>
            <?php
                }
                else{?>
                 <div class="list-group<?php if($search_terms!='') echo ' searched';?>" style="width: 100%;">
                     <h1>Result</h1>
                  <?php if ($search_terms!='') echo 'no result';?>  
                </div>
            
            <?php 
                }
            
            ?>
            
            
                

        </form>
    </div>
</div>
@stop
<?php 
function recursiveFind(array $haystack, $needle)
{
    $iterator  = new RecursiveArrayIterator($haystack);
    $recursive = new RecursiveIteratorIterator(
        $iterator,
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($recursive as $key => $value) {
        if ($key === $needle) {
            return $value;
        }
    }
}
?>