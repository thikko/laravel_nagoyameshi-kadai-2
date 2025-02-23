<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // indexアクション
    public function index(Restaurant $restaurant)
    {
         // 現在のユーザーを取得
         $user = Auth::user();
        // 条件分岐でレビューを取得
        if ($user && $user->subscribed('premium_plan')) {
            // 有料会員の場合、引数で受け取ったRestaurantインスタンスのIDと一致するデータをページネーションで5件ずつ取得
            $reviews = Review::where('restaurant_id', $restaurant->id)
            // 作成日時が新しい順に並べ替える
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        } else {
            $reviews = Review::where('restaurant_id', $restaurant->id)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        }

        return view('reviews.index', compact('restaurant', 'reviews'));
    }
    // createアクション
    public function create(Restaurant $restaurant)
    {
        return view('reviews.create', compact('restaurant'));
    }
    // storeアクション
    public function store(Request $request, Restaurant $restaurant)
    {
        // バリデーション
        $request->validate([
            'score' => 'required|integer|between:1,5',
            'content' => 'required', 
        ]);
        // レビューの新規作成
        $review = new Review();
        $review->score = $request->score;
        $review->content = $request->content;
        $review->restaurant_id = $restaurant->id;
        $review->user_id = Auth::id();

        $review->save();
        
        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを投稿しました。');
    }
    // editアクション
    public function edit(Restaurant $restaurant, Review $review)
    {
        $user = Auth::user();
        if ($user->id !== $review->user_id){
            return redirect()->route('restaurants.reviews.index', ['restaurant' => $restaurant->id])->with('error_message', '不正なアクセスです。');
        }

        return view('reviews.edit', compact('restaurant', 'review'));
    }
    // updateアクション（レビュー更新機能）
    public function update(Request $request, Restaurant $restaurant, Review $review)
    {
        // ログイン中のユーザーがレビューの所有者か確認
        if ($review->user_id !== Auth::id()) {
            // 一致しない場合一覧ページにリダイレクト
            return redirect()->route('restaurants.reviews.index', $restaurant->id)
                ->with('error_message', '不正なアクセスです。');
        }

        // storeと同じバリデーション
        $request->validate([
            'score' => 'required|integer|between:1,5',
            'content' => 'required|string',
        ]);

        // レビューの更新
        $review->update([
            'score' => $request->score,
            'content' => $request->content,
        ]);

        // 更新後、レビュー一覧ページにリダイレクト
        return redirect()->route('restaurants.reviews.index', $restaurant->id)
            ->with('flash_message', 'レビューを編集しました。');
    }
    // destroyアクション
    public function destroy(Request $request, Restaurant $restaurant, Review $review)
    {
        $user = Auth::user();

        if ($user->id !== $review->user_id){
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }
        // データの削除
        $review->delete();

        return redirect()->route('restaurants.reviews.index', $restaurant->id)->with('flash_message', 'レビューを削除しました。');
    }
}
