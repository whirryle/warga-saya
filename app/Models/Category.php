<?php

namespace App\Models;

use App\Models\Civilian;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'amount',
    ];

    public function civilians(): BelongsToMany
    {
        return $this->belongsToMany(Civilian::class, 'civilian_pivot_categories', 'category_id', 'civilian_id');
    }

    // public function civilians(): HasMany
    // {
    //     return $this->hasMany(Civilian::class);
    // }

    // public function subscriptions(): BelongsTo
    // {
    //     return $this->belongsTo(Category::class);
    // }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    protected function activityLogName(): string
    {
        return 'Kategori';
    }
}
