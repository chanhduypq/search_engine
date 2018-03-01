<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller {

    public $allProducts = array();
    public $getfirstpage = true;
    public $via_proxy = true;
    public $proxyAuth = 'galvin24x7:egor99';
    public $mysqli;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->mysqli = new \mysqli('localhost', 'root', '', 'engine_search');
        $this->mysqli->query('SET NAMES utf8mb4;');
    }

    public function search(Request $request) {
        
        @set_time_limit(0);
        @ini_set('max_execution_time', 0);
        require_once 'simple_html_dom.php';

        $result = [];
        $keywork = '';

        if ($keywork = $request->post('keywork')) {
            $this->insert($keywork);
            return view('result', [
                'result' => $this->allProducts,
            ]);
        } else {
            $sql = "select created_at,keyword from product order by id desc LIMIT 1";
            $result = $this->mysqli->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $created_at = $row['created_at'];
                    $keywork = $row['keyword'];
                    break;
                }
                $sql = "select * from product where created_at='$created_at'";
                $result = $this->mysqli->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $data = $row;
                    if (ceil($data['price']) == floor($data['price'])) {
                        $data['price'] = '$' . number_format(intval($data['price']), 0, ".", ",");
                    } else {
                        $data['price'] = '$' . number_format($data['price'], 2, ".", ",");
                    }

                    if ($data['sale_price'] != '') {
                        if (ceil($data['sale_price']) == floor($data['sale_price'])) {
                            $data['sale_price'] = '$' . number_format(intval($data['sale_price']), 0, ".", ",");
                        } else {
                            $data['sale_price'] = '$' . number_format($data['sale_price'], 2, ".", ",");
                        }
                    }

                    $this->allProducts[] = $data;
                }
            }
        }

        return view('home', [
            'result' => $this->allProducts,
            'keywork' => $keywork
        ]);
    }
    
    

    public function getStoreUrl(Request $request){

        $result = '';
        if ($url = $request->post('url')) {
            //get id
            $id = str_replace(array('https://ezbuy.sg/product/','.html'), '', $url);
            //json content
            $json = '{"catalogCode":"SG","identifier":"'.trim($id).'","entrance":1,"src":"","userInfo":{"customerId":0,"isPrime":false},"loadLocal":false}';
            //get product details
            $json_data = $this->curl_getcontent("https://sg-en-web-api.ezbuy.sg/api/EzProduct/GetProduct",$json,$url);
            $json_data = json_decode($json_data,true);
            //check isset shop name
            if(isset($json_data['shopName']) && trim($json_data['shopName'])!=''){
                $result = "https://ezbuy.sg/shop/".trim($json_data['shopName']);
            }
        }
        //return
        return $result;
    }
    
    private function insert($keywork){
        $this->start_qoo10($keywork);
        $this->start_shopee($keywork);
        $this->start_lazada($keywork);
        $this->start_carousell($keywork);
        $this->start_ezbuy($keywork);
        foreach ($this->allProducts as &$data){
            $data['price']= str_replace(",", "", $data['price']);
            preg_match_all('!\d+\.*\d*!',  $data['price'], $matches);
            if(ceil($matches[0][0])== floor($matches[0][0])){
                $data['price']= '$'.number_format(intval($matches[0][0]), 0, ".", ",") ;
            }
            else{
                $data['price']= '$'.number_format($matches[0][0], 2, ".", ",") ;
            }

            if($data['sale_price']!=''){
                $data['sale_price']= str_replace(",", "", $data['sale_price']);
                preg_match_all('!\d+\.*\d*!',  $data['sale_price'], $matches);
                if(ceil($matches[0][0])== floor($matches[0][0])){
                    $data['sale_price']= '$'.number_format(intval($matches[0][0]), 0, ".", ",") ;
                }
                else{
                    $data['sale_price']= '$'.number_format($matches[0][0], 2, ".", ",") ;
                }
            }
        }

        $sql = 'INSERT INTO product (product_name,product_url,image,price,sale_price,store_name,store_url,currency,keyword,created_at,site) VALUES ';
        $datas = $this->allProducts;
        for ($i = 0; $i < count($datas); $i++) {
            $price = str_replace(",", "", $datas[$i]['price']);
            $price = str_replace("$", "", $price);
            $sale_price = str_replace(",", "", $datas[$i]['sale_price']);
            $sale_price = str_replace("$", "", $sale_price);
            if($sale_price==''){
                $sale_price='NULL';
            }
            $sql .= "('" . $this->mysqli->real_escape_string($datas[$i]['product_name']) . "','" .
                    $this->mysqli->real_escape_string($datas[$i]['product_url']) . "','" .
                    $this->mysqli->real_escape_string($datas[$i]['image']) .
                    "',$price,$sale_price,'" . $this->mysqli->real_escape_string($datas[$i]['store_name']) . "','" .
                    $this->mysqli->real_escape_string($datas[$i]['store_name']) .
                    "','$','".$this->mysqli->real_escape_string($keywork)."','".date('Y-m-d H:i:s')."','".$datas[$i]['site']."'),";
        }
        $sql = rtrim($sql, ',');
        $this->mysqli->query($sql);
    }

    private function start_ezbuy($keywork) {

        $origin_keywork = $keywork;
        $keywork = str_replace('"', '\"', $keywork);
        $keywork = str_replace("'", "\'", $keywork);
        $json_data = array(
                            'searchCondition' => array( 
                                                'limit' => 56, 
                                                'offset' => 0, 
                                                'propValues' =>array(), 
                                                'filters' =>array(), 
                                                'keyWords' => $keywork, 
                                                'categoryId' => 0
                                            ),
                            'limit' => 56, 
                            'offset' => 0, 
                            'language' => 'en', 
                            'dataType' => 'new');

        $json_data = $this->curl_getcontent("https://sg-en-web-api.ezbuy.sg/api/EzCategory/ListProductsByCondition",json_encode($json_data),'https://ezbuy.sg/category/?keywords='.urlencode($keywork));
        $json_data = json_decode($json_data, true);
        //check isset products
        if(isset($json_data['products'])  && !empty($json_data['products']) ){
            foreach ($json_data['products'] as $product) {
                // $product
                if(isset($product['name']) && trim($product['name'])!='' ){

                    if($this->checkCompareProduct($product['name'], $origin_keywork))
                    {

                        if(isset($product['originPrice']) && $product['originPrice']!=$product['price']){
                            $price = $product['originPrice'];
                            $sale_price = $product['price'];
                        }
                        else{
                            $price = $product['price'];
                            $sale_price = 0;
                        }

                        $data = array(
                                'product_name'=>$product['name'],
                                'product_url'=>(isset($product['url']))?"https://ezbuy.sg/product/".trim($product['url']).".html":'',
                                'image'=>(isset($product['picture']))?trim($product['picture']):'',
                                'price'=>$price,
                                'sale_price'=>$sale_price,
                                'store_name'=>(isset($product['titleIcons'][0]['text']) && trim($product['titleIcons'][0]['text'])!='')?trim($product['titleIcons'][0]['text']):'go to shop',
                                'store_url'=>'#',
                            );

                        $data['site'] = 'ezbuy';

                        //merege data
                        $this->allProducts[]= $data;

                    }//end checkCompareProduct

                }
            }//end loop all products
        }//end check isset products
        
    }

    private function start_shopee($keywork) {

        $last_page = false;
        $page_num = 0;
        while ($last_page == false) {

            $page_num++;
            $last_page = true;
            //
            $url = "https://shopee.sg/api/v1/search_items/?by=pop&order=desc&keyword=" . urlencode($keywork) . "&newest=" . (($page_num * 50) - 50) . "&limit=50";
            $json_data = $this->curl_getcontent($url);
            $json_data = str_replace("\"items\"", "\"item_shop_ids\"", $json_data);
            $json_data = json_decode($json_data, true);
            if (isset($json_data['item_shop_ids'])) {
                $json_data = json_encode(array('item_shop_ids' => $json_data['item_shop_ids']));
                $json_data = $this->curl_getcontent("https://shopee.sg/api/v1/items/", $json_data, "https://shopee.sg/search/?keyword=" . urlencode($keywork) . "^&page=" . $page_num);
                $json_data = json_decode($json_data, true);
                foreach ($json_data as $product) {

                    if (isset($product['name']) && trim($product['name']) != '') {

                        if($this->checkCompareProduct($product['name'], $keywork))
                        {

                            if ($product['price_before_discount'] != 0) {
                                $price = $product['price_before_discount'];
                                $sale_price = $product['price'];
                            } else {
                                $price = $product['price'];
                                $sale_price = $product['price_before_discount'];
                            }

                            $data = array(
                                'product_name' => $product['name'],
                                'product_url' => "https://shopee.sg/product/" . $product['shopid'] . "/" . $product['itemid'] . "/",
                                'image' => (trim($product['image']) != '') ? "https://cfshopeesg-a.akamaihd.net/file/" . trim($product['image']) . "_tn" : '',
                                'price' => $price/100000,
                                'sale_price' => $sale_price/100000,
                                'store_name' => 'go to shop',
                                'store_url' => "https://shopee.sg/shop/" . $product['shopid'] . "/"
                            );
                            
                            $data['site'] = 'shopee';

                            //merege data
                            $this->allProducts[] = $data;
                        }//end checkCompareProduct
                        $last_page = false;
                    }
                }//end loop all producs in page
            }
            //check get first page
            if ($this->getfirstpage == true)
                break;
        }//end loop all pages	
    }

    private function start_qoo10($keywork) {

        $last_page = false;
        $page_num = 0;
        while ($last_page == false) {

            $page_num++;
            $last_page = true;
            $url = "https://www.qoo10.sg/gmkt.inc/Search/SearchResultAjaxTemplate.aspx?&keyword_hist=" . urlencode($keywork) . "&sortType=RANK_POINT&dispType=LIST&filterDelivery=NNNNNA&is_research_yn=Y&hid_keyword=" . urlencode($keywork) . "&coupon_filter_no=0&is_img_search_yn=N&search_option=tt&partial=off&curPage=" . $page_num . "&pageSize=60&ajax_search_type=S&___cache_expire___=3507155922359";
            $html = $this->curl_getcontent($url);
            $html_base = new \simple_html_dom();
            $html_base->load($html);

            $nodes = $html_base->find("tr");
            foreach ($nodes as $node) {
                if ($node->goodscode != '') {

                    $data = array(
                        'product_name' => '',
                        'product_url' => '',
                        'image' => '',
                        'price' => '',
                        'sale_price' => '',
                        'store_name' => '',
                        'store_url' => ''
                    );

                    //product_name
                    $tmp = $node->find('.td_item a[data-type="goods_url"]');
                    $data['product_name'] = (isset($tmp[0])) ? trim($tmp[0]->plaintext) : '';
                    $data['product_url'] = (isset($tmp[0])) ? trim($tmp[0]->href) : '';

                    if($this->checkCompareProduct($data['product_name'], $keywork)){

                        //image
                        $tmp = $node->find(".td_thmb img");
                        $data['image'] = (isset($tmp[0])) ? trim($tmp[0]->gd_src) : '';
                        //sale_price
                        $tmp = $node->find(".td_prc .prc strong");
                        $data['sale_price'] = (isset($tmp[0])) ? trim($tmp[0]->plaintext) : '';
                        //price
                        $tmp = $node->find(".td_prc .prc .dc_prc");
                        $data['price'] = (isset($tmp[0])) ? trim($tmp[0]->plaintext) : '';
                        //update price
                        if ($data['price'] == '') {
                            $data['price'] = $data['sale_price'];
                            $data['sale_price'] = '';
                        }
                        //store_name
                        $tmp = $node->find(".opt_dtl a.lnk_sh");
                        $data['store_name'] = (isset($tmp[0])) ? trim($tmp[0]->plaintext) : '';
                        $data['store_url'] = (isset($tmp[0])) ? trim($tmp[0]->href) : '';
                        $data['site'] = 'qoo10';

                        //merege data
                        $this->allProducts[] = $data;
                    }//end checkCompareProduct
                    $last_page = false;
                }//end check is product item
            }//end foreach
            // clear html_base
            $html_base->clear();
            unset($html_base);

            //check get first page
            if ($this->getfirstpage == true)
                break;
        }//end loop all pages
    }

    private function start_lazada($keywork) {


        $last_page = false;
        $page_num = 0;
        while ($last_page == false) {

            $page_num++;
            $last_page = true;

            $url = "https://www.lazada.sg/catalog/?page=" . $page_num . "&q=" . urlencode($keywork);
            $html = $this->curl_getcontent($url);
            $products = $this->getBetweenXandY($html, 'window.pageData=', '</script>');
            if ($products != false) {
                $products = json_decode($products, true);
                if (isset($products['mods']['listItems']) && !empty($products['mods']['listItems'])) {

                    foreach ($products['mods']['listItems'] as $product) {

                        if (isset($product['name']) && trim($product['name']) != '') {

                            if($this->checkCompareProduct($product['name'], $keywork))
                            {

                                if (isset($product['originalPrice']) && $product['originalPrice'] != 0) {
                                    $price = $product['originalPrice'];
                                    $sale_price = $product['price'];
                                } else {
                                    $price = $product['price'];
                                    $sale_price = 0;
                                }

                                $data = array(
                                    'product_name' => $product['name'],
                                    'product_url' => (isset($product['productUrl'])) ? trim($product['productUrl']) : '',
                                    'image' => (isset($product['image'])) ? trim($product['image']) : '',
                                    'price' => $price,
                                    'sale_price' => $sale_price,
                                    'store_name' => (isset($product['sellerName'])) ? trim($product['sellerName']) : '',
                                    'store_url' => (isset($product['sellerId'])) ? trim($product['sellerId']) : '',
                                );
                                
                                $data['site'] = 'lazada';

                                //merege data
                                $this->allProducts[] = $data;
                            }//end checkCompareProduct
                            $last_page = false;
                        }
                    }//end loop all producs in page
                }
            }//end check isset json data
            //check get first page
            if ($this->getfirstpage == true)
                break;
        }//end loop all pages
    }

    private function start_carousell($keywork) {

        $url = "https://sg.carousell.com/search/products/?query=" . urlencode($keywork);
        $html = $this->curl_getcontent($url);
        $html_base = new \simple_html_dom();
        $html_base->load($html);

        $nodes = $html_base->find(".card-row .col-lg-3");
        foreach ($nodes as $node) {
            if ($node->{'data-reactid'} != '') {

                $data = array(
                    'product_name' => '',
                    'product_url' => '',
                    'image' => '',
                    'price' => '',
                    'sale_price' => '',
                    'store_name' => '',
                    'store_url' => ''
                );

                //product_name
                $tmp = $node->find('#productCardTitle');
                $data['product_name'] = (isset($tmp[0])) ? trim($tmp[0]->plaintext) : '';

                if($this->checkCompareProduct($data['product_name'], $keywork))
                {

                    //product_url
                    $tmp = $node->find('a#productCardThumbnail');
                    $tmp = (isset($tmp[0])) ? trim($tmp[0]->href) : '';
                    $tmp = explode('?', $tmp);
                    $data['product_url'] = 'https://sg.carousell.com' . trim($tmp[0]);
                    //image
                    $tmps = $node->find("img");
                    foreach ($tmps as $tmp) {
                        if ($tmp->{'data-layzr'} != '') {
                            $data['image'] = trim($tmp->{'data-layzr'});
                            break;
                        }
                    }
                    if ($data['image'] == '') {
                        $tmp = $node->find("img");
                        $data['image'] = (isset($tmp[0])) ? trim($tmp[0]->src) : '';
                    }

                    //price
                    $tmp = $node->find("dl dd");
                    $data['price'] = (isset($tmp[0])) ? trim($tmp[0]->plaintext) : '';
                    //store_name
                    $tmp = $node->find("h3");
                    $data['store_name'] = (isset($tmp[0])) ? trim($tmp[0]->plaintext) : '';
                    //store_url
                    $tmp = $node->find("a.media");
                    $data['store_url'] = (isset($tmp[0])) ? 'https://sg.carousell.com' . trim($tmp[0]->href) : '';

                    $data['site'] = 'carousell';
                    //merege data
                    $this->allProducts[] = $data;

                }//end checkCompareProduct
            }//end check is product item
        }//end foreach
        // clear html_base
        $html_base->clear();
        unset($html_base);
    }

    // @getBetweenXandY
    private function getBetweenXandY($string, $a, $b) {
        $result = false;
        if (strrpos($string, $a) !== false) {
            $tmp = explode($a, $string);
            if (strrpos($tmp[1], $b) !== false) {
                $tmp = explode($b, $tmp[1]);
                $result = trim($tmp[0]);
            }
        }
        return $result;
    }

    private function checkCompareProduct($product_name, $keywork){

        $product_name = str_replace(array('_','-'), ' ', strtolower($product_name));
        $keywork = trim(str_replace(array('_','-'), ' ', strtolower($keywork)));
        if(strrpos($product_name, $keywork)!==false){
            return true;
        }
        else{
            return false;
        }
    }

    private function curl_getcontent($url, $json = false, $referer = false, $count = 0) {

        $headers = array();
        $headers[] = "Accept-Encoding: gzip, deflate";
        $headers[] = "Accept-Language: en-US,en;q=0.9";
        $headers[] = "Upgrade-Insecure-Requests: 1";
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        if ($json != false) {
            $headers[] = "X-Requested-With: XMLHttpRequest";
            $headers[] = "Accept: application/json";
            $headers[] = "Content-Type: application/x-www-form-urlencoded";
        } else {
            $headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";
        }
        $headers[] = "Cache-Control: max-age=0";
        $headers[] = "Connection: keep-alive";
        if ($referer != false) {
            $headers[] = "Referer: " . $referer;
        }

        if (strrpos($url, 'shopee.sg/api/v1/items') !== false) {
            $headers[] = "X-Csrftoken: Xg7jJJwJ4r6fcrtPDchtGilnpfaAB4YO";
            $headers[] = "Cookie: _ga=GA1.2.680186883.1519700856; _gid=GA1.2.1946785702.1519700856; cto_lwid=6b9cee5e-f4f1-41c6-bdb7-9a3648cb988c; csrftoken=Xg7jJJwJ4r6fcrtPDchtGilnpfaAB4YO; __BWfp=c1519700860546xa2fcd5d59; SPC_IA=-1; SPC_U=-; SPC_EC=-; bannerShown=true; SPC_SC_TK=; UYOMAPJWEMDGJ=; SPC_SC_UD=; SPC_F=zRjUetudjMWMgmUmr3f8Tij0vF6L3p0I; REC_T_ID=5c6c2e48-1b6b-11e8-9b1a-1866dab29c0a; SPC_T_ID=\"99jVmjgK9KZL0SnPMX/yuwLLv9M3sEGDo+J3VZT9ZSQx3lifdMmK2MmdqjtqdRttt3ZgPL+lyYVOwmvMZ1z5kZsGi/X9Sfz54Vps8e6Eq1w=\"; SPC_SI=qqdhd4n2jlz4i9124ah7o2wr8m4gaafz; SPC_T_IV=\"Eqx+GcOke9cn5Sl3jATR4A==\"; _gat=1";
        }


        $ch = curl_init();

        //check use proxies
        if ($this->via_proxy) {
            curl_setopt($ch, CURLOPT_PROXY, 'http://' . $this->getProxy());
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);
        }
        //post json data
        if ($json != false) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $content = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // print_r("status: ".$status."\n");

        if (($status != 200 && $status != 404) || trim($content) == '' || (strpos($url, 'abc') !== false && strpos($content, '</html>') !== false) && $count < 4) {
            sleep(1);
            $count++;
            return $this->curl_getcontent($url, $json, $referer, $count);
        }

        return $content;
    }

    private function getProxy() {
        $f_contents = file("proxies.txt");
        $line = trim($f_contents[rand(0, count($f_contents) - 1)]);
        return $line;
    }

}
