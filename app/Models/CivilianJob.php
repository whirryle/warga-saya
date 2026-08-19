<?php

namespace App\Models;

use App\Models\Civilian;
use App\Models\Employee;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CivilianJob extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'civilian_jobs';
    protected $guarded = [];

    public function civilians(): BelongsToMany {
        return $this->belongsToMany(Civilian::class, 'civilian_pivot_jobs');
    }
    // public function civilians(): HasMany {
    //     return $this->hasMany(Civilian::class);
    // }

    protected function activityLogName(): string
    {
        return 'Pekerjaan';
    }
}
