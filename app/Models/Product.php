<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'base_unit', 'status',
    ];

    public static function getStock($saleId = null)
    {
        $saleCond = $saleId != null ? "AND sale_id!='".$saleId."'" : '';
        $data = Product::select('products.*', DB::raw("((IFNULL(A.inQty, 0) + IFNULL(D.inQty, 0)) - (IFNULL(B.outQty, 0) + IFNULL(C.outQty, 0))) AS stockQty"))
        ->join(DB::raw("(SELECT product_id, SUM(actual_quantity) AS inQty FROM stock_items WHERE deleted_at IS NULL GROUP BY product_id) AS A"), function($q) {
            $q->on('A.product_id', '=', 'products.id');
        })
        ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS outQty FROM stock_return_items WHERE deleted_at IS NULL GROUP BY product_id) AS B"), function($q) {
            $q->on('B.product_id', '=', 'products.id');
        })
        ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS outQty FROM sale_items WHERE deleted_at IS NULL $saleCond GROUP BY product_id) AS C"), function($q) {
            $q->on('C.product_id', '=', 'products.id');
        })
        ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS inQty FROM sale_return_items WHERE deleted_at IS NULL GROUP BY product_id) AS D"), function($q) {
            $q->on('D.product_id', '=', 'products.id');
        })
        ->where('status', 'Active')
        ->having('stockQty', '>', 0)
        ->get();
        return $data;
    }
}
