<?php



namespace App\Http\Helpers\customer;
use App\Models\Customer;

class CustomerService {
    public static function getFullnameById(int $id) {
        return Customer::find($id)->full_name;
    }
}