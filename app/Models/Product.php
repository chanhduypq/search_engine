<?php 

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'product';

    public function __construct() {
        parent::__construct();
    }

    public function getProducts(&$total, $limit = null, $start = null) {
        if (false) {
            $items = DB::select("select * from product order by id limit $limit,$start");
        } else {
            $items = DB::select("select * from product order by id");
        }

        $total = DB::select("select count(*) as count from product");
        $total = $total[0];
        $total = $total['count'];

        return $items;
    }

    

}

?>