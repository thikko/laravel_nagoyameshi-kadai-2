<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $category_id = $request->category_id;
        $price = $request->price;
        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '掲載日が古い順' => 'created_at asc',
            '価格が安い順' => 'lowest_price asc',
            '価格が高い順' => 'lowest_price desc',
            '評価が高い順' => 'rating desc',
            '評価が低い順' => 'rating asc',
        ];
        $categories = Category::all();

        // 絞り込み
        $query = Restaurant::query();
        // 変数$keywordが存在するとき
        if($keyword) {
            $query->where(function($q) use($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%")
                    ->orWhereHas('categories', function($q) use($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                });
            });
        }
         // 変数$category_idが存在する場合
         if ($category_id) {
            $query->whereHas('categories', function($q) use($category_id){
                $q->where('categories.id', $category_id);
            });
        }
          // 変数$priceが存在する場合
        if($price) {
            $query->where('lowest_price','<=', $price);
        }

        // 並び替えのデフォルト
        $sorted = "created_at desc";
        $sort_query = [];
        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }

        // sortable()メソッドを使って並び替え
        $restaurants = $query->sortable($sort_query)
                ->paginate(15);

        $total = $restaurants->total();

        return view('restaurants.index',compact('restaurants', 'sorts', 'sorted', 'keyword', 'categories', 'category_id', 'price', 'total'));
    }

    public function show(Restaurant $restaurant) {
        return view('restaurants.show', compact('restaurant'));
    }
}
