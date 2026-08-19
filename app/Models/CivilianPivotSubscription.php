<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CivilianPivotSubscription extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'civilian_pivot_subscriptions';
    protected $fillable = [
        'subscription_id',
        'civilian_id',
        'paid_months',
        'debit',
    ];

    protected $casts = [
        'paid_months' => 'array',
        'debit' => 'decimal:2',
    ];

    // Accessor untuk balance (update logic)
    public function getTotalBalanceAttribute()
    {
        return $this->debit - $this->expenses()->where('is_income', false)->sum('amount');
    }

    public function getIsPaidAttribute()
    {
        return !empty($this->paid_months);
    }

    // Relasi ke model Employee
    public function civilian(): BelongsTo
    {
        return $this->belongsTo(Civilian::class);
    }

    // Relasi ke model Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    protected function activityLogName(): string
    {
        return 'Data Iuran';
    }

}
