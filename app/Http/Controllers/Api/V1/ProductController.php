<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\GenericMessage;
use App\Models\Product;
use App\Models\User;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\V1\ProductCollection;
use App\Http\Resources\V1\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //get the request input per page in query params
        $per_page = $request->input('per_page', 15);
        $products = QueryBuilder::for(Product::class)
            ->allowedSorts([
                'id',
                'name',
                'created_at',
                'added_by',
                'quantity',
                'srp'
            ])
            ->allowedFilters([
                'id',
                'name',
                'created_at',
                'added_by',
                'category_id',
                'srp',
                'is_removed'
            ])
            ->paginate($per_page);

        //append it to the products variable
        $products->appends(['per_page' => $per_page]);
            
        //cache the data
        $cache_data = Cache::remember('products', now()->addHours(2), function () use ($products) {
            return new ProductCollection($products);
        });

        return $cache_data;

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
        $user = Auth::user();
        $validated_data = $request->validated();
        $product = $user->products()->create($validated_data);
        return response()->json("$product->name was succesfully added");
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated_data = $request->validated();
        $product->update($validated_data);

        return response()->json("$product->name was succesfully updated");
    }


    public function destroy(Product $product)
    {
        // if ($product->is_removed) {
        //     return response()->json("Product was already removed.");
        // }
        // $product->is_removed = true;
        // $product->save();

        // return response()->json("{$product->name} was successfully removed.");

        Product::find($product->id)->delete();
        return response()->json("{$product->name} was successfully removed.");
    }
}
