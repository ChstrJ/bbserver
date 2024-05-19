<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\customer\CustomerStatus;
use App\Http\Helpers\product\ProductStatus;
use App\Http\Helpers\transaction\TransactionStatus;
use App\Http\Helpers\user\UserService;
use App\Http\Utils\ResponseHelper;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
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

        $orders = Transaction::selectRaw("
            COUNT(CASE WHEN status = 'approved' THEN status ELSE null END) AS approved_count,
            COUNT(CASE WHEN status = 'rejected' THEN status ELSE null END) AS rejected_count,
            COUNT(CASE WHEN status = 'pending' THEN status ELSE null END) AS pending_count,
            TRUNCATE(SUM(CASE WHEN status = 'approved' AND user_id = '" . $currentUser . "' THEN amount_due ELSE 0 END), 2) AS overall_sales,
            TRUNCATE(SUM(CASE WHEN DATE(created_at) = '" . $today . "' AND user_id = '" . $currentUser . "' THEN amount_due ELSE 0 END), 2) AS today_sales
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
            ]
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

        if ($sales) {
            $sales = $this->getLogScaleData($interval);
        } else {
            $sales = $this->getLogScaleData('weekly');
        }

        return $this->json($sales);
    }

    private static function getLogScaleData($interval)
    {
        $now = Carbon::now();
        $id = UserService::getUserId();

        $query = Transaction::query();

        $startWeek = $now->startOfWeek()->toDateTimeString();
        $endWeek = $now->endOfWeek()->toDateTimeString();

        $startYear = $now->startOfYear()->toDateString();
        $endYear = $now->endOfYear()->toDateString();

        switch ($interval) {
            case 'weekly':

                $weeklySales = [
                    'Monday' => 0,
                    'Tuesday' => 0,
                    'Wednesday' => 0,
                    'Thursday' => 0,
                    'Friday' => 0,
                    'Saturday' => 0,
                    'Sunday' => 0,
                ];

                $weeklySalesData = $query
                    ->selectRaw('DATE(created_at) AS day, TRUNCATE(SUM(amount_due), 2) AS total_sales')
                    ->where('user_id', $id)
                    ->whereRaw('status = ?', [TransactionStatus::$APPROVE])
                    ->whereBetween('created_at', [$startWeek, $endWeek])
                    ->groupByRaw('DATE(created_at)')
                    ->get();


                for ($day = 0; $day < count($weeklySalesData); $day++) {
                    $dayName = Carbon::parse($weeklySalesData[$day]->day)->format('l');
                    $weeklySales[$dayName] = $weeklySalesData[$day]->total_sales;
                }

                return $weeklySales;

            case 'monthly':

                $monthSales = [];

                $monthlySales = $query
                    ->selectRaw('MONTH(created_at) AS month, TRUNCATE(SUM(amount_due), 2) AS total_sales')
                    ->where('user_id', $id)
                    ->whereRaw('status = ?', [TransactionStatus::$APPROVE])
                    ->whereBetween('created_at', [$startYear, $endYear])
                    ->groupByRaw('MONTH(created_at)')
                    ->get();


                for ($month = 0; $month < 12; $month++) {
                    $monthName = Carbon::createFromDate(null, $month)->format('F');
                    $monthName = substr($monthName, 0, 3);
                    $salesData = $monthlySales->where('month', $month)->first();
                    $monthSales[$monthName] = $salesData ? $salesData->total_sales : 0;
                }

                return $monthSales;

            case 'yearly':

                $yearSales = [];

                //start with the last 5 years through the current yhear
                $startYear = $now->year - 4;
                $endYear = $now->year;

                $yearlySalesData = $query
                    ->selectRaw('YEAR(created_at) AS year, SUM(amount_due) AS total_sales')
                    ->where('user_id', $id)
                    ->whereRaw('status = ?', [TransactionStatus::$APPROVE])
                    ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 5 YEAR)')
                    ->groupByRaw('YEAR(created_at)')
                    ->get()
                    ->keyBy('year');

                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearSales[$year] = $yearlySalesData->has($year) ? $yearlySalesData[$year]->total_sales : 0;
                }

                return $yearSales;

            default:
                $todaySales = $query
                    ->whereRaw('status = ?', [TransactionStatus::$APPROVE])
                    ->whereRaw('created_at = ?', [UserService::getDate()])
                    ->sum('amount_due');

                return ["today_sales" => $todaySales];
        }
    }
}
