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
    public function getAllSummary(Request $request)
    {
       
        $today = UserService::getDate();
        $products = Product::whereNot('is_removed', ProductStatus::$REMOVE)->count();
        $customers = Customer::whereNot('is_active', CustomerStatus::$NOT_ACTIVE)->count(); 


        $employees = User::selectRaw("
            COUNT(id) AS all_users,
            COUNT(CASE WHEN role_id = 1 THEN role_id ELSE null END) AS admin,
            COUNT(CASE WHEN role_id = 2 THEN role_id ELSE null END) AS employee
        ")->first();

        $orders = Transaction::selectRaw("
            COUNT(CASE WHEN status = 'approved' THEN status ELSE null END) AS approved_count,
            COUNT(CASE WHEN status = 'rejected' THEN status ELSE null END) AS rejected_count,
            COUNT(CASE WHEN status = 'pending' THEN status ELSE null END) AS pending_count,
            TRUNCATE(SUM(CASE WHEN status = 'rejected' THEN amount_due ELSE 0 END), 2) AS total_rejected,
            TRUNCATE(SUM(CASE WHEN status = 'pending' THEN amount_due ELSE 0 END), 2) AS total_pending,
            TRUNCATE(SUM(CASE WHEN status = 'approved' THEN commission ELSE 0 END), 2) AS total_commission,
            TRUNCATE(SUM(CASE WHEN status = 'approved' THEN amount_due ELSE 0 END), 2) AS overall_sales,
            TRUNCATE(SUM(CASE WHEN DATE(created_at) = '" . $today . "' THEN amount_due ELSE 0 END), 2) AS today_sales
        ")->first();

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
                    ->whereNot('is_removed', ProductStatus::$REMOVE)->simplePaginate();

        return $this->json($criticalStocks);
    }

    public function chartSales(Request $request)
    {
        
        $interval = $request->input('interval');

        $sales = [];

        if ($interval) {
            $sales = TransactionService::getLogScaleData($interval);
        } else {
            $sales = TransactionService::getLogScaleData('weekly');
        }

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
