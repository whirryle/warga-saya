<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Civilian;
use App\Models\CivilianPivotActivity;
use Illuminate\Support\Facades\Auth;

class FormKegiatan extends Component
{

    public $selectedCategory = null;
    public $selectedCivilian = null;
    public $progressInputs = [];

    public $civilians = [];
    public $activities = null;

    public function mount()
    {
        $this->activities = collect();
        $this->civilians = collect();
    }

    public function updatedSelectedCategory($category_id)
    {
        $this->reset(['selectedCivilian', 'activities', 'progressInputs']);
        
        if ($category_id) {
            $this->civilians = Civilian::whereHas('categories', function($query) use ($category_id) {
                $query->where('category_id', $category_id);
            })->get();
        } else {
            $this->civilians = collect();
        }
    }

    public function updatedSelectedCivilian($civilian_id)
    {
        $this->reset(['activities', 'progressInputs']);
        
        if ($this->selectedCategory && $civilian_id) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            // 1) query kegiatan berdasarkan kategori
            $query = Activity::where('category_id', $this->selectedCategory);

            // 2) jika bukan super_admin, filter hanya kegiatan yang diurus user ini
            if ($user && !$user->isSuperAdmin())
            {
                $query->whereHas('users', function($q) use ($user)
                {

                    $q->where('users.id', $user->id);
                });
            }

            $this->activities = $query
                ->get()
                ->map(function($activity) use ($civilian_id) {
                    $progress = CivilianPivotActivity::where([
                        'civilian_id' => $civilian_id,
                        'activity_id' => $activity->id
                    ])->first();
                    
                    $this->progressInputs[$activity->id] = $progress ? $progress->progress : 0;
                    
                    return [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'target' => $activity->target
                    ];
                });
        }
    }

    public function saveAllProgress()
    {
        $this->validate([
            'progressInputs.*' => 'required|numeric|min:0'
        ]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        foreach ($this->progressInputs as $activityId => $progress) {
            // cek wewenang admin kegiatan terhadap activity_id
            if ($user && !$user->managesActivity($activityId)) {
                // skip atau lempar error jika tidak berhak mengurus kegiatan ini
                continue;
            } 

            CivilianPivotActivity::updateOrCreate(
                [
                    'civilian_id' => $this->selectedCivilian,
                    'activity_id' => $activityId
                ],
                [
                    'progress' => $progress
                ]
            );
        }

        session()->flash('success', 'Progress kegiatan berhasil disimpan.');
        $this->resetExcept(['selectedCategory']); // reset semua kecuali kategori
    }

    public function render()
    {
        $categories = Category::all();
        return view('livewire.form-kegiatan', [
            'categories' => $categories,
            // 'allCivilians' => $this->allCivilians,
        ]);
    }
}
