<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\customer\CustomerStatus;
use App\Http\Helpers\product\ProductStatus;
use App\Http\Helpers\transaction\TransactionService;
use App\Http\Helpers\transaction\TransactionStatus;
use App\Http\Helpers\user\UserService;
use App\Http\Helpers\user\UserStatus;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Utils\Response;
use App\Http\Utils\ResponseHelper;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ResponseHelper;
    public function getAllTotal()
    {
        $today = UserService::getDate();

        $products = Product::whereNot('is_removed', ProductStatus::$REMOVE)->count();
        $customer = Customer::whereNot('is_active', CustomerStatus::$NOT_ACTIVE)->count();
        $employee = User::whereNot('is_active', UserStatus::$NOT_ACTIVE)->count();

        $totals = Transaction::selectRaw("
        COUNT(CASE WHEN status = 'approved' THEN status ELSE null END) AS sales_count,
        COUNT(CASE WHEN status = 'rejected' THEN status ELSE null END) AS reject_count,
        COUNT(status) AS pending_count,
        TRUNCATE(SUM(CASE WHEN status = 'approved' THEN amount_due ELSE 0 END), 2) AS overall_sales,
        TRUNCATE(SUM(CASE WHEN status = 'rejected' THEN amount_due ELSE 0 END), 2) AS total_rejected,
        TRUNCATE(SUM(CASE WHEN status = 'pending' THEN amount_due ELSE 0 END), 2) AS total_pending,
        TRUNCATE(SUM(CASE WHEN status = 'approved' THEN commission ELSE 0 END), 2) AS total_commission,
        TRUNCATE(SUM(CASE WHEN DATE(created_at) = '".$today ."' THEN amount_due ELSE 0 END), 2) AS today_sales
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
            "total_transactions" => [
                "today_sales" => $totals->today_sales,
                "total_commission" => $totals->total_commission,
                "overall_sales" => $totals->overall_sales,
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
            return Response::notFound();
        }

        if ($transaction->status === TransactionStatus::$APPROVE) {
            return Response::alreadyChanged();
        }

        $data = $transaction->checkouts;
        TransactionService::decrementQty($data);

        $transaction->status = TransactionStatus::$APPROVE;
        $transaction->save();

        return Response::approve();
    }

    public function reject(Transaction $transaction, int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return Response::notFound();
        }
        if ($transaction->status === TransactionStatus::$REJECT) {
            return Response::alreadyChanged();
        }
        $transaction->status = TransactionStatus::$REJECT;
        $transaction->save();

        return Response::reject();
    }

    public function createAdmin(StoreRegisterRequest $request) 
    {
        $data = $request->validated();
        User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'role_id' => 1,
        ]);
        return response('',200);
    }
}
