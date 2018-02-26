<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public $totalPages = 10;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    public function search(Request $request)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', 0);
        
        $result = [];
        $params = [];
        $toIndex = 10;
        $NUMBER_ROW_PERPAGE=10;
        $count=0;
        
        if ($request->post('page') && ctype_digit($request->post('page'))) {
            $page = $request->post('page');
        } else {
            $page = 1;
        }
        $offset = ($page - 1) * $NUMBER_ROW_PERPAGE+1;

        if ($searchTerms = $request->post('search_terms')) {

            $searchTermsFiltred = urlencode(str_replace(' ', '+', trim($searchTerms)));

            $toIndex = $request->post('to_index') ?? 10;
            
            $key = "AIzaSyB16wvV51-FSuB4n5dbGgNqtxLuRWh5z8s";
            $cx = "007043409519568967944:zqts7n3gnc4";

            $url = "https://www.googleapis.com/customsearch/v1?start={$offset}&key={$key}&cx={$cx}&q={$searchTermsFiltred}";
            $ch = curl_init($url);
            curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER , false );
            curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1 );
            $result = curl_exec($ch);        
            curl_close($ch);

            $result = json_decode($result);
            if(isset($result->error)){
                  $error=$result->error->errors;
                  $error=$error[0];
              }
            
            
            if (isset($result->queries)) {
                if (isset($result->queries->totalResults)) {
                    $count = $result->queries->totalResults;
                } else {
                    $temp = $result->queries->request;
                    
                    $temp = $temp[0];
                    
                    $count = $temp->totalResults;
                    
                }
            }
            if (!isset($result->items)) {
                
                return view('home', [
                    'result' => null,
                    'search_terms' => $searchTerms ?? '',
                    'page'=>$page,
                    'count'=>$count,
                    'NUMBER_ROW_PERPAGE'=>$NUMBER_ROW_PERPAGE,
                    
                ]);
            }
        }

        return view('home', [
            'result' => $result ?? null,
            'search_terms' => $searchTerms ?? '',
            'params' => $params,
            'page'=>$page,
            'count'=>$count,
            'NUMBER_ROW_PERPAGE'=>$NUMBER_ROW_PERPAGE,
        ]);
    }

}
