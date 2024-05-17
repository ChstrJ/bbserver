<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\product\ProductService;
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
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    use ResponseHelper;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortByDesc = $request->input('sort_by_desc');
        $sortByAsc = $request->input('sort_by_asc');
        $categoryId = $request->input('category_id');
        $perPage = $request->input('per_page', 15);

        $query = Product::query()
            ->whereNot('is_removed', ProductStatus::$REMOVE)
            ->orderBy('created_at', 'DESC')
            ->orderBy('updated_at', 'DESC');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('srp', 'LIKE', "%$search%")
                    ->orWhere('member_price', 'LIKE', "%{$search}%");
            });
        }

        if($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($sortByDesc) {
            $query->orderBy($sortByDesc, 'DESC');
        }

        if ($sortByAsc) {
            $query->orderBy($sortByDesc, 'ASC');
        }

        $products = $query->paginate($perPage);
        return new ProductCollection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $user = UserService::getUser();
        $validated_data = $request->validated();
        $code = ProductService::generateProductCode();
        $validated_data['created_by'] = $user->id;
        $validated_data['product_code'] = $code;
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
        if ($product->is_removed === ProductStatus::$REMOVE) {
            return $this->json(Message::alreadyChanged(), HttpStatusCode::$CONFLICT);
        }

        $product->is_removed = ProductStatus::$REMOVE;
        $product->save();
        return $this->json(Message::deleteResource(), HttpStatusCode::$ACCEPTED);
    }
}
