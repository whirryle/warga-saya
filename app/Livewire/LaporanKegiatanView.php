<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Civilian;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class LaporanKegiatanView extends Component
{
    use WithPagination;                 // ← pakai paginasi
    protected $paginationTheme = 'tailwind'; // atau 'bootstrap'

    // properti untuk menampung filter
    public $selectedCategory = '';
    public $searchName       = '';
    public $perPage = 5;

    // dipanggil saat tombol "Terapkan Filter" diklik
    public function applyFilter()
    {
        // kosong saja—Livewire akan otomatis re-render dengan nilai deferred model
    }

    // reset halaman ketika jumlah data per halaman diubah
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    private function getKeterangan($progress, $target)
    {
        if ($target <= 0) {
            return ['label' => 'N/A', 'color' => 'bg-gray-200 text-gray-700'];
        }

        $percentage = ($progress / $target) * 100;

        return match (true) {
            $percentage >= 100 => ['label' => 'B', 'color' => 'bg-blue-600 text-white'],
            default => ['label' => 'KB', 'color' => 'bg-red-500 text-white'],
        };
    }

    public function render()
{
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
    $categories = Category::all();

        // 1) bangun query dasar
        $query = Civilian::with(['categories.activities','activities']);

        // 2> Jika bukan super admin, batasi hanya menampilkan warga
        // yang terhubung dengan kegiatan yang diurus oleh user yang login
        if ($user && !$user->isSuperAdmin()) {
            $query->whereHas('activities', function($q) use ($user) {
                $q->WhereIn('activites.id', $user->activites()->pluck('id'));
            });
        }

        // 3) filter
        if ($this->selectedCategory) {
            $query->whereHas('categories', fn($q) =>
                $q->where('categories.id', $this->selectedCategory)
            );
        }
        if ($this->searchName) {
            $query->where('full_name','like',"%{$this->searchName}%");
        }

        // 4) Paginate
        $paginator = $query->paginate($this->perPage)
                           ->appends([
                              'selectedCategory' => $this->selectedCategory,
                              'searchName'       => $this->searchName,
                           ]);

        // 5) Transform items in the current page
        $paginator->getCollection()->transform(function($civ) {
            $cats = $civ->categories->isEmpty()
                ? collect([(object)['id'=>null,'name'=>'-','activities'=>collect()]])
                : $civ->categories;

            $categoriesData = $cats->map(function($cat) use ($civ) {
                $allActs      = $cat->activities;
                $pivotMapping = $civ->activities->pluck('pivot.progress','id');

                $acts = $allActs->map(function($a) use ($pivotMapping) {
                    $prog = $pivotMapping->get($a->id, 0);
                    return [
                        'name'       => $a->name,
                        'progress'   => $prog ?: '-',
                        'target'     => $a->target,
                        'keterangan' => $this->getKeterangan($prog, $a->target),
                    ];
                });

                if ($acts->isEmpty()) {
                    $acts = collect([[
                        'name'       => '-',
                        'progress'   => '-',
                        'target'     => '-',
                        'keterangan' => ['label' => 'KB', 'color' => 'bg-red-500 text-white'],
                    ]]);
                }

                return [
                    'name'       => $cat->name,
                    'activities' => $acts->values(),
                ];
            })->values();

            return [
                'full_name'  => $civ->full_name,
                'categories' => $categoriesData,
            ];
        });

        return view('livewire.laporan-kegiatan-view', [
            'categories' => $categories,
            'data'       => $paginator,
        ]);
    }

}



