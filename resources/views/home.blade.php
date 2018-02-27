@extends('layouts.app')

@section('content')
<div class="my_loading">
</div>
<img src="public/images/loading.gif" class="my_loading">
<div class="container">
    <div class="row">

        <form id="frm">
    
            <!--<h2>Custom Search</h2>-->
            <div id="custom-search-input" style="margin-top: 50px;">
                <div class="input-group col-md-12">
                    <input placeholder="Please enter keyword" type="text" class="search-query form-control" id="keywork" name="keywork"/>
                    <li class="input-group-btn">
                        <button class="btn btn-danger" type="submit">
                            <li class="glyphicon glyphicon-search"></li>
                        </button>
                    </li>
                </div>
            </div>
            <div id="result" class="list-group searched" style="width: 100%;display: none;">
                <!--<h1>Result</h1>-->
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($){
        $("#frm").submit(function (e){
            e.preventDefault();
            if($.trim($("#keywork").val())==''){
                alert("Please enter keywork.");
                return;
            }
            $('.my_loading').show();
            $("#result").html('').hide();
            $.ajax({
               url: "{{ route('search') }}"+"?keywork="+$.trim($("#keywork").val()),
               type: 'GET',
               success: function (data, textStatus, jqXHR) {
                  $('.my_loading').hide();
                  $("#result").show().html(data);      
               }
            });
        });
        
        $("body").delegate(".store_name.lazada", "click", function(){
            $('.my_loading').show();
            product_url=$(this).parent().find('.name').eq(0).find('a').eq(0).attr('href');
            $.ajax({
               url: product_url,
               type: 'GET',
               success: function (data, textStatus, jqXHR) {
                  $('.my_loading').hide();
                  nodes=$.parseHTML(data);
                  url=$(nodes).find('div.seller-name__detail').eq(0).find('a').eq(0).attr('href');
                  if(url != undefined && url != 'undefined' && url !=''){
                      window.open(url,'_blank'); 
                  }
                  else{
                      window.open('https://lazada.sg','_blank'); 
                  }
                     
               }
            });
        });
    });
</script>
@stop