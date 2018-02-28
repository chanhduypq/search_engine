@extends('layouts.app')

@section('content')
    <div class="loading" id="loading" style="display: none;">Loading&#8230;</div>
    
    <form id="frm">

        <!--<h2>Custom Search</h2>-->
        <div id="custom-search-input" style="margin-top: 50px;">
            <div class="input-group col-md-4">
                <input placeholder="Please enter keyword" type="text" class="search-query form-control" id="keywork" name="keywork"/>
                <li class="input-group-btn">
                    <button class="btn btn-danger" type="submit">
                        <li class="glyphicon glyphicon-search"></li>
                    </button>
                </li>
            </div>
        </div>
        <div id="result" class="list-group row" style="display: none;">
            <!--<h1>Result</h1>-->
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