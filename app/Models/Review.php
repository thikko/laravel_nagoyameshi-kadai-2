<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    // updateアクションでレビューの保存に使う
    protected $fillable = ['content', 'score'];

    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurants()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
