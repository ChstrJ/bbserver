<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\user\UserService;
use App\Http\Utils\DynamicMessage;
use App\Http\Utils\GenericMessage;
use App\Http\Utils\HttpStatusCode;
use App\Http\Utils\Message;
use App\Http\Utils\ResponseHelper;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\V1\ProductCollection;
use App\Http\Resources\V1\ProductResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    use ResponseHelper;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //get the request input for date range filter
        $startDate = $request->input('filter.created_at.0');
        $endDate = $request->input('filter.created_at.1');

        //get the request input per page in query params
        $per_page = $request->input('per_page');

        $query = QueryBuilder::for(Product::class)
            ->allowedSorts([
                'id',
                'name',
                'created_at',
                'updated_at',
                'user_id',
                'quantity',
                'srp'
            ])
            ->allowedFilters([
                'id',
                'name',
                'created_at',
                'updated_at',
                'user_id',
                'category_id',
                'srp',
                'is_removed'
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('updated_at')
            ->with('user');


        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        //paginate the results
        $products = $query->paginate($per_page);

        return new ProductCollection($products);

    }

    public function store(StoreProductRequest $request)
    {
        $user = UserService::getUser();
        $validated_data = $request->validated();
        $validated_data['created_by'] = $user->id;
        $product = $user->products()->create($validated_data);
        return $this->json(DynamicMessage::productAdded($product->name));
    }

    public function show(int $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        return new ProductResource($product->load('user'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $user = UserService::getUser();
        $validated_data = $request->validated();
        $validated_data['updated_by'] = $user->id;
        $product->update($validated_data);
        if (!$product) {
            return $this->json(Message::invalid(), HttpStatusCode::$UNPROCESSABLE_ENTITY);
        }
        return $this->json(DynamicMessage::productUpdated($product->name));
    }

    public function destroy(int $id)
    {
        // if ($product->is_removed) {
        //     return response()->json("Product was already removed.");
        // }
        // $product->is_removed = true;
        // $product->save();

        // return response()->json("{$product->name} was successfully removed.");
        $product = Product::find($id);
        if (!$product) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        $product->delete();

        return $this->json(DynamicMessage::productRemove($product->name));
    }
}
