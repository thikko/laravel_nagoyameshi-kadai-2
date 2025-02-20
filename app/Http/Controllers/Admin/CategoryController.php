<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function index(Request $request) 
    {
        $keyword = $request->input('keyword');

        if($keyword !== null) {
            $categories = Category::where('name', 'LIKE', "%{$keyword}%")
            ->paginate(15);
            $total = $categories->total();
        } else {
            $categories = Category::paginate(15);
            $total = 0;
            $keyword = null;
        }

        return view('admin.categories.index', compact('categories', 'keyword', 'total'));
    }

    public function store(CategoryRequest $request)
    {
        $category = new Category();
        $category->name = $request->input('name');

        $category->save();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを登録しました。');
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);

        $category->name = $request->input('name');
        $category->save();

        return redirect()->route('admin.categories.index', )->with('flash_message', 'カテゴリを編集しました。');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを削除しました。');
    }
}
