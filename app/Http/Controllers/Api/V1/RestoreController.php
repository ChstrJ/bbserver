<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\customer\CustomerStatus;
use App\Http\Helpers\product\ProductStatus;
use App\Http\Helpers\transaction\TransactionStatus;
use App\Http\Helpers\user\UserStatus;
use App\Http\Utils\Message;
use App\Http\Utils\ResponseHelper;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;

class RestoreController extends Controller
{
    use ResponseHelper;

    public function getAllDeletedCustomers()
    {
        return Customer::where('is_active', CustomerStatus::$NOT_ACTIVE)->get();
    }
    public function getAllDeletedTransactions()
    {
        return Transaction::where('is_removed', TransactionStatus::$REMOVE)->get();
    }
    public function getAllDeletedEmployees()
    {
        return User::where('is_active', UserStatus::$NOT_ACTIVE)->get();
    }

    public function getAllDeletedProducts()
    {
        return Product::where('is_removed', ProductStatus::$REMOVE)->get();
    }

    public function restoreCustomer(int $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        if ($customer->is_active === CustomerStatus::$ACTIVE) {
            return $this->json(Message::alreadyChanged(), HttpStatusCode::$CONFLICT);
        }

        $customer->is_active = CustomerStatus::$ACTIVE;
        $customer->save();

        return $this->json(Message::restoreResource(), HttpStatusCode::$ACCEPTED);
    }

    public function restoreTransaction(int $id)
    {
        $order = Transaction::find($id);
        if (!$order) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        if ($order->is_removed === TransactionStatus::$RESTORE) {
            return $this->json(Message::alreadyChanged(), HttpStatusCode::$CONFLICT);
        }
        $order->is_removed = TransactionStatus::$RESTORE;
        $order->save();

        return $this->json(Message::restoreResource(), HttpStatusCode::$ACCEPTED);
    }

    public function restoreEmployee(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        if($user->is_active === UserStatus::$NOT_ACTIVE) {
            return $this->json(Message::alreadyChanged(), HttpStatusCode::$CONFLICT);
        }

        $user->is_active = UserStatus::$NOT_ACTIVE;
        $user->save();
        return $this->json(Message::restoreResource(), HttpStatusCode::$ACCEPTED);
    }

    public function restoreProduct(int $id)
    {
        $product = User::find($id);
        if (!$product) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        if($product->is_removed === ProductStatus::$RESTORE) {
            return $this->json(Message::alreadyChanged(), HttpStatusCode::$CONFLICT);
        }

        $product->is_removed = ProductStatus::$RESTORE;
        $product->save();
        return $this->json(Message::restoreResource(), HttpStatusCode::$ACCEPTED);
    }
}