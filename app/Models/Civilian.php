<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Liability;
use App\Models\CivilianJob;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use App\Models\CivilianPivotSubscription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Civilian extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'civilians';
    protected $fillable = [
        'full_name',
        'age',
        'gender',
        'born_place',
        'born_date',
        'nik',
        'home_address',
        'married_status',
        'phone_number',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'civilian_pivot_categories', 'civilian_id', 'category_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function civilian_pivot_jobs(): HasMany
    {
        return $this->hasMany(CivilianPivotJob::class);
    }

    public function civilian_jobs(): BelongsToMany
    {
        return $this->belongsToMany(CivilianJob::class, 'civilian_pivot_jobs')
            ->withPivot('accepted_date', 'retirement_date');
    }

    public function civilian_pivot_subscriptions(): HasMany
    {
        return $this->hasMany(CivilianPivotSubscription::class);
    }

    public function pivotCategories()
    {
        return $this->hasMany(CivilianPivotCategory::class, 'civilian_id');
    }

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(Subscription::class, 'civilian_pivot_subscriptions')
            ->withPivot('temp_amount');
    }

    public function liabilities(): HasMany
    {
        return $this->hasMany(Liability::class);
    }

    public function liability(){
        return $this->belongsTo(Liability::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(
            Activity::class, 
            'civilian_pivot_activities',
            'civilian_id',
            'activity_id')
            ->using(CivilianPivotActivity::class)
            ->withPivot('progress')
            ->withTimestamps();
    }

    protected function activityLogName(): string
    {
        return 'Data Warga';
    }
}
