<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\product\ProductStatus;
use App\Http\Helpers\user\UserService;
use App\Http\Utils\DynamicMessage;
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
                'quantity',
                'srp'
            ])
            ->allowedFilters([
                'id',
                'name',
                'created_at',
                'updated_at',
                'category_id',
                'srp',
                'is_removed'
            ])
            ->whereNot('is_removed', ProductStatus::$REMOVE)
            ->orderByDesc('created_at')
            ->orderByDesc('updated_at');


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
        $product = Product::find($id);
        if (!$product) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        if($product->is_removed === ProductStatus::$REMOVE) {
            return $this->json(Message::alreadyChanged(), HttpStatusCode::$CONFLICT);
        }

        $product->is_removed = ProductStatus::$REMOVE;
        $product->save();
        return $this->json(Message::deleteResource(), HttpStatusCode::$ACCEPTED);
    }
}
