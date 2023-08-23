<?php

namespace App\Http\Controllers\Api;

use App\Models\Unit;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\StockItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\SaleReturnItem;
use App\Models\StockReturnItem;
use App\Models\WarehouseReceive;
use App\Models\WarehouseTransfer;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Setting;
use App\Models\StockReturn;
use App\Models\WarehouseIssue;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": "Setting Provided.",
        * "data": {
            * "id": 1,
            * "is_open": 1
        * }
     * }
     */
    public function setting()
    {
        $setting = Setting::find(1);

        return response()->json([
            'status'    => true,
            'message'   => 'Setting Provided.',
            'data'      => $setting,
        ], 200);
    }
    
    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": "Setting Updated.",
        * "data": {
            * "id": 1,
            * "is_open": 1
        * }
     * }
     */
    public function settingUpdate()
    {
        $setting = Setting::find(1);
        if (!empty($setting)) {
            $setting->update(['is_open' => $setting->is_open == 1 ? 0 : 1]);
        } else {
            $setting = Setting::updateOrCreate(['id' => 1], ['is_open' => 1]);
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Setting Updated.',
            'data'      => $setting,
        ], 200);
    }

    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": "Customers Provided.",
        * "data": [
            * {
                * "id": 1,
                * "name": "Customer 1",
                * "mobile": "01711000000",
                * "address": null,
                * "shop_name": "Shop 1",
                * "previous_due": "0.00",
                * "status": "Active"
            * },
            * {
                * "id": 2,
                * "name": "Customer 2",
                * "mobile": "01811000000",
                * "address": null,
                * "shop_name": "Shop 2",
                * "previous_due": "0.00",
                * "status": "Active"
            * }
        * ]
     * }
     */
    public function customer()
    {
        $customers = Customer::where('status', 'Active')->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Customers Provided.',
            'data'      => $customers,
        ], 200);
    }

    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": "Suppliers Provided.",
        * "data": [
            * {
                * "id": 1,
                * "name": "Supplier 1",
                * "mobile": "01711000000",
                * "address": null,
                * "shop_name": "Shop 1",
                * "previous_due": "0.00",
                * "status": "Active"
            * },
            * {
                * "id": 2,
                * "name": "Supplier 2",
                * "mobile": "01811000000",
                * "address": null,
                * "shop_name": "Shop 2",
                * "previous_due": "0.00",
                * "status": "Active"
            * }
        * ]
     * }
     */
    public function supplier()
    {
        $suppliers = Supplier::where('status', 'Active')->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Suppliers Provided.',
            'data'      => $suppliers,
        ], 200);
    }

    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": "Units Provided.",
        * "data": [
            * {
                * "id": 1,
                * "base_unit": "Kg",
                * "name": "25 Kg",
                * "quantity": "25.00",
                * "status": "Active"
            * },
            * {
                * "id": 2,
                * "base_unit": "Litre",
                * "name": "5 Litre",
                * "quantity": "5.00",
                * "status": "Active"
            * }
        * ]
     * }
     */
    public function unit()
    {
        $units = Unit::where('status', 'Active')->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Units Provided.',
            'data'      => $units,
        ], 200);
    }

    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": "Products Provided.",
        * "data": [
            * {
                * "id": 1,
                * "name": "Rice",
                * "base_unit": "Kg",
                * "status": "Active"
            * },
            * {
                * "id": 2,
                * "name": "Oil",
                * "base_unit": "Litre",
                * "status": "Active"
            * }
        * ]
     * }
     */
    public function product()
    {
        $products = Product::where('status', 'Active')->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Products Provided.',
            'data'      => $products,
        ], 200);
    }

    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": "Warehouses Provided.",
        * "data": [
            * {
                * "id": 1,
                * "name": "Warehouse 1",
                * "address": null,
                * "status": "Active"
            * },
            * {
                * "id": 2,
                * "name": "Warehouse 2",
                * "address": null,
                * "status": "Active"
            * }
        * ]
     * }
     */
    public function warehouse()
    {
        $warehouses = Warehouse::where('status', 'Active')->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Warehouses Provided.',
            'data'      => $warehouses,
        ], 200);
    }

    /**
     * @authenticated
     * @bodyParam warehouse_id integer required The ID of the warehouse. Example: 1
     * @bodyParam product_id integer The ID of the product. Example: 1
     * @bodyParam unit_id integer The ID of the unit. Example: 1
     * @response {
        * "status": true,
        * "message": "Warehouse received Provided.",
        * "data": [
            * {
                * "id": 1,
                * "product_id": "1",
                * "unit_id": "1",
                * "rcvQuantity": "200"
                * "issueQty": "100"
                * "quantity": "100"
            * },
            * {
                * "id": 2,
                * "product_id": "2",
                * "unit_id": "1",
                * "rcvQuantity": "150"
                * "issueQty": "150"
                * "quantity": "0"
            * }
        * ]
     * }
     */
    public function warehouseStockList(Request $request)
    {
        $credentials = $request->only('warehouse_id');
        $validator = Validator::make($credentials, [
            'warehouse_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message'=> implode(", " , $validator->messages()->all()), 'code' => 401, 'error_type' => 'Validation Error'], 401);
        }

        $sql = WarehouseReceive::with('product')->select('warehouse_receives.product_id', DB::raw('SUM(warehouse_receives.actual_quantity) AS rcvQuantity'), DB::raw('IFNULL(B.issueQty, 0) AS issueQty'), DB::raw('(SUM(warehouse_receives.actual_quantity) - IFNULL(B.issueQty, 0)) AS quantity'))
        ->leftJoin(DB::raw("(SELECT warehouse_id, product_id, SUM(actual_quantity) AS issueQty FROM warehouse_issues GROUP BY warehouse_id, product_id) AS B"), function($q) {
            $q->on( 'warehouse_receives.warehouse_id', '=', 'B.warehouse_id');
            $q->on( 'warehouse_receives.product_id', '=', 'B.product_id');
        })
        ->where('warehouse_receives.warehouse_id', $request->warehouse_id)
        ->groupBy('warehouse_receives.product_id');

        if ($request->product_id > 0) {
            $sql->where('warehouse_receives.product_id', $request->product_id);
        }

        $selectedUnit = null;
        if ($request->unit_id > 0) {
            $selectedUnit = Unit::find($request->unit);
        }

        $stocks = $sql->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Warehouse stock list Provided.',
            'data'      => $stocks,
        ], 200);
    }

    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": "Warehouse receivable Provided.",
        * "data": [
            * "stockins": [
                * {
                    * "id": 1,
                    * "supplier_id": 1,
                    * "date": "2021-07-28",
                    * "stock_id": 1,
                    * "product_id": 1,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "10",
                    * "receivable_quantity": "90"
                * },
                * {
                    * "id": 2,
                    * "supplier_id": 1,
                    * "date": "2021-07-28",
                    * "stock_id": 2,
                    * "product_id": 1,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "10",
                    * "receivable_quantity": "90"
                * }
            * ],
            * "sale_returns": [
                * {
                    * "id": 1,
                    * "customer_id": 1,
                    * "date": "2021-07-28",
                    * "sale_return_id": 1,
                    * "product_id": 1,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "10",
                    * "receivable_quantity": "90"
                * },
                * {
                    * "id": 2,
                    * "customer_id": 1,
                    * "date": "2021-07-28",
                    * "sale_return_id": 2,
                    * "product_id": 1,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "10",
                    * "receivable_quantity": "90"
                * }
            * ],
            * "warehouse_transfers": [
                * {
                    * "warehouse_transfer_id": 1,
                    * "date": "2021-07-28",
                    * "from_warehouse_id": 1,
                    * "to_warehouse_id": 1,
                    * "product_id": 1,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "0",
                    * "receivable_quantity": "100"
                * },
                * {
                    * "warehouse_transfer_id": 2,
                    * "date": "2021-07-28",
                    * "from_warehouse_id": 1,
                    * "to_warehouse_id": 1,
                    * "product_id": 2,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "0",
                    * "receivable_quantity": "100"
                * }
            * ]
        * ]
     * }
     */
    public function warehouseReceivable()
    {
        $stockins = StockItem::select('stocks.supplier_id', 'stocks.date', 'stock_items.id', 'stock_items.stock_id', 'stock_items.product_id', 'stock_items.unit_id', 'stock_items.quantity', DB::raw('IFNULL(A.received_quantity, 0) AS received_quantity'), DB::raw('(stock_items.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
        ->join('stocks', 'stock_items.stock_id', '=', 'stocks.id')
        ->leftJoin(DB::raw("(SELECT stock_item_id, SUM(quantity) AS received_quantity FROM warehouse_receives GROUP BY stock_item_id) AS A"), function($q) {
            $q->on('stock_items.id', '=', 'A.stock_item_id');
        })
        ->having('receivable_quantity', '>', 0)
        ->get();

        $sale_returns = SaleReturnItem::select('sale_returns.customer_id', 'sale_returns.date', 'sale_return_items.id', 'sale_return_items.sale_return_id', 'sale_return_items.product_id', 'sale_return_items.unit_id', 'sale_return_items.quantity', DB::raw('IFNULL(A.received_quantity, 0) AS received_quantity'), DB::raw('(sale_return_items.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
        ->join('sale_returns', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')
        ->leftJoin(DB::raw("(SELECT sale_return_item_id, SUM(quantity) AS received_quantity FROM warehouse_receives GROUP BY sale_return_item_id) AS A"), function($q) {
            $q->on('sale_return_items.id', '=', 'A.sale_return_item_id');
        })
        ->having('receivable_quantity', '>', 0)
        ->get();

        $warehouse_transfers = WarehouseTransfer::select('warehouse_transfers.id', 'warehouse_transfers.date', 'warehouse_transfers.from_warehouse_id', 'warehouse_transfers.to_warehouse_id', 'warehouse_transfers.product_id', 'warehouse_transfers.unit_id', 'warehouse_transfers.quantity', DB::raw('IFNULL(A.received_quantity, 0) AS received_quantity'), DB::raw('(warehouse_transfers.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
        ->join('warehouse_issues', 'warehouse_issues.warehouse_transfer_id', '=', 'warehouse_transfers.id')
        ->leftJoin(DB::raw("(SELECT warehouse_transfer_id, SUM(quantity) AS received_quantity FROM warehouse_receives GROUP BY warehouse_transfer_id) AS A"), 'warehouse_transfers.id', '=', 'A.warehouse_transfer_id')
        ->having('receivable_quantity', '>', 0)
        ->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Warehouse receivable Provided.',
            'data'      => ['stockins' => $stockins, 'sale_returns' => $sale_returns, 'warehouse_transfers' => $warehouse_transfers],
        ], 200);
    }

    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": "Warehouse issuable Provided.",
        * "data": [
            * "stockins": [
                * {
                    * "id": 1,
                    * "supplier_id": 1,
                    * "date": "2021-07-28",
                    * "stock_id": 1,
                    * "product_id": 1,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "10",
                    * "receivable_quantity": "90"
                * },
                * {
                    * "id": 2,
                    * "supplier_id": 1,
                    * "date": "2021-07-28",
                    * "stock_id": 2,
                    * "product_id": 1,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "10",
                    * "receivable_quantity": "90"
                * }
            * ],
            * "sale_returns": [
                * {
                    * "id": 1,
                    * "customer_id": 1,
                    * "date": "2021-07-28",
                    * "sale_return_id": 1,
                    * "product_id": 1,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "10",
                    * "receivable_quantity": "90"
                * },
                * {
                    * "id": 2,
                    * "customer_id": 1,
                    * "date": "2021-07-28",
                    * "sale_return_id": 2,
                    * "product_id": 1,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "10",
                    * "receivable_quantity": "90"
                * }
            * ],
            * "warehouse_transfers": [
                * {
                    * "warehouse_transfer_id": 1,
                    * "date": "2021-07-28",
                    * "from_warehouse_id": 1,
                    * "to_warehouse_id": 1,
                    * "product_id": 1,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "0",
                    * "receivable_quantity": "100"
                * },
                * {
                    * "warehouse_transfer_id": 2,
                    * "date": "2021-07-28",
                    * "from_warehouse_id": 1,
                    * "to_warehouse_id": 1,
                    * "product_id": 2,
                    * "unit_id": 1,
                    * "quantity": "100",
                    * "received_quantity": "0",
                    * "receivable_quantity": "100"
                * }
            * ]
        * ]
     * }
     */
    public function warehouseIssuable()
    {
        $sales = SaleItem::select('sales.customer_id', 'sales.date', 'sale_items.id', 'sale_items.sale_id', 'sale_items.product_id', 'sale_items.unit_id', 'sale_items.quantity', DB::raw('IFNULL(A.received_quantity, 0) AS received_quantity'), DB::raw('(sale_items.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->leftJoin(DB::raw("(SELECT sale_item_id, SUM(quantity) AS received_quantity FROM warehouse_issues GROUP BY sale_item_id) AS A"), function($q) {
            $q->on('sale_items.id', '=', 'A.sale_item_id');
        })
        ->having('receivable_quantity', '>', 0)
        ->get();

        $stock_returns = StockReturnItem::select('stock_returns.supplier_id', 'stock_returns.date', 'stock_return_items.id', 'stock_return_items.stock_return_id', 'stock_return_items.product_id', 'stock_return_items.unit_id', 'stock_return_items.quantity', DB::raw('IFNULL(A.received_quantity, 0) AS received_quantity'), DB::raw('(stock_return_items.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
        ->join('stock_returns', 'stock_return_items.stock_return_id', '=', 'stock_returns.id')
        ->leftJoin(DB::raw("(SELECT stock_return_item_id, SUM(quantity) AS received_quantity FROM warehouse_issues GROUP BY stock_return_item_id) AS A"), function($q) {
            $q->on('stock_return_items.id', '=', 'A.stock_return_item_id');
        })
        ->having('receivable_quantity', '>', 0)
        ->get();

        $warehouse_transfers = WarehouseTransfer::select('warehouse_transfers.id', 'warehouse_transfers.date', 'warehouse_transfers.from_warehouse_id', 'warehouse_transfers.to_warehouse_id', 'warehouse_transfers.product_id', 'warehouse_transfers.unit_id', 'warehouse_transfers.quantity', DB::raw('IFNULL(A.received_quantity, 0) AS received_quantity'), DB::raw('(warehouse_transfers.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
        ->leftJoin(DB::raw("(SELECT warehouse_transfer_id, SUM(quantity) AS received_quantity FROM warehouse_issues GROUP BY warehouse_transfer_id) AS A"), 'warehouse_transfers.id', '=', 'A.warehouse_transfer_id')
        ->having('receivable_quantity', '>', 0)
        ->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Warehouse issuable Provided.',
            'data'      => ['sales' => $sales, 'stock_returns' => $stock_returns, 'warehouse_transfers' => $warehouse_transfers],
        ], 200);
    }

    /**
     * @authenticated
     * @bodyParam type string required Example: ['Supplier', 'Customer', 'Transfer']
     * @bodyParam type_id integer required The ID of the type Example: 1
     * @bodyParam type_item_id integer required The ID of the item Example: 1
     * @bodyParam warehouse_id integer required The ID of the warehouse Example: 1
     * @bodyParam product_id integer required The ID of the product Example: 1
     * @bodyParam unit_id integer required The ID of the product Example: 1
     * @bodyParam quantity integer required The quantity Example: 100
     * @response {
        * "status": true,
        * "message": "Warehouse received successfully",
        * "data": {
            * "type": "Supplier",
            * "date": "2021-07-28",
            * "warehouse_id": 1,
            * "warehouse_transfer_id": null,
            * "stock_id": 1,
            * "stock_item_id": 1,
            * "sale_return_id": null,
            * "sale_return_item_id": null,
            * "supplier_id": 1,
            * "customer_id": null,
            * "product_id": 1,
            * "unit_id": 1,
            * "quantity": "100"
        * }
     * }
     */
    public function warehouseReceiveStore(Request $request)
    {
        $credentials = $request->only('type', 'type_id', 'type_item_id', 'warehouse_id', 'product_id', 'unit_id', 'quantity');
        $validator = Validator::make($credentials, [
            'type' => 'required|in:Supplier,Customer,Transfer',
            'type_id' => 'required|integer',
            'type_item_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'product_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'quantity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message'=> implode(", " , $validator->messages()->all()), 'code' => 401, 'error_type' => 'Validation Error'], 401);
        }

        if ($request->type == 'Supplier') {
            $stocks = StockItem::select(DB::raw('(stock_items.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
            ->join('stocks', 'stock_items.stock_id', '=', 'stocks.id')
            ->leftJoin(DB::raw("(SELECT stock_item_id, SUM(quantity) AS received_quantity FROM warehouse_receives GROUP BY stock_item_id) AS A"), function($q) {
                $q->on('stock_items.id', '=', 'A.stock_item_id');
            })
            ->where('stock_items.id', $request->type_item_id)
            ->first();
        } elseif ($request->type == 'Customer') {
            $stocks = SaleReturnItem::select(DB::raw('IFNULL(A.received_quantity, 0) AS received_quantity'), DB::raw('(sale_return_items.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
            ->join('sale_returns', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')
            ->leftJoin(DB::raw("(SELECT sale_return_item_id, SUM(quantity) AS received_quantity FROM warehouse_receives GROUP BY sale_return_item_id) AS A"), function($q) {
                $q->on('sale_return_items.id', '=', 'A.sale_return_item_id');
            })
            ->where('sale_return_items.id', $request->type_item_id)
            ->first();
        } else {
            $stocks = WarehouseTransfer::select(DB::raw('(warehouse_transfers.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
            ->join('warehouse_issues', 'warehouse_issues.warehouse_transfer_id', '=', 'warehouse_transfers.id')
            ->leftJoin(DB::raw("(SELECT warehouse_transfer_id, SUM(quantity) AS received_quantity FROM warehouse_receives GROUP BY warehouse_transfer_id) AS A"), 'warehouse_transfers.id', '=', 'A.warehouse_transfer_id')
            ->where('warehouse_transfers.id', $request->type_id)
            ->first();
        }

        if ($stocks->receivable_quantity < $request->quantity) {
            return response()->json([
                'status'    => false,
                'message'   => 'Stock not found! Current stock is : '.$stocks->receivable_quantity,
                'data'      => null,
            ], 200);
        }

        $supplierId = null;
        if ($request->type == 'Supplier') {
            $supplierId = Stock::find($request->type_id)->supplier_id;
        }

        $customerId = null;
        if ($request->type == 'Customer') {
            $customerId = SaleReturn::find($request->type_id)->customer_id;
        }
        
        $unit = Unit::find($request->unit_id);

        $receive = WarehouseReceive::create([
            'type' => $request->type, 
            'date' => date('Y-m-d'), 
            'warehouse_id' => $request->warehouse_id, 
            'warehouse_transfer_id' => $request->type == 'Transfer' ? $request->type_id : null, 
            'stock_id' => $request->type == 'Supplier' ? $request->type_id : null,  
            'stock_item_id' => $request->type == 'Supplier' ? $request->type_item_id : null,  
            'sale_return_id' => $request->type == 'Customer' ? $request->type_id : null, 
            'sale_return_item_id' => $request->type == 'Customer' ? $request->type_item_id : null, 
            'supplier_id' => $supplierId, 
            'customer_id' => $customerId, 
            'product_id' => $request->product_id, 
            'unit_id' => $request->unit_id, 
            'unit_quantity' => $unit->quantity,
            'quantity' => $request->quantity,
            'actual_quantity' => ($request->quantity * $unit->quantity),
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Warehouse received successfully.',
            'data'      => $receive,
        ], 200);
    }
    
    /**
     * @authenticated
     * @bodyParam type string required Example: ['Supplier', 'Customer', 'Transfer']
     * @bodyParam type_id integer required The ID of the type Example: 1
     * @bodyParam type_item_id integer required The ID of the item Example: 1
     * @bodyParam warehouse_id integer required The ID of the warehouse Example: 1
     * @bodyParam product_id integer required The ID of the product Example: 1
     * @bodyParam unit_id integer required The ID of the product Example: 1
     * @bodyParam quantity integer required The quantity Example: 100
     * @response {
        * "status": true,
        * "message": "Warehouse Issued successfully",
        * "data": {
            * "type": "Supplier",
            * "date": "2021-07-28",
            * "warehouse_id": 1,
            * "warehouse_transfer_id": null,
            * "stock_return_id": 1,
            * "stock_return_item_id": 1,
            * "sale_id": null,
            * "sale_item_id": null,
            * "supplier_id": 1,
            * "customer_id": null,
            * "product_id": 1,
            * "unit_id": 1,
            * "quantity": "100"
        * }
     * }
     */
    public function warehouseIssueStore(Request $request)
    {
        $credentials = $request->only('type', 'type_id', 'type_item_id', 'warehouse_id', 'product_id', 'unit_id', 'quantity');
        $validator = Validator::make($credentials, [
            'type' => 'required|in:Supplier,Customer,Transfer',
            'type_id' => 'required|integer',
            'type_item_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'product_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'quantity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message'=> implode(", " , $validator->messages()->all()), 'code' => 401, 'error_type' => 'Validation Error'], 401);
        }

        if ($request->type == 'Customer') {
            $stocks = SaleItem::select('sales.customer_id', 'sales.date', 'sale_items.id', 'sale_items.sale_id', 'sale_items.product_id', 'sale_items.unit_id', 'sale_items.quantity', DB::raw('IFNULL(A.received_quantity, 0) AS received_quantity'), DB::raw('(sale_items.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin(DB::raw("(SELECT sale_item_id, SUM(quantity) AS received_quantity FROM warehouse_issues GROUP BY sale_item_id) AS A"), function($q) {
                $q->on('sale_items.id', '=', 'A.sale_item_id');
            })
            ->where('sale_items.id', $request->type_item_id)
            ->first();
        } elseif ($request->type == 'Supplier') {
            $stocks = StockReturnItem::select(DB::raw('(stock_return_items.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
            ->join('stock_returns', 'stock_return_items.stock_return_id', '=', 'stock_returns.id')
            ->leftJoin(DB::raw("(SELECT stock_return_item_id, SUM(quantity) AS received_quantity FROM warehouse_issues GROUP BY stock_return_item_id) AS A"), function($q) {
                $q->on('stock_return_items.id', '=', 'A.stock_return_item_id');
            })
            ->where('stock_return_items.id', $request->type_item_id)
            ->first();
        } else {
            $stocks = WarehouseTransfer::select('warehouse_transfers.id', 'warehouse_transfers.date', 'warehouse_transfers.from_warehouse_id', 'warehouse_transfers.to_warehouse_id', 'warehouse_transfers.product_id', 'warehouse_transfers.unit_id', 'warehouse_transfers.quantity', DB::raw('IFNULL(A.received_quantity, 0) AS received_quantity'), DB::raw('(warehouse_transfers.quantity - IFNULL(A.received_quantity, 0)) AS receivable_quantity'))
            ->leftJoin(DB::raw("(SELECT warehouse_transfer_id, SUM(quantity) AS received_quantity FROM warehouse_issues GROUP BY warehouse_transfer_id) AS A"), 'warehouse_transfers.id', '=', 'A.warehouse_transfer_id')
            ->where('warehouse_transfers.id', $request->type_id)
            ->first();
        }

        if ($stocks->receivable_quantity < $request->quantity) {
            return response()->json([
                'status'    => false,
                'message'   => 'Stock not found! Current stock is : '.$stocks->receivable_quantity,
                'data'      => null,
            ], 200);
        }

        $supplierId = null;
        if ($request->type == 'Supplier') {
            $supplierId = StockReturn::find($request->type_id)->supplier_id;
        }

        $customerId = null;
        if ($request->type == 'Customer') {
            $customerId = Sale::find($request->type_id)->customer_id;
        }
        
        $unit = Unit::find($request->unit_id);

        $receive = WarehouseIssue::create([
            'type' => $request->type, 
            'date' => date('Y-m-d'), 
            'warehouse_id' => $request->warehouse_id, 
            'warehouse_transfer_id' => $request->type == 'Transfer' ? $request->type_id : null, 
            'stock_return_id' => $request->type == 'Supplier' ? $request->type_id : null,  
            'stock_return_item_id' => $request->type == 'Supplier' ? $request->type_item_id : null,  
            'sale_id' => $request->type == 'Customer' ? $request->type_id : null, 
            'sale_item_id' => $request->type == 'Customer' ? $request->type_item_id : null, 
            'supplier_id' => $supplierId, 
            'customer_id' => $customerId, 
            'product_id' => $request->product_id, 
            'unit_id' => $request->unit_id, 
            'unit_quantity' => $unit->quantity,
            'quantity' => $request->quantity,
            'actual_quantity' => ($request->quantity * $unit->quantity),
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Warehouse Issued successfully.',
            'data'      => $receive,
        ], 200);
    }

    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": null,
        * "data": [
            * {
                * "type": "Supplier",
                * "date": "2021-07-28",
                * "warehouse_id": 1,
                * "warehouse_transfer_id": null,
                * "stock_id": 1,
                * "sale_return_id": null,
                * "supplier_id": 1,
                * "customer_id": null,
                * "product_id": 1,
                * "unit_id": 1,
                * "quantity": "100"
            * },
            * {
                * "type": "Customer",
                * "date": "2021-07-28",
                * "warehouse_id": 1,
                * "warehouse_transfer_id": null,
                * "stock_id": null,
                * "sale_return_id": 1,
                * "supplier_id": null,
                * "customer_id": 2,
                * "product_id": 1,
                * "unit_id": 1,
                * "quantity": "100"
            * }
        * ]
     * }
     */
    public function warehouseReceive()
    {
        $receives = WarehouseReceive::get();

        return response()->json([
            'status'    => true,
            'message'   => null,
            'data'      => $receives,
        ], 200);
    }

    /**
     * @authenticated
     * @response {
        * "status": true,
        * "message": null,
        * "data": [
            * {
                * "type": "Supplier",
                * "date": "2021-07-28",
                * "warehouse_id": 1,
                * "warehouse_transfer_id": null,
                * "stock_return_id": 1,
                * "sale_id": null,
                * "supplier_id": 1,
                * "customer_id": null,
                * "product_id": 1,
                * "unit_id": 1,
                * "quantity": "100"
            * },
            * {
                * "type": "Customer",
                * "date": "2021-07-28",
                * "warehouse_id": 1,
                * "warehouse_transfer_id": null,
                * "stock_return_id": null,
                * "sale_id": 1,
                * "supplier_id": null,
                * "customer_id": 2,
                * "product_id": 1,
                * "unit_id": 1,
                * "quantity": "100"
            * }
        * ]
     * }
     */
    public function warehouseIssue()
    {
        $issues = WarehouseIssue::get();

        return response()->json([
            'status'    => true,
            'message'   => null,
            'data'      => $issues,
        ], 200);
    }
}
