<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\transaction\TransactionService;
use App\Http\Helpers\transaction\TransactionStatus;
use App\Http\Helpers\user\UserService;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Utils\HttpStatusCode;
use App\Http\Utils\Message;
use App\Http\Utils\ResponseHelper;
use App\Http\Utils\Roles;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use ResponseHelper;
    public function getAllTotal()
    {
        $today = UserService::getDate();

        $products = Product::count();
        $customer = Customer::count();
        $employee = User::count();

        $totals = Transaction::selectRaw("
        COUNT(CASE WHEN status = '".TransactionStatus::$APPROVE."' THEN amount_due ELSE 0 END) AS sales_count,
        COUNT(CASE WHEN status = '".TransactionStatus::$REJECT."' THEN amount_due ELSE 0 END) AS reject_count,
        COUNT(CASE WHEN status = '".TransactionStatus::$PENDING."' THEN amount_due ELSE 0 END) AS pending_count,
        TRUNCATE(SUM(CASE WHEN DATE(created_at) = '".$today ."' THEN amount_due ELSE 0 END), 2) AS today_sales,
        TRUNCATE(SUM(CASE WHEN status = '".TransactionStatus::$APPROVE."' THEN amount_due ELSE 0 END), 2) AS total_sales,
        TRUNCATE(SUM(CASE WHEN status = '".TransactionStatus::$REJECT."' THEN amount_due ELSE 0 END), 2) AS total_rejected,
        TRUNCATE(SUM(CASE WHEN status = '".TransactionStatus::$PENDING."' THEN amount_due ELSE 0 END), 2) AS total_pending,
        TRUNCATE(SUM(CASE WHEN status = '".TransactionStatus::$APPROVE."' THEN commission ELSE 0 END), 2) AS total_commission
        ")->first();

        return response()->json([
            "inventory" => $products,
            "customers" => $customer,
            "employees" => $employee,
            "orders" => $totals->pending_count,

            "transaction_counts" => [
                "sales_count" => $totals->sales_count,
                "pending_count" => $totals->pending_count,
                "reject_count" => $totals->reject_count,
            ],
            "transactions_total" => [
                "today_sales" => $totals->today_sales,
                "total_commission" => $totals->total_commission,
                "total_sales" => $totals->total_sales,
                "total_pending" => $totals->total_pending,
                "total_rejected" => $totals->total_rejected,
            ],
        ]);
    }


    public function chartSales(Request $request)
    {

        $interval = $request->input('interval');

        return $this->json(TransactionService::getLogScaleData($interval));
    }

    public function approve(Transaction $transaction, int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }

        if ($transaction->status === TransactionStatus::$APPROVE) {
            return $this->json(Message::alreadyChanged(), HttpStatusCode::$CONFLICT);
        }

        $data = $transaction->checkouts;
        TransactionService::decrementQty($data);

        $transaction->status = TransactionStatus::$APPROVE;
        $transaction->save();
        return $this->json(Message::approve());
    }

    public function reject(Transaction $transaction, int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        if ($transaction->status === TransactionStatus::$REJECT) {
            return $this->json(Message::alreadyChanged(), HttpStatusCode::$CONFLICT);
        }
        $transaction->status = TransactionStatus::$REJECT;
        $transaction->save();
        return $this->json(Message::reject());
    }
}
