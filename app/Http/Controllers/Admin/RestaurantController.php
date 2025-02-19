<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\RegularHoliday;
use App\Http\Requests\RestaurantRequest;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        if ($keyword !== null) {
            $restaurants = Restaurant::where('name', 'LIKE', "%{$keyword}%")
            ->paginate(15);
            $total = $restaurants->total();
        } else {
            $restaurants = Restaurant::paginate(15);
            $total = 0;
            $keyword = null;
        }

        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }

    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurants.show', compact('restaurant'));
    }

    public function create()
    {
        $regular_holidays = RegularHoliday::all();
        $categories = Category::all();
        return view('admin.restaurants.create', compact('categories', 'regular_holidays'));
    }

    public function store(RestaurantRequest $request)
    {
        $restaurant = new Restaurant();
        $restaurant->name = $request->input('name');
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/restaurants');
            $restaurant->image = basename($image);
        } else {
            $restaurant->image = '';
        }

        $restaurant->save();

        // HTTPリクエストから取得したcategory_idsパラメータ(カテゴリのID配列)に基づいて、category_restaurantテーブルのデータを同期する
        $category_ids = array_filter($request->input('category_ids', []));
        $restaurant->categories()->sync($category_ids);
        // Httpリクエストから取得したregular_holiday_idsパラメータ（定休日のIDの配列）に基づいて、regular_holiday_restaurantテーブルのデータを同期する
        $regular_holiday_ids = array_filter($request->input('regular_holiday_ids', []));
        $restaurant->regular_holidays()->sync($regular_holiday_ids);

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
    }

    public function edit(Restaurant $restaurant)
    {
        // インスタンスに紐づくcategoriesテーブルのすべてのデータをインスタンスのコレクションとして取得する
        $categories = $restaurant->categories;
        // 設定されたカテゴリのIDを配列化する
        $category_ids = $restaurant->categories->pluck('id')->toArray();

        // viewに渡す変数に（変数$regular_holidays 内容regular_holidaysテーブルの全てのデータ）を追加する
        $regular_holidays = RegularHoliday::all();

        return view('admin.restaurants.edit', compact('restaurant', 'categories', 'category_ids', 'regular_holidays'));
    }

    public function update(RestaurantRequest $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/restaurants');
            $restaurant->image = basename($image);
        }

        $restaurant->name = $request->input('name');
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
        $restaurant->save();
        // storeアクションと同様に、HTTPリクエストから取得したcategory_idsパラメータ(カテゴリのID配列)に基づいて、category_restaurantテーブルのデータを同期する
        $category_ids = array_filter($request->input('category_ids', []));
        $restaurant->categories()->sync($category_ids);

        // storeアクションと同様に、HTTPリクエストから取得したregular_holiday_idsパラメータ（定休日のIDの配列）に基づいて、regular_holiday_restaurantテーブルのデータを同期する処理を追記
        $regular_holiday_ids = array_filter($request->input('regular_holiday_ids', []));
        $restaurant->regular_holidays()->sync($regular_holiday_ids);

        return redirect()->route('admin.restaurants.update', ['restaurant' => $restaurant->id])->with('flash_message', '店舗を編集しました。');
    }

    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
}
