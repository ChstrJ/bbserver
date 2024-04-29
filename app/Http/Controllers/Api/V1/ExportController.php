<?php

namespace App\Http\Controllers\Api\V1;
use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\transaction\TransactionService;
use App\Http\Utils\ResponseHelper;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    use ResponseHelper;
    public function exportSales(Request $request) {

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Transaction::query();

        if($startDate && $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        }

        $transactions = $query->get();

        $filename = TransactionService::generateFilename();

        //--for testing
        return Excel::download(new SalesExport($transactions), $filename);

        // $excelFile = Excel::download(new SalesExport($transactions), $filename)->getFile();

        // $fileContent = base64_encode(file_get_contents($excelFile));

        // return response()->json(['file_name' => $filename, 'excel_file' => $fileContent]);
    }

    public function exportSaless(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $customerName = $request->input('customer');
        $employeeName = $request->input('employee');
        $searchByRefNo = $request->input('search_by_ref');
        $sortByDesc = $request->input('sort_by_desc');
        $sortByAsc = $request->input('sort_by_asc');
        $status = $request->input('status');
        $paymentMethod = $request->input('payment_method');

        $query = Transaction::query()
            ->select('transactions.*')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'transactions.user_id', '=', 'users.id')
            ->whereNot('transactions.is_removed', TransactionStatus::$REMOVE)
            ->orderBy('transactions.status')
            ->with('user', 'customer');

        if ($startDate && $endDate) {
            $query->whereDate('transactions.created_at', '>=', $startDate)
                ->whereDate('transactions.created_at', '<=', $endDate);
        }

        if($status) {
            $query->where("transactions.status", $status);
        }

        if($paymentMethod) {
            $query->where("transactions.payment_method", $paymentMethod);
        }

        if ($sortByDesc) {
            $query->orderBy("transactions.$sortByDesc", 'DESC');
        }

        if ($sortByAsc) {
            $query->orderBy("transactions.$sortByAsc", 'ASC');
        }
        
        if ($searchByRefNo) {
            $query->where('transactions.reference_number', 'LIKE', "%$searchByRefNo%");
        }

        //filtering by customer fullname
        if ($customerName) {
            $query->where('customers.full_name', 'LIKE', "%$customerName%");
        }

        //filtering by user fullname
        if ($employeeName) {
            $query->where('users.full_name', 'LIKE', "%$employeeName%");
        }

        $transactions = $query->get();

        $filename = TransactionService::generateFilename();

        //--for testing
        return Excel::download(new SalesExport($transactions), $filename);


    }
}
