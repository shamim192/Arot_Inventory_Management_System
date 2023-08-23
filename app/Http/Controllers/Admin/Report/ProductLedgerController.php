<?php

namespace App\Http\Controllers\Admin\Report;

use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class ProductLedgerController extends Controller
{
    public function stock(Request $request)
    {
        $sql = Product::select('products.*', DB::raw('IFNULL(A.inQty, 0) AS stockInQty'), DB::raw('IFNULL(B.outQty, 0) AS sReturnQty'), DB::raw('IFNULL(C.outQty, 0) AS saleQty'), DB::raw('IFNULL(D.inQty, 0) AS cReturnQty'),  DB::raw("((IFNULL(A.inQty, 0) + IFNULL(D.inQty, 0)) - (IFNULL(B.outQty, 0) + IFNULL(C.outQty, 0))) AS stockQty"))
        ->join(DB::raw("(SELECT product_id, SUM(actual_quantity) AS inQty FROM stock_items WHERE deleted_at IS NULL GROUP BY product_id) AS A"), function($q) {
            $q->on('A.product_id', '=', 'products.id');
        })
        ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS outQty FROM stock_return_items WHERE deleted_at IS NULL GROUP BY product_id) AS B"), function($q) {
            $q->on('B.product_id', '=', 'products.id');
        })
        ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS outQty FROM sale_items WHERE deleted_at IS NULL GROUP BY product_id) AS C"), function($q) {
            $q->on('C.product_id', '=', 'products.id');
        })
        ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS inQty FROM sale_return_items WHERE deleted_at IS NULL GROUP BY product_id) AS D"), function($q) {
            $q->on('D.product_id', '=', 'products.id');
        })
        ->where('status', 'Active')
        ->having('stockQty', '>', 0);

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('products.name', 'LIKE', $request->q.'%')
                    ->orWhere('products.base_unit', 'LIKE', $request->q.'%');
            });
        }

        $reports = $sql->paginate($request->limit ?? 15);
        
        $units = Unit::where('status', 'Active')->get();
        
        $unit = null;
        if ($request->unit) {
            $unit = Unit::find($request->unit);
        }

        return view('admin.report.product-stock', compact('reports', 'unit', 'units'));
    }
    
    public function ledger(Request $request)
    {
        $products = Product::where('status', 'Active')->get();
        $units = Unit::where('status', 'Active')->get();

        if ($request->product == null) {
            return view('admin.report.product-ledger', compact('products', 'units'));
        }
        
        $product = Product::find($request->product);
        
        $unit = null;
        if ($request->unit) {
            $unit = Unit::find($request->unit);
        }

        $asOnDate = $request->date ? "AND DATE(created_at) <= '".dbDateFormat($request->date)."'" : '';
        $query1 = "SELECT 'Stock In' AS type, 'stock.show' AS route, stock_id AS rowId, created_at, actual_quantity FROM stock_items WHERE product_id = $request->product $asOnDate";
        $query2 = "SELECT 'Stock Return' AS type, 'stock-return.show' AS route, stock_return_id AS rowId, created_at, actual_quantity FROM stock_return_items WHERE product_id = $request->product $asOnDate";
        $query3 = "SELECT 'Sale' AS type, 'sale.show' AS route, sale_id AS rowId, created_at, actual_quantity FROM sale_items WHERE product_id = $request->product $asOnDate";
        $query4 = "SELECT 'Sale Return' AS type, 'sale-return.show' AS route, sale_return_id AS rowId, created_at, actual_quantity FROM sale_return_items WHERE product_id = $request->product $asOnDate";
        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4) S ORDER BY S.`created_at` ASC");

        return view('admin.report.product-ledger', compact('reports', 'product', 'unit', 'products', 'units'));
    }

       
   

}
