<?php

namespace App\Http\Helpers\summary;

use App\Models\Transaction;
use App\Models\User;


class SummaryService
{
    public static function getOrderSummary(string $today)
    {
        return Transaction::selectRaw("
            COUNT(CASE WHEN status = 'approved' THEN status ELSE null END) AS approved_count,
            COUNT(CASE WHEN status = 'rejected' THEN status ELSE null END) AS rejected_count,
            COUNT(CASE WHEN status = 'pending' THEN status ELSE null END) AS pending_count,
            TRUNCATE(SUM(CASE WHEN status = 'rejected' THEN amount_due ELSE 0 END), 2) AS total_rejected,
            TRUNCATE(SUM(CASE WHEN status = 'pending' THEN amount_due ELSE 0 END), 2) AS total_pending,
            TRUNCATE(SUM(CASE WHEN status = 'approved' THEN commission ELSE 0 END), 2) AS total_commission,
            TRUNCATE(SUM(CASE WHEN status = 'approved' THEN amount_due ELSE 0 END), 2) AS overall_sales,
            TRUNCATE(SUM(CASE WHEN DATE(created_at) = '" . $today . "' AND status = 'approved' THEN amount_due ELSE 0 END), 2) AS today_sales
        ")->first();
    }

    public static function getOrderSummaryPerUser(int $currentUser, string $today)
    {
        return Transaction::selectRaw("
            COUNT(CASE WHEN status = 'approved' AND user_id = '" . $currentUser . "' THEN status ELSE null END) AS approved_count,
            COUNT(CASE WHEN status = 'rejected' AND user_id = '" . $currentUser . "' THEN status ELSE null END) AS rejected_count,
            COUNT(CASE WHEN status = 'pending' AND user_id = '" . $currentUser . "' THEN status ELSE null END) AS pending_count,
            TRUNCATE(SUM(CASE WHEN status = 'approved' AND user_id = '" . $currentUser . "' THEN amount_due ELSE 0 END), 2) AS overall_sales,
            TRUNCATE(SUM(CASE WHEN DATE(created_at) = '" . $today . "' AND user_id = '" . $currentUser . "' AND status = 'approved' THEN amount_due ELSE 0 END), 2) AS today_sales
        ")->first();
    }

    public static function getEmployeeSummary()
    {
        return User::selectRaw("
            COUNT(CASE WHEN role_id != 3 THEN role_id ELSE NULL END) AS all_users,
            COUNT(CASE WHEN role_id = 1 THEN role_id ELSE null END) AS admin,
            COUNT(CASE WHEN role_id = 2 THEN role_id ELSE null END) AS employee
        ")->first();
    }
}

