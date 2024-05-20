<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\transaction\TransactionStatus;
use App\Http\Helpers\user\UserService;
use App\Http\Helpers\transaction\TransactionService;
use App\Http\Utils\Response;
use App\Http\Utils\ResponseHelper;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\V1\TransactionCollection;
use App\Http\Resources\V1\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    use ResponseHelper;
    public function index(Request $request)
    {

        //get the request input per page in query params
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = $request->input('per_page', 15);
        $sortByDesc = $request->input('sort_by_desc');
        $sortByAsc = $request->input('sort_by_asc');
        $categoryId = $request->input('category_id');
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Transaction::query()
            ->select('transactions.*')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->where('transactions.user_id', UserService::getUserId())
            ->orderBy('transactions.created_at', 'DESC')
            ->orderBy('transactions.status', 'ASC')
            ->with('customer', 'user');

        if ($startDate && $endDate) {
            $query->whereDate('transactions.created_at', '>=', $startDate)
                ->whereDate('transactions.created_at', '<=', $endDate);
        } else if ($startDate) {
            $query->whereDate('transactions.created_at', '>=', $startDate);
        } else if ($endDate) {
            $query->whereDate('transactions.created_at', '<=', $endDate);
        }

        if($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($sortByDesc) {
            $query->orderBy("transactions.$sortByDesc", 'DESC');
        }

        if ($sortByAsc) {
            $query->orderBy("transactions.$sortByAsc", 'ASC');
        }

        if ($status) {
            $query->where('transactions.status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transactions.reference_number', 'LIKE', "%{$search}%")
                    ->orWhere('customers.name', 'LIKE', "%{$search}%");
            });
        }

        $transaction = $query->paginate($perPage);
        return new TransactionCollection($transaction);
    }

    public function store(StoreTransactionRequest $request)
    {
        //check if the user is logged in
        $user = Auth::user();
        $validated_data = $request->validated();

        //reduce the qty from db and auto compute items & amount
        $total = TransactionService::processTransaction($validated_data);
        $reference_number = TransactionService::generateReference();

        //attach to payload
        $validated_data['reference_number'] = $reference_number;
        $validated_data['number_of_items'] = $total['total_items'];
        $validated_data['amount_due'] = $total['total_amount'];
        $validated_data['commission'] = $total['commission'];

        $user->transactions()->create($validated_data);
        return Response::orderSuccess();
    }

    public function show(int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return Response::notFound();
        }
        return new TransactionResource($transaction->load('customer'));
    }
    public function destroy(int $id)
    {
        $order = Transaction::find($id);
        if (!$order) {
            return Response::notFound();
        }
        if ($order->is_removed === TransactionStatus::$REMOVE) {
            return Response::alreadyChanged();
        }
        $order->is_removed = TransactionStatus::$REMOVE;
        $order->save();

        return Response::deleteResource();
    }

}
