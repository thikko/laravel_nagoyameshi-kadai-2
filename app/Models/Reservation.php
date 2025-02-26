<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_date',
        'reservation_time',
        'number_of_people',
        'restaurant_id',
        'user_id',
    ];


    protected $casts = [
        'reservation_date' => 'date:Y-m-d',  // こちらは日付型にキャスト
        'reservation_time' => 'date:H:i',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
