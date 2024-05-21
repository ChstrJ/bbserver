<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\customer\CustomerStatus;
use App\Http\Helpers\product\ProductStatus;
use App\Http\Helpers\summary\SummaryService;
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

    public function getAllSummary(Request $request)
    {
        $today = UserService::getDate();
        $products = Product::whereNot('is_removed', ProductStatus::$REMOVE)->count();
        $customers = Customer::whereNot('is_active', CustomerStatus::$NOT_ACTIVE)->count(); 
        $employees = SummaryService::getEmployeeSummary();
        $orders = SummaryService::getOrderSummary($today);

        return $this->json([
            "sales" => [
                "overall" => $orders->overall_sales,
                "today" => $orders->today_sales,
            ],
            "counts" => [
                "products" => $products,
                "orders" => [
                    "pending" => $orders->pending_count,
                    "approved" => $orders->approved_count,
                    "rejected" => $orders->rejected_count,
                ],
                "customers" => $customers,
                "employees" => [
                    "all" => $employees->all_users,
                    "admin" => $employees->admin,
                    "employee" => $employees->employee,
                ],
            ],
        ]);
    }

    public function criticalStocks() 
    {
        $criticalStocks = Product::where('quantity', '<=', '50')
                    ->whereNot('is_removed', ProductStatus::$REMOVE)->limit(15)->get();

        return $this->json($criticalStocks);
    }

    public function chartSales(Request $request)
    {
        $interval = $request->input('interval');

        $interval = $interval ?: 'weekly';
        $sales = TransactionService::getLogScaleData($interval);
        
        return $this->json($sales);
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
        return response('', 200);
    }
}
