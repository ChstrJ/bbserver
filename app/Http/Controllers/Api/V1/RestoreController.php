<?php


namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Helpers\customer\CustomerStatus;
use App\Http\Helpers\transaction\TransactionStatus;
use App\Http\Helpers\user\UserStatus;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;

class RestoreController extends Controller 
{
   
    public function getAllDeletedCustomer() 
    {
        return Customer::where('is_active', CustomerStatus::$NOT_ACTIVE)->get();
    }
    public function getAllDeletedTransactions() 
    {
        return Transaction::where('is_removed', TransactionStatus::$REMOVED)->get();
    }
    public function getAllDeletedEmployee() 
    {
        return User::where('is_active', UserStatus::$NOT_ACTIVE)->get();
    }

    public function restoreCustomer(int $id) 
    {

    }

    public function restoreTransactions(int $id) 
    {

    }

    public function restoreEmployee(int $id)
    {

    }
}