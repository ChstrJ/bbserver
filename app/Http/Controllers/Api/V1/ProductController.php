<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::all();
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
        $validated_data = $request->validate([
            'category_id' => 'required|int',
            'name' => 'required|string|min:2|unique:products,name',
            'description' => 'required|string|min:2',
            'quantity' => 'required|int',
            'srp' => 'required|numeric',
            'member_price' => 'required|numeric',
        ]);


        $product = Product::create($validated_data);
        $message = "{$product->name} succesffully added to the inventory.";

        return response()->json([
            'message' => $message,
            'products' => $product
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return Product::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        $product = Product::find($id);
        $product->update($request->all());
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return Product::destroy($id);
    }
}
