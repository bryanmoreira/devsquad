<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'day',
        'log',
    ];

    protected $casts = [
        'day' => 'datetime',
    ];

    public function scopeFromToday(Builder $query): Builder
    {
        return $query->whereDate('day', Carbon::today());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
