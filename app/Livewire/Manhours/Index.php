<?php

namespace App\Livewire\Manhours;

use Carbon\Carbon;
use App\Models\Company;
use App\Models\Manhour;
use Livewire\Component;
use App\Models\Custodian;
use App\Models\Contractor;
use App\Models\Department;
use App\Models\BusinessUnit;
use App\Models\Department_group;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $modalOpen;
    public $custodian = [];
    public $deptGroup = [];

    // input umum
    public $date;
    public $entity_type;
    public $company;
    public $department;
    public $dept_group;

    // jobclass setup
    public $jobclasses = [
        'supervisor'     => 'Supervisor',
        'operational'    => 'Operational',
        'administration' => 'Administration',
    ];

    public $hide = [
        'supervisor'     => false,
        'operational'    => false,
        'administration' => false,
    ];

    public $manhours = [
        'supervisor'     => null,
        'operational'    => null,
        'administration' => null,
    ];

    public $manpower = [
        'supervisor'     => null,
        'operational'    => null,
        'administration' => null,
    ];

    protected function rules()
    {
        $rules = [
            'date'        => 'required',
            'entity_type' => 'required|string',
            'company'     => 'required|string',
            'department'  => 'required|string',
        ];

        foreach ($this->jobclasses as $key => $label) {
            $rules["manhours.$key"] = $this->hide[$key] ? 'nullable' : 'required|numeric|min:0';
            $rules["manpower.$key"] = $this->hide[$key] ? 'nullable' : 'required|numeric|min:0';
        }

        return $rules;
    }

    public function open_modal()
    {
        $this->modalOpen = 'modal-open';
    }

    public function close_modal()
    {
        $this->reset('modalOpen');
    }

    public function updatedCompany()
    {
        if ($this->entity_type === "contractor") {
            $custodian = Contractor::where('contractor_name', 'LIKE', $this->company)->first()->id ?? null;
            $this->custodian = Custodian::where('contractor_id', $custodian)->get();
        } elseif ($this->entity_type === "owner") {
            $this->deptGroup = Department_group::get();
        } else {
            $this->reset('department');
        }
    }

    public function updatedDepartment()
    {
        $dept_id = Department::where('department_name', 'LIKE', $this->department)->value('id') ?? null;
        $this->dept_group = Department_group::where('department_id', $dept_id)->first()->Group->group_name ?? null;
    }

    public function render()
    {
        return view('livewire.manhours.index', [
            'bu'        => BusinessUnit::all(),
            'cont'      => Contractor::all(),
            'departemen'=> Department::get(),
            'Companies' => Company::get(),
            'manhours'  => Manhour::paginate(30),
        ]);
    }

    public function store()
    {
        $this->validate();

        $company_category = $this->entity_type === "contractor" ? 'Contractor' : 'PT. Archi Indonesia';
        $bulan = Carbon::createFromFormat('m-Y', $this->date)->startOfMonth();

        foreach ($this->jobclasses as $key => $label) {
            Manhour::create([
                'date'             => $bulan->format('Y/m/d'),
                'company_category' => $company_category,
                'company'          => $this->company,
                'department'       => $this->department,
                'dept_group'       => $this->dept_group,
                'job_class'        => $label,
                'manhours'         => $this->hide[$key] ? null : $this->manhours[$key],
                'manpower'         => $this->hide[$key] ? null : $this->manpower[$key],
            ]);
        }

        $this->dispatch('alert', [
            'text'            => "Data berhasil di input!!!",
            'duration'        => 5000,
            'destination'     => '/contact',
            'newWindow'       => true,
            'close'           => true,
            'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
        ]);
    }

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }
}
