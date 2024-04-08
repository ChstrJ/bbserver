<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Utils\DynamicMessage;
use App\Http\Utils\GenericMessage;
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
            ]);
        

        if($startDate && $endDate) {
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
        $user = Auth::user();
        $validated_data = $request->validated();
        $product = $user->products()->create($validated_data);
        return response()->json(DynamicMessage::productAdded($product->name));
    }

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
