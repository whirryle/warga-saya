<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subscription extends Model
{
    use LogsActivity;
    protected $table = 'subscriptions';
    protected $guarded = [];

    public function civilians(): BelongsToMany {
        return $this->belongsToMany(Civilian::class, 'civilian_pivot_subscriptions')
        ->withPivot('temp_amount');  
    }

    public function categories(): HasMany {
        return $this->hasMany(Category::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function civilian_subscriptions(): HasMany
    {
        return $this->hasMany(CivilianPivotSubscription::class);
    }

    // app/Models/Subscription.php
    public function getNumericAmountAttribute()
    {
        $amount = preg_replace('/[^0-9]/', '', $this->amount);
        $amount = (float) $amount;
        
        // Handle format dengan koma desimal (Rp 1.000,00)
        if (strpos($this->amount, ',') !== false) {
            return $amount / 100;
        }
        
        return $amount;
    }

    protected function activityLogName(): string
    {
        return 'Iuran';
    }
}
