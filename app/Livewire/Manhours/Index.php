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
    public $selectedId = null;
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

    public function open_modal($id = null)
    {
        $this->modalOpen = 'modal-open';

        if ($id) {
            $this->selectedId = $id;
            $data = Manhour::findOrFail($id);

            $this->date        = Carbon::parse($data->date)->format('m-Y');
            $this->entity_type = strtolower($data->company_category) === "contractor" ? "contractor" : "owner";
            $this->company     = $data->company;
            $this->department  = $data->department;
            $this->dept_group  = $data->dept_group;

            // reset dulu
            foreach ($this->jobclasses as $key => $label) {
                $this->hide[$key]     = true;
                $this->manhours[$key] = null;
                $this->manpower[$key] = null;
            }

            // isi sesuai data yg ada di DB
            $manhoursData = Manhour::where('date', $data->date)
                ->where('company', $data->company)
                ->where('department', $data->department)
                ->where('dept_group', $data->dept_group)
                ->get();

            foreach ($manhoursData as $row) {
                $key = array_search($row->job_class, $this->jobclasses);
                if ($key !== false) {
                    $this->hide[$key]     = false;
                    $this->manhours[$key] = $row->manhours;
                    $this->manpower[$key] = $row->manpower;
                }
            }
        }
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
            'departemen' => Department::get(),
            'Companies' => Company::get(),
            'data_manhours'  => Manhour::paginate(30),
        ]);
    }

    private function saveManhours($mode = 'create', $id = null)
    {
        $this->validate();

        $company_category = $this->entity_type === "contractor"
            ? 'Contractor'
            : 'PT. Archi Indonesia';

        $bulan = Carbon::createFromFormat('m-Y', $this->date)->startOfMonth();

        foreach ($this->jobclasses as $key => $label) {
            $query = Manhour::where('date', $bulan->format('Y/m/d'))
                ->where('company', $this->company)
                ->where('department', $this->department)
                ->where('dept_group', $this->dept_group)
                ->where('job_class', $label);

            // 🔹 Jika checkbox dicentang atau kosong → hapus record lama
            if ($this->hide[$key] || (empty($this->manhours[$key]) && empty($this->manpower[$key]))) {
                $query->delete();
                continue;
            }

            // 🔹 Kalau create → buat baru
            if ($mode === 'create') {
                Manhour::create([
                    'date'             => $bulan->format('Y/m/d'),
                    'company_category' => $company_category,
                    'company'          => $this->company,
                    'department'       => $this->department,
                    'dept_group'       => $this->dept_group,
                    'job_class'        => $label,
                    'manhours'         => $this->manhours[$key],
                    'manpower'         => $this->manpower[$key],
                ]);
            }

            // 🔹 Kalau update → updateOrCreate
            if ($mode === 'update') {
                Manhour::updateOrCreate(
                    [
                        'id'               => $this->selectedId,
                    ],
                    [
                        'date'             => $bulan->format('Y/m/d'),
                        'company_category' => $company_category,
                        'company'          => $this->company,
                        'department'       => $this->department,
                        'dept_group'       => $this->dept_group,
                        'job_class'        => $label,
                        'manhours'         => $this->manhours[$key],
                        'manpower'         => $this->manpower[$key],
                    ]
                );
            }
        }

        $this->dispatch('alert', [
            'text'            => $mode === 'create' ? "Data berhasil di input!!!" : "Data berhasil diperbarui!!!",
            'duration'        => 5000,
            'destination'     => '/contact',
            'newWindow'       => true,
            'close'           => true,
            'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
        ]);
    }

    public function store()
    {
        $this->saveManhours('create');
    }

    public function update($id)
    {
        $this->saveManhours('update', $id);
    }
    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }
}
