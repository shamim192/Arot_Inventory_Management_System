<?php

namespace App\Http\Controllers\Admin\Report;

use App\Models\Bank;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CommonController extends Controller
{
    public function bank(Request $request)
    {
        $sql = Bank::select('banks.*', DB::raw('IFNULL(A.inAmount, 0) AS inAmount'), DB::raw('IFNULL(B.outAmount, 0) AS outAmount'), DB::raw('(IFNULL(A.inAmount, 0) - IFNULL(B.outAmount, 0)) AS balanceAmount'))->orderBy('name', 'ASC');
        $sql->leftJoin(DB::raw("(SELECT bank_id, SUM(amount) AS inAmount FROM `transactions` WHERE type='In' AND deleted_at IS NULL GROUP BY bank_id) AS A"), function($q) {
            $q->on('A.bank_id', '=', 'banks.id');
        });
        $sql->leftJoin(DB::raw("(SELECT bank_id, SUM(amount) AS outAmount FROM `transactions` WHERE type='Out' AND deleted_at IS NULL GROUP BY bank_id) AS B"), function($q) {
            $q->on('B.bank_id', '=', 'banks.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('branch', 'LIKE', $request->q.'%')
                ->orWhere('account_number', 'LIKE', $request->q.'%');
            });
        }

        $reports = $sql->paginate($request->limit ?? 15);

        return view('admin.report.bank', compact('reports'));
    }

    public function bankTransactions(Request $request)
    {
        $banks = Bank::where('status', 'Active')->get();

        if ($request->bank == null) {
            return view('admin.report.bank-transactions', compact('banks'));
        }

        $data = Bank::find($request->bank);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('report.bank', qArray());
        }

        $sql = Transaction::with('flagable')->orderBy('datetime', 'ASC')->where('bank_id', $data->id);

        if ($request->from) {
            $sql->whereDate('datetime', '>=', dbDateFormat($request->from));
        }
        if ($request->to) {
            $sql->whereDate('datetime', '<=', dbDateFormat($request->to));
        }

        $reports = $sql->get();

        return view('admin.report.bank-transactions', compact('data', 'reports', 'banks'));
    }



    // Expense report
  public function expenseReport(Request $request){

    $banks = Bank::where('status', 'Active')->get();


    $sql = ExpenseCategory::select('expense_categories.name as ExpenseName', 'expense_categories.id as Expense_id', 'E.ExpenseAmount','E.category_id','E.bank_id','E.ExpenseCode','E.ExpenseDate','D.BankName')
    ->orderBy('name', 'ASC');
    
    $sql->leftJoin(DB::raw("(SELECT category_id, date as ExpenseDate, expense_number as ExpenseCode, bank_id, SUM(amount) AS ExpenseAmount FROM `expenses` WHERE deleted_at IS NULL GROUP BY category_id) AS E"), function($q) {
    $q->on('E.category_id', '=', 'expense_categories.id');
});



$sql->leftJoin(DB::raw("(SELECT name as BankName, id FROM `banks`) AS D"), function($q) {
    $q->on('D.id', '=', 'E.bank_id');
});
$sql->whereExists(function ($query) {
    $query->select(DB::raw(1))
        ->from('expenses')
        ->whereRaw('expenses.category_id = expense_categories.id');
});


if ($request->from) {
    $sql->whereDate('ExpenseDate', '>=', dbDateFormat($request->from));
}
if ($request->to) {
    $sql->whereDate('ExpenseDate', '<=', dbDateFormat($request->to));
}
$reports = $sql->paginate($request->limit ?? 15);
   

 return view('admin.report.expense', compact('reports','banks'));

  }
    //end of expense report


    // start of expense transaction 

    public function expenseTransaction(Request $request)
    {
        $categoryId = $request->input('category');
        $categoryName = ExpenseCategory::where('id', $categoryId)->value('name');
    
        $sql = ExpenseCategory::select(
            'expense_categories.name as ExpenseName',
            'expense_categories.id as Expense_id',
            'E.ExpenseAmount',
            'E.category_id',
            'E.bank_id',
            'E.ExpenseCode',
            'E.ExpenseDate',
            'D.BankName'
        )
        ->orderBy('name', 'ASC')
        ->leftJoin(
            DB::raw("
                (SELECT category_id, date as ExpenseDate, expense_number as ExpenseCode, bank_id, amount AS ExpenseAmount 
                 FROM `expenses` 
                 WHERE deleted_at IS NULL AND category_id = $categoryId) AS E"
            ),
            function($q) {
                $q->on('E.category_id', '=', 'expense_categories.id');
            }
        )
        ->leftJoin(
            DB::raw("(SELECT name as BankName, id FROM `banks`) AS D"),
            function($q) {
                $q->on('D.id', '=', 'E.bank_id');
            }
        );
   
        
         $reports = $sql->get();

        return view('admin.report.expense-transactions', compact('reports','categoryName'));
    }
    

    //end of expense transaction

     // start of income report

    public function incomeReport(Request $request){

        $banks = Bank::where('status', 'Active')->get();
    
    
        $sql = IncomeCategory::select('income_categories.name as IncomeName', 'income_categories.id as Income_id', 'E.IncomeAmount','E.category_id','E.bank_id','E.IncomeCode','E.IncomeDate','D.BankName')
        ->orderBy('name', 'ASC');
        
    $sql->leftJoin(DB::raw("(SELECT category_id, date as IncomeDate, income_number as IncomeCode, bank_id, SUM(amount) AS IncomeAmount FROM `incomes` WHERE deleted_at IS NULL GROUP BY category_id) AS E"), function($q) {
        $q->on('E.category_id', '=', 'income_categories.id');
    });
    
    
    
    $sql->leftJoin(DB::raw("(SELECT name as BankName, id FROM `banks`) AS D"), function($q) {
        $q->on('D.id', '=', 'E.bank_id');
    });
    
    
    $sql->whereExists(function ($query) {
        $query->select(DB::raw(1))
            ->from('incomes')
            ->whereRaw('incomes.category_id = income_categories.id');
    });

    
    if ($request->from) {
        $sql->whereDate('date', '>=', dbDateFormat($request->from));
    }
    if ($request->to) {
        $sql->whereDate('date', '<=', dbDateFormat($request->to));
    }
    

    $reports = $sql->paginate($request->limit ?? 15);
       
    
     return view('admin.report.income', compact('reports','banks'));
    
      }

   



    // end of income report


  // start of income transaction 

  public function incomeTransaction(Request $request){
    $categoryId = $request->input('category');
    $categoryName = IncomeCategory::where('id', $categoryId)->value('name');

    $sql = IncomeCategory::select(
        'income_categories.name as IncomeName',
        'income_categories.id as Income_id',
        'E.IncomeAmount',
        'E.category_id',
        'E.bank_id',
        'E.IncomeCode',
        'E.IncomeDate',
        'D.BankName'
    )
    ->orderBy('name', 'ASC')
    ->leftJoin(
        DB::raw("
            (SELECT category_id, date as IncomeDate, income_number as IncomeCode, bank_id, amount AS IncomeAmount 
             FROM `incomes` 
             WHERE deleted_at IS NULL AND category_id = $categoryId) AS E"
        ),
        function($q) {
            $q->on('E.category_id', '=', 'income_categories.id');
        }
    )
    ->leftJoin(
        DB::raw("(SELECT name as BankName, id FROM `banks`) AS D"),
        function($q) {
            $q->on('D.id', '=', 'E.bank_id');
        }
    );
    

$reports = $sql->get();

    return view('admin.report.income-transactions', compact('reports','categoryName'));
  }

  //end of income transaction

    
    public function supplier(Request $request)
    {
        $sql = Supplier::select('suppliers.*', DB::raw('IFNULL(A.stockAmount, 0) AS stockAmount'), DB::raw('IFNULL(B.returnAmount, 0) AS returnAmount'), DB::raw('IFNULL(C.inAmount, 0) AS inAmount'), DB::raw('IFNULL(D.outAmount, 0) AS outAmount'), DB::raw('(suppliers.previous_due - (IFNULL(A.stockAmount, 0) - IFNULL(B.returnAmount, 0)) - (IFNULL(D.outAmount, 0) - IFNULL(C.inAmount, 0))) AS dueAmount'))
        ->orderBy('name', 'ASC');
        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS stockAmount FROM `stocks` WHERE deleted_at IS NULL GROUP BY supplier_id) AS A"), function($q) {
            $q->on('A.supplier_id', '=', 'suppliers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT X.supplier_id, SUM(Y.amount) AS returnAmount FROM `stock_returns` AS X INNER JOIN stock_return_items AS Y ON X.id = Y.stock_id WHERE X.deleted_at IS NULL GROUP BY supplier_id) AS B"), function($q) {
            $q->on('B.supplier_id', '=', 'suppliers.id');
        });

        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(amount) AS inAmount FROM `supplier_payments` WHERE type='In' AND deleted_at IS NULL GROUP BY supplier_id) AS C"), function($q) {
            $q->on('C.supplier_id', '=', 'suppliers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT supplier_id, SUM(amount) AS outAmount FROM `supplier_payments` WHERE type='Out' AND deleted_at IS NULL GROUP BY supplier_id) AS D"), function($q) {
            $q->on('D.supplier_id', '=', 'suppliers.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('suppliers.name', 'LIKE', $request->q.'%')
                ->orWhere('suppliers.mobile', 'LIKE', $request->q.'%')
                ->orWhere('suppliers.shop_name', 'LIKE', $request->q.'%');
            });
        }

        $reportsSum = $sql->sum(DB::raw('(suppliers.previous_due - (IFNULL(A.stockAmount, 0) - IFNULL(B.returnAmount, 0)) - (IFNULL(D.outAmount, 0) - IFNULL(C.inAmount, 0)))'));

        $reports = $sql->paginate($request->limit ?? 15);

        return view('admin.report.supplier', compact('reports', 'reportsSum'));
    }

    public function supplierTransactions(Request $request)
    {
        $suppliers = Supplier::where('status', 'Active')->get();

        if ($request->supplier == null) {
            return view('admin.report.supplier-transactions', compact('suppliers'));
        }

        $data = Supplier::find($request->supplier);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('report.supplier', qArray());
        }

        $dateCond = '';
        if ($request->from) {
            $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($request->to)."'";
        }
        if ($request->to) {
            $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($request->to)."'";
        }

        $query1 = "SELECT 'Stock In' AS type, 'stock.show' AS route, id, `date`, total_amount AS amount FROM stocks AS X WHERE supplier_id = $request->supplier $dateCond";
        $query2 = "SELECT 'Stock Return' AS type, 'stock-return.show' AS route, id, X.`date`, Y.amount 
        FROM stock_returns AS X 
        INNER JOIN(
            SELECT stock_id, SUM(amount) AS amount FROM stock_return_items GROUP BY stock_id
        ) AS Y ON X.id = Y.stock_id 
        WHERE X.supplier_id = $request->supplier $dateCond";
        $query3 = "SELECT 'Payment In' AS type, 'supplier-payment.show' AS route, id, `date`, amount FROM supplier_payments AS X WHERE type='In' AND deleted_at IS NULL AND supplier_id = $request->supplier $dateCond";
        $query4 = "SELECT 'Payment Out' AS type, 'supplier-payment.show' AS route, id, `date`, amount FROM supplier_payments AS X WHERE type='Out' AND deleted_at IS NULL AND supplier_id = $request->supplier $dateCond";
        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4) S ORDER BY S.`date` ASC");

        return view('admin.report.supplier-transactions', compact('data', 'reports', 'suppliers'));
    }
    
    public function customer(Request $request)
    {
        $sql = Customer::select('customers.*', DB::raw('IFNULL(A.saleAmount, 0) AS saleAmount'), DB::raw('IFNULL(B.returnAmount, 0) AS returnAmount'), DB::raw('IFNULL(C.inAmount, 0) AS inAmount'), DB::raw('IFNULL(D.outAmount, 0) AS outAmount'), DB::raw('(customers.previous_due + (IFNULL(A.saleAmount, 0) - IFNULL(B.returnAmount, 0)) - (IFNULL(C.inAmount, 0) - IFNULL(D.outAmount, 0))) AS dueAmount'))
        ->orderBy('name', 'ASC');
        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(total_amount) AS saleAmount FROM `sales` WHERE deleted_at IS NULL GROUP BY customer_id) AS A"), function($q) {
            $q->on('A.customer_id', '=', 'customers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT X.customer_id, SUM(Y.amount) AS returnAmount FROM `sale_returns` AS X INNER JOIN sale_return_items AS Y ON X.id = Y.sale_id WHERE X.deleted_at IS NULL GROUP BY customer_id) AS B"), function($q) {
            $q->on('B.customer_id', '=', 'customers.id');
        });

        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS inAmount FROM `customer_payments` WHERE type='In' AND deleted_at IS NULL GROUP BY customer_id) AS C"), function($q) {
            $q->on('C.customer_id', '=', 'customers.id');
        });
        $sql->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS outAmount FROM `customer_payments` WHERE type='Out' AND deleted_at IS NULL GROUP BY customer_id) AS D"), function($q) {
            $q->on('D.customer_id', '=', 'customers.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('customers.name', 'LIKE', $request->q.'%')
                ->orWhere('customers.mobile', 'LIKE', $request->q.'%')
                ->orWhere('customers.shop_name', 'LIKE', $request->q.'%');
            });
        }

        $reportsSum = $sql->sum(DB::raw('(customers.previous_due + (IFNULL(A.saleAmount, 0) - IFNULL(B.returnAmount, 0)) - (IFNULL(C.inAmount, 0) - IFNULL(D.outAmount, 0)))'));

        $reports = $sql->paginate($request->limit ?? 15);

        return view('admin.report.customer', compact('reports', 'reportsSum'));
    }

    public function customerTransactions(Request $request)
    {
        $customers = Customer::where('status', 'Active')->get();

        if ($request->customer == null) {
            return view('admin.report.customer-transactions', compact('customers'));
        }

        $data = Customer::find($request->customer);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('report.customer', qArray());
        }

        $dateCond = '';
        if ($request->from) {
            $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($request->to)."'";
        }
        if ($request->to) {
            $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($request->to)."'";
        }

        $query1 = "SELECT 'Sale' AS type, 'sale.show' AS route, id, `date`, total_amount AS amount FROM sales AS X WHERE customer_id = $request->customer $dateCond";
        $query2 = "SELECT 'Sale Return' AS type, 'sale-return.show' AS route, X.id, X.`date`, Y.amount 
            FROM sale_returns AS X 
            INNER JOIN (
                SELECT sale_id, SUM(amount) AS amount FROM sale_return_items GROUP BY sale_id
            ) AS Y ON X.id = Y.sale_id 
        WHERE X.customer_id = $request->customer $dateCond";
        $query3 = "SELECT 'Payment In' AS type, 'customer-payment.show' AS route, id, `date`, amount FROM customer_payments AS X WHERE type='In' AND deleted_at IS NULL AND customer_id = $request->customer $dateCond";
        $query4 = "SELECT 'Payment Out' AS type, 'customer-payment.show' AS route, id, `date`, amount FROM customer_payments AS X WHERE type='Out' AND deleted_at IS NULL AND customer_id = $request->customer $dateCond";
        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4) S ORDER BY S.`date` ASC");

        return view('admin.report.customer-transactions', compact('data', 'reports', 'customers'));
    }
}
