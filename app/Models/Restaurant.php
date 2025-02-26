<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Restaurant extends Model
{
    use HasFactory, Sortable;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s O',
        'updated_at' => 'datetime:Y-m-d H:i:s O',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone('+09:00'))->format('Y-m-d H:i:s');
    }

    public function categories() {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function regular_Holidays() {
        return $this->belongsToMany(RegularHoliday::class)->withTimestamps();
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function ratingSortable($query, $direction) {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', $direction);
    }

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    // 予約数が多い順
    public function popularSortable($query, $direction) {
        return $query->withCount('reservations')->orderBy('reservations_count', $direction);
    }
}
