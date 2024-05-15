<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Utils\Response;
use App\Models\Category;

class CategoryController extends Controller
{
  public function store(StoreCategoryRequest $request, Category $category)
  {
    $data = $request->validated();
    $category->create($data);
    return Response::createResource();
  }

  public function update(UpdateCategoryRequest $request, Category $category)
  {
    $data = $request->validated();
    $category->update($data);
    return Response::updateResource();
  }

  public function destroy(int $id)
  {
    $category = Category::find($id);
    if (!$category) {
      return Response::notFound();
    }
    $category->delete();
    return Response::deleteResource();
  }
}
