<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\customer\CustomerStatus;
use App\Http\Helpers\product\ProductStatus;
use App\Http\Helpers\summary\SummaryService;
use App\Http\Helpers\transaction\TransactionService;
use App\Http\Helpers\user\UserService;
use App\Http\Utils\ResponseHelper;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use ResponseHelper;
    public function getAllSummary()
    {
        $today = UserService::getDate();
        $currentUser = UserService::getUserId();
        $products = Product::whereNot('is_removed', ProductStatus::$REMOVE)->count();
        $customers = Customer::whereNot('is_active', CustomerStatus::$NOT_ACTIVE)->count();
        $orders = SummaryService::getOrderSummaryPerUser($currentUser, $today);
      
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
            ]
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
        $currentUser = UserService::getUserId();

        $interval = $interval ?: 'weekly';
        $sales = TransactionService::getLogScaleData($interval, $currentUser);
        
        return $this->json($sales);
    }

    
}
