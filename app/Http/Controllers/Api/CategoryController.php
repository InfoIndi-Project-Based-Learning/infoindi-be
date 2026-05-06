<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends BaseApiController
{
    private CategoryService $categoryService;
    
    public function __construct(CategoryService $categoryService) {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $categories = $this->categoryService->getCategories($request);
        return $this->paginated(
            $categories, 
            CategoryResource::class, 
            'Categories fetched successfully.');
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());
        return $this->created($category, 'Category created successfully.');
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->categoryService->update($category, $request->validated());
        return $this->success($category, 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);
        return $this->success(null, 'Category deleted successfully.');
    }
}
