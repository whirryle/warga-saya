<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;

class LogAktivitasView extends Component
{
    use WithPagination;

    public $selectedUser = '';
    public $selectedRole = '';
    public $selectedLog = '';
    public $selectedEvent = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 10;

    public $appliedUser = '';
    public $appliedRole = '';
    public $appliedLog = '';
    public $appliedEvent = '';
    public $appliedDateFrom = '';
    public $appliedDateTo = '';

    protected $queryString = ['perPage'];

    public function applyFilter()
    {
        $this->appliedUser = $this->selectedUser;
        $this->appliedRole = $this->selectedRole;
        $this->appliedLog = $this->selectedLog;
        $this->appliedEvent = $this->selectedEvent;
        $this->appliedDateFrom = $this->dateFrom;
        $this->appliedDateTo = $this->dateTo;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'selectedUser', 'selectedRole', 'selectedLog', 'selectedEvent',
            'dateFrom', 'dateTo',
            'appliedUser', 'appliedRole', 'appliedLog', 'appliedEvent',
            'appliedDateFrom', 'appliedDateTo',
        ]);
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = Activity::query()
            ->with('causer')
            ->when($this->appliedUser, function ($query) {
                $query->where('causer_id', $this->appliedUser);
            })
            ->when($this->appliedRole, function ($query) {
                $query->where('role', $this->appliedRole);
            })
            ->when($this->appliedLog, function ($query) {
                $query->where('log_name', $this->appliedLog);
            })
            ->when($this->appliedEvent, function ($query) {
                $query->where('event', $this->appliedEvent);
            })
            ->when($this->appliedDateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->appliedDateFrom);
            })
            ->when($this->appliedDateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->appliedDateTo);
            })
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $users = \App\Models\User::orderBy('name')->get(['id', 'name', 'role']);
        $logNames = Activity::distinct()->pluck('log_name')->unique()->sort()->values();
        $events = Activity::distinct()->pluck('event')->filter()->unique()->sort()->values();
        $roles = DB::table('activity_log')->distinct()->pluck('role')->filter()->unique()->sort()->values();

        return view('livewire.log-aktivitas-view', [
            'logs' => $logs,
            'users' => $users,
            'logNames' => $logNames,
            'events' => $events,
            'roles' => $roles,
        ]);
    }
}