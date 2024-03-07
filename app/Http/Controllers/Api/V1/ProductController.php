<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\GenericMessage;
use App\Http\Helpers\HttpStatus;
use App\Http\Helpers\HttpStatusCode;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\V1\ProductCollection;
use App\Http\Resources\V1\ProductResource;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = QueryBuilder::for(Product::class)
            ->allowedSorts(['id', 'name', 'created_at',  'quantity', 'srp'])
            ->allowedFilters(['id', 'name', 'created_at', 'category_id', 'srp', 'is_removed'])
            ->active()
            ->paginate();
        return new ProductCollection($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {

        $validated_data = $request->validated();
        $product = Product::create($validated_data);
        $message = GenericMessage::productAdded($product->name);
        return response()->json('Product was succesfully added');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }
    /**
     * Update the specified resource in storage.
     */
    // public function edit(UpdateProductRequest $request, Product $product)
    // {
    //     $validated_data = $request->validated();
    //     $product->update($validated_data);
    //     $message = GenericMessage::productUpdated($product->name);
    //     return response()->json([
    //         'data' => new ProductResource($product),
    //         'message' => $message,
    //     ]);
    // }


    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated_data = $request->validated();
        $product->update($validated_data);
        return response()->json('Product was succesfully updated');
    }


    public function destroy(Product $product)
    {
        if ($product->is_removed) {
            return response()->json("Product was already removed.");
        }
        $product->is_removed = true;
        $product->save();

        return response()->json("{$product->name} was successfully removed.");
    }
}
