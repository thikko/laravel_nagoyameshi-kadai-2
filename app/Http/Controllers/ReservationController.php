<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    //indexアクション（予約一覧ページ）
    public function index()
    {
        $user = Auth::user();

        $reservations = Reservation::where('user_id', $user->id)
            ->orderBy('reserved_datetime', 'desc')
            ->paginate(15);
        
        return view('reservations.index', compact('reservations'));
    }

    // createアクション（予約ページ）
    public function create(Restaurant $restaurant)
    {
        return view('reservations.create', compact('restaurant'));
    }

    // storeアクション（予約機能）
    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'reservation_date' => 'required|date_format:Y-m-d',
            'reservation_time' => 'required|date_format:H:i',
            'number_of_people' => 'required|integer|between:1,50',

        ]);
        // 予約の新規作成
        $reservation = new Reservation();
        $reservation->reserved_datetime = $request->input('reservation_date') . ' ' . $request->input('reservation_time');
        $reservation->number_of_people = $request->input('number_of_people');
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = Auth::id();
        $reservation->save();

        return Redirect()->route('reservations.index')->with('flash_message', '予約が完了しました。');
    }

    // destroyアクション（予約キャンセル機能）
    public function destroy(Restaurant $restaurant, Reservation $reservation)
    {
        $user = Auth::user();
        if ($user->id !== $reservation->user_id){
            return redirect()->route('reservations.index')->with('error_message', '不正なアクセスです。');
        }

        $reservation->delete();

        return Redirect()->route('reservations.index')->with('flash_message', '予約をキャンセルしました。');
    }
}
