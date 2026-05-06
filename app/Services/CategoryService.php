<?php

namespace App\Services;

use App\Models\Category;
use App\Traits\HasQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CategoryService
{
    use HasQuery;
    
    public function getCategories(
        Request $request
        ) : LengthAwarePaginator {
        $query = Category::query();
        $query = $this->applySearch($query, $request->get('search'), ['category_name']);
        return $this->paginate($query, $request);
    }

    public function findById($id)
    {
        return Category::find($id);
    }

    public function findBySlug($slug)
    {
        return Category::where('slug', $slug)->first();
    }

    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data)
    {
        $category->update($data);
        return $category;
    }

    public function delete(Category $category)
    {
        $category->delete();
        return true;
    }
}