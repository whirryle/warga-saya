<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;
    protected $table = 'activities';
    protected $fillable = [
        'name',
        'target',
        'category_id',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activity_user');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function civilians(): BelongsToMany
    {
        return $this->belongsToMany(Civilian::class, 'civilian_pivot_activities')
            ->using(CivilianPivotActivity::class)
            ->withPivot('progress')
            ->withTimestamps();
    }

}
