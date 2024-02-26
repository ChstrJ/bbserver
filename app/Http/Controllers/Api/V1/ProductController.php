<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\GenericMessage;
use App\Http\Helpers\HttpStatusCode;
use App\Http\Helpers\HttpStatusMessage;
use App\Http\Helpers\ResponseHelper;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;


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
        $validated_data = $request->validated();
        $product = Product::create($validated_data);
        $message = GenericMessage::productAdded($product->name);
        return ResponseHelper::productResponse($product, $message);
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
    public function edit(UpdateProductRequest $request, int $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(["message" => HttpStatusMessage::$NOT_FOUND], 404);
        }
        $validated_data = $request->validated();
        $product->update($validated_data);
        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(["message" => HttpStatusMessage::$NOT_FOUND], 404);
        }
        $validated_data = $request->validated();
        $product->update($validated_data);
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $product = Product::destroy($id);
        if (!$product) {
            return response()->json(["message" => HttpStatusMessage::$BAD_REQUEST], 400);
        }
        return $product;
    }
}
