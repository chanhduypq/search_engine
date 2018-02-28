<?php 

namespace App\Models;

//use Illuminate\Auth\Authenticatable;
//use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Notifications\Notifiable;
//use Illuminate\Auth\Passwords\CanResetPassword;
//use Illuminate\Foundation\Auth\Access\Authorizable;
//use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
//use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
//use Illuminate\Database\Eloquent\SoftDeletes;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Common\Numeric;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'product';

    public function __construct() {
        parent::__construct();
    }

    public function getProducts(&$total, $limit = null, $start = null) {
        if (Numeric::isInteger($limit) && Numeric::isInteger($start)) {
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