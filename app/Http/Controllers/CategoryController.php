<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // List all categories
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    // Show form to create a new category
    public function create()
    {
        return view('categories.create');
    }

    // Store a new category
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Category::create(['name' => $request->name]);
        return back()->with('success', 'Category added!');
    }

    // Show a single category
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    // Show form to edit a category
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // Update a category
    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name]);
        return back()->with('success', 'Category updated!');
    }

    // Delete a category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return back()->with('success', 'Category deleted!');
    }
}