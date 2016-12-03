<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request){
        $categories = Category::with(['articles' => function($articles){
            $articles->where('is_deleted', 0);
        }])->get();
        return view('backend.categoryList', compact('categories'));
    }

    public function show(Request $request, $categoryId){
        return Category::with('articles')->find($categoryId);
    }

    public function update(Request $request, $categoryId){
        $updatedCategory = $request->only(['name', 'position', 'parent_category_id']);
        try{
            Category::where('id', $categoryId)->update($updatedCategory);
        }catch (\PDOException $e){
            return response()->json(['message' => $e->getMessage()]);
        }
        return response()->json(['message' => 'Category updated successfully!']);
    }

    public function store(Request $request){
        $newCategory = $request->only(['name', 'position', 'parent_category_id']);
        try{
            $category = Category::create($newCategory);
        }catch (\PDOException $e){
            return response()->json(['message' => $e->getMessage()]);
        }
        return response()->json(['message' => 'Category Created successfully!', 'entity' => $category]);
    }

    public function toggleActive(Request $request, $categoryId){
        $category = Category::find($categoryId);
        try{
            $category->update(['is_active' => !$category->is_active]);
        }catch(\PDOException $e){
            return response()->json(['message' => $e->getMessage()]);
        }
        return redirect()->route('categories');
    }

    public function getArticles(Request $request, $categoryAlias){
        $category = Category::where('alias', $categoryAlias)->with(['articles'  => function($articles){
            $articles->where('is_published', 1)->where('is_deleted', 0);
        }])->first();
        $articles = $category->articles;
        return view('frontend.articles', compact('articles'));
    }
}
