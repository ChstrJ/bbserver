<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\product\ProductService;
use App\Http\Helpers\product\ProductStatus;
use App\Http\Helpers\queries\ProductQuery;
use App\Http\Helpers\user\UserService;
use App\Http\Utils\DynamicMessage;
use App\Http\Utils\Response;
use App\Http\Utils\ResponseHelper;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\V1\ProductCollection;
use App\Http\Resources\V1\ProductResource;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    use ResponseHelper;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input("per_page", 15);

        $query = ProductQuery::ProductQuery();
        ProductQuery::applyFilters($query, $request);
        $transactions = $query->paginate($perPage);

        return new ProductCollection($transactions);
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
            return Response::notFound();
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
            return Response::invalid();
        }
        return $this->json(DynamicMessage::productUpdated($product->name));
    }

    public function destroy(int $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return Response::notFound();
        }
        if ($product->is_removed === ProductStatus::$REMOVE) {
            return Response::alreadyChanged();
        }

        $product->is_removed = ProductStatus::$REMOVE;
        $product->save();

        return Response::deleteResource();
    }
}
