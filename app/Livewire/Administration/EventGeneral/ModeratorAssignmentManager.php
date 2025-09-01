<?php

namespace App\Livewire\Administration\EventGeneral;

use App\Models\User;
use App\Models\Company;
use Livewire\Component;
use App\Models\Contractor;
use App\Models\Department;
use Livewire\Attributes\Validate;
use App\Models\ModeratorAssignment;

class ModeratorAssignmentManager extends Component
{
    #[Validate('required_without:contractor_id')]
    public $department_id;
    #[Validate('required_without:department_id')]
    public $contractor_id;
    public  $company_id;
    public $assignments, $search = '';
    public $status = 'department'; // default departemen
    public $users = [], $showMpderatorDropdown = false, $searchModerator = '';
    public $departments = [], $showDepartemenDropdown = false, $searchDepartemen = '';
    public $contractors = [], $showContractorDropdown = false, $searchContractor = '';
    #[Validate('required')]
    public $user_id;
    protected $messages =
    [
        'user_id.required'               => 'Nama Moderator wajib diisi.',
        'department_id.required_without' => 'Departemen wajib dipilih jika kontraktor tidak diisi.',
        'contractor_id.required_without' => 'Kontraktor wajib dipilih jika departemen tidak diisi.',
    ];
    public function mount()
    {
        $this->loadAssignments();
    }
    public function updatedSearch()
    {
        $this->loadAssignments();
    }
    public function loadAssignments()
    {
        $query  = ModeratorAssignment::with(['user', 'department', 'contractor', 'company']);
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }
        $this->assignments = $query->get();
    }
    public function updatedStatus($value)
    {
        if ($value === 'department') {
            // Reset kontraktor jika pindah ke departemen
            $this->resetErrorBag(['contractor_id']);
            $this->reset(['contractor_id', 'searchContractor', 'contractors']);
        }
        if ($value === 'company') {
            // Reset departemen jika pindah ke kontraktor
            $this->resetErrorBag(['department_id']);
            $this->reset(['department_id', 'searchDepartemen', 'departments']);
        }
    }
    public function updatedSearchModerator()
    {
        if (strlen($this->searchModerator) > 1) {
            $this->users = User::where('name', 'like', '%' . $this->searchModerator . '%')
                ->orderBy('name')
                ->limit(10)
                ->get();
            $this->showMpderatorDropdown = true;
        } else {
            $this->users = [];
            $this->showMpderatorDropdown = false;
        }
    }
    public function selectModerator($id, $name)
    {
        $this->reset('searchContractor', 'contractor_id');
        $this->user_id = $id;
        $this->searchModerator = $name;
        $this->showMpderatorDropdown = false;
        $this->validateOnly('user_id');
    }
    public function updatedSearchDepartemen()
    {
        if (strlen($this->searchDepartemen) > 1) {
            $this->departments = Department::where('department_name', 'like', '%' . $this->searchDepartemen . '%')
                ->orderBy('department_name')
                ->limit(10)
                ->get();
            $this->showDepartemenDropdown = true;
        } else {
            $this->departments = [];
            $this->showDepartemenDropdown = false;
        }
    }
    public function selectDepartment($id, $name)
    {
        $this->department_id = $id;
        $this->searchDepartemen = $name;
        $this->showDepartemenDropdown = false;
        $this->validateOnly('department_id');
    }
    public function updatedSearchContractor()
    {
        if (strlen($this->searchContractor) > 1) {
            $this->contractors = Contractor::query()
                ->where('contractor_name', 'like', '%' . $this->searchContractor . '%')
                ->orderBy('contractor_name')
                ->limit(10)
                ->get();
            $this->showContractorDropdown = true;
        } else {
            $this->contractors = [];
            $this->showContractorDropdown = true;
        }
    }
    public function selectContractor($id, $name)
    {
        $this->reset('searchDepartemen', 'department_id');
        $this->contractor_id = $id;
        $this->searchContractor = $name;
        $this->showContractorDropdown = false;
        $this->validateOnly('contractor_id');
    }
    public function assign()
    {
        $this->validate();
        // Cegah duplikasi per level
        $exists = ModeratorAssignment::where('user_id', $this->user_id)
            ->where(function ($q) {
                $q->where('department_id', $this->department_id)
                    ->orWhere('contractor_id', $this->contractor_id)
                    ->orWhere('company_id', $this->company_id);
            })->exists();

        if ($exists) {
            session()->flash('error', 'User sudah ditetapkan sebagai moderator di level ini.');
            return;
        }
        ModeratorAssignment::create([
            'user_id' => $this->user_id,
            'department_id' => $this->department_id,
            'contractor_id' => $this->contractor_id,
            'company_id' => $this->company_id,
        ]);
        $this->reset(['user_id', 'department_id', 'contractor_id', 'company_id']);
        $this->loadAssignments();
        $this->dispatch(
            'alert',
            [
                'text'            => 'Moderator berhasil ditetapkan.',
                'duration'        => 5000,
                'destination'     => '/contact',
                'newWindow'       => true,
                'close'           => true,
                'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
            ]
        );
    }
    public function delete($id)
    {
        ModeratorAssignment::findOrFail($id)->delete();
        $this->loadAssignments();
    }
    public function render()
    {
        return view('livewire.administration.event-general.moderator-assignment-manager', [
            'companies' => Company::all(),
            'contractors' => Contractor::all(),
        ]);
    }
}
