<?php

namespace App\Livewire\Manhours;

use App\Models\BusinessUnit;
use Carbon\Carbon;
use App\Models\Company;
use App\Models\Contractor;
use App\Models\Custodian;
use Livewire\Component;
use App\Models\Department;
use App\Models\Department_group;
use Livewire\Attributes\Validate;
use Matrix\Operators\Division;

class Index extends Component
{
    public $modalOpen;
    public $custodian = [];
    public $deptGroup = [];
    // input umum
    #[Validate('required|date')]
    public $date;
    #[Validate('required|string')]
    public $entity_type;
    #[Validate('required|string')]
    public $company;
    #[Validate('required|string')]
    public $department;
    public $dept_group;
    // input Supervisor
    #[Validate('required|numeric')]
    public $manhours_supervisor;
    #[Validate('required|numeric')]
    public $manpower_supervisor;
    // input Operational
    #[Validate('required|numeric')]
    public $manhours_operational;
    #[Validate('required|numeric')]
    public $manpower_operational;
    // input Administration
    #[Validate('required|numeric')]
    public $manhours_administration;
    #[Validate('required|numeric')]
    public $manpower_administration;

    // 🔹 Custom messages
    protected $messages = [
        'date.required' => 'Tanggal wajib diisi.',
        'date.date' => 'Format tanggal tidak valid.',

        'entity_type.required' => 'Tipe entitas wajib dipilih.',
        'company.required' => 'Perusahaan wajib diisi.',
        'department.required' => 'Departemen wajib diisi.',

        'manhours_supervisor.required' => 'Manhours Supervisor wajib diisi.',
        'manhours_supervisor.numeric'  => 'Manhours Supervisor harus berupa angka.',

        'manpower_supervisor.required' => 'Manpower Supervisor wajib diisi.',
        'manpower_supervisor.numeric'  => 'Manpower Supervisor harus berupa angka.',

        'manhours_operational.required' => 'Manhours Operational wajib diisi.',
        'manhours_operational.numeric'  => 'Manhours Operational harus berupa angka.',

        'manpower_operational.required' => 'Manpower Operational wajib diisi.',
        'manpower_operational.numeric'  => 'Manpower Operational harus berupa angka.',

        'manhours_administration.required' => 'Manhours Administration wajib diisi.',
        'manhours_administration.numeric'  => 'Manhours Administration harus berupa angka.',

        'manpower_administration.required' => 'Manpower Administration wajib diisi.',
        'manpower_administration.numeric'  => 'Manpower Administration harus berupa angka.',
    ];
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
            $custodian = Contractor::where('contractor_name', 'LIKE', $this->company)->first()->id;
            $this->custodian = Custodian::where('contractor_id', 'LIKE', $custodian)->get();
        } elseif ($this->entity_type === "owner") {
            $this->deptGroup = Department_group::get();
        } else {
            $this->reset('department');
        }
    }
    public function updatedDepartment(){
        $dept_id = Department::where('department_name', 'LIKE', $this->department)->value('id') ?? null;
        $this->dept_group = Department_group::where('department_id',$dept_id)->first()->Group->group_name;
    }
    public function render()
    {

        return view('livewire.manhours.index', [
            'bu'   => BusinessUnit::all(),
            'cont' => Contractor::all(),
            'departemen' => Department::get(),
            'Companies' => Company::get(),
        ]);
    }
    public function store()
    {
        $this->validate();
        $bulan = Carbon::createFromFormat('m-Y', $this->date)->startOfMonth();
        $data = [
            [
                'job_class' => 'Supervisor',
                'manhours'  => $this->manhours_supervisor,
                'manpower'  => $this->manpower_supervisor,
            ],
            [
                'job_class' => 'Operational',
                'manhours'  => $this->manhours_operational,
                'manpower'  => $this->manpower_operational,
            ],
            [
                'job_class' => 'Administration',
                'manhours'  => $this->manhours_administration,
                'manpower'  => $this->manpower_administration,
            ],
        ];

        foreach ($data as $row) {
            Manhour::create([
                'date'             => $bulan->format('Y/m/d'),
                'company_category' => $this->company_category,
                'company'          => $this->company,
                'department'       => $this->department,
                'dept_group'       => $this->dept_group,
                'job_class'        => $row['job_class'],
                'manhours'         => $row['manhours'],
                'manpower'         => $row['manpower'],
            ]);
        }
        $this->dispatch(
            'alert',
            [
                'text' => "Data berhasil di input!!!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
            ]
        );
    }
}
