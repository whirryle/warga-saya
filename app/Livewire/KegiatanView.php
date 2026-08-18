<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Civilian;

class KegiatanView extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public $perPage = 5;
    public $selectedCategory = '';
    public $searchName       = '';

    public function updatedSelectedCategory() { $this->resetPage(); }
    public function updatedSearchName()       { $this->resetPage(); }
    public function updatedPerPage()          { $this->resetPage(); }

    public function render()
    {
        $categories = Category::all();

        // 1) QueryBuilder (eager‐load kategori→activities + pivot activities)
        $query = Civilian::with(['categories.activities','activities']);

        if ($this->selectedCategory) {
            $query->whereHas('categories', fn($q) =>
                $q->where('categories.id', $this->selectedCategory)
            );
        }
        if ($this->searchName) {
            $query->where('full_name','like',"%{$this->searchName}%");
        }

        // 2) Paginate + appends (jaga filter di URL)
        $paginator = $query
            ->paginate($this->perPage)
            ->appends([
                'selectedCategory' => $this->selectedCategory,
                'searchName'       => $this->searchName,
            ]);

        // 3) Ambil raw items dan map jadi struktur:
        //    [ full_name, categories => [ name, activities => [name,progress,target] ] ]
        $raw = collect($paginator->items())->map(function($civ) {
            $cats = $civ->categories->isEmpty()
                ? collect([(object)[ 'id'=>null,'name'=>'-','activities'=>collect() ]])
                : $civ->categories;

            $catsData = $cats->map(function($cat) use ($civ) {
                $allActs      = $cat->activities;
                $pivotMapping = $civ->activities->pluck('pivot.progress','id');

                $acts = $allActs->map(function($a) use ($pivotMapping) {
                    $prog = $pivotMapping->get($a->id, 0);
                    return [
                        'name'     => $a->name,
                        'progress' => $prog ?: '-',
                        'target'   => $a->target,
                    ];
                });

                if ($acts->isEmpty()) {
                    $acts = collect([[
                        'name'     => '-',
                        'progress' => '-',
                        'target'   => '-',
                    ]]);
                }

                return [
                    'name'       => $cat->name,
                    'activities' => $acts->values(),
                ];
            })->values();

            return [
                'full_name'  => $civ->full_name,
                'categories' => $catsData,
            ];
        });

        // 4) Bungkus lagi jadi paginator agar links() tetap working
        $data = new \Illuminate\Pagination\LengthAwarePaginator(
            $raw->all(),
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            [
                'path'  => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('livewire.kegiatan-view', [
            'categories'   => $categories,
            'data'         => $data,
        ]);
    }
}
