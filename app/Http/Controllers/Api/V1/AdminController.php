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
        $employee = User::where('role_id', Roles::$EMPLOYEE)->count();

        $sales = Transaction::where('status', TransactionStatus::$APPROVE)->sum('amount_due');
        $reject = Transaction::where('status', TransactionStatus::$REJECT)->sum('amount_due');
        $pending = Transaction::where('status', TransactionStatus::$PENDING)->sum('amount_due');
        $commission = Transaction::where('status', TransactionStatus::$APPROVE)->sum('commission');

        $sales_count = Transaction::where('status', TransactionStatus::$APPROVE)->count();
        $reject_count = Transaction::where('status', TransactionStatus::$REJECT)->count();
        $pending_count = Transaction::where('status', TransactionStatus::$PENDING)->count();

        $today_sales = Transaction::where('status', TransactionStatus::$APPROVE)
            ->whereDate('created_at', $today)
            ->sum('amount_due');

        return response()->json([
            "total_products" => $products,
            "total_customers" => $customer,
            "total_employees" => $employee,
            "transaction_counts" => [
                "sales_count" => $sales_count,
                "pending_count" => $pending_count,
                "reject_count" => $reject_count,
            ],
            "transactions_total" => [
                "today_sales" => floatval($today_sales),
                "total_commission" => floatval($commission),
                "total_sales" => floatval($sales),
                "total_pending" => floatval($pending),
                "total_rejected" => floatval($reject),
            ],
        ]);
    }


    public function chartSales(Request $request)
    {
        $interval = $request->query('interval');
        return $this->json([
            $interval . '_sales' => TransactionService::getChartSalesData($interval)
        ]);
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
