<?php

namespace App\Livewire\Manhours;

use App\Models\BusinessUnit;
use Carbon\Carbon;
use App\Models\Company;
use App\Models\Contractor;
use App\Models\Custodian;
use Livewire\Component;
use App\Models\Department;
use Livewire\Attributes\Validate;
use Matrix\Operators\Division;

class Index extends Component
{
    public $modalOpen;
    public $date;
    public $entity_type;
    public $company;
    public $department;
    public $custodian=[];

    // input Supervisor
    public $manhours_supervisor;
    public $manpower_supervisor;

    // input Operational
    public $manhours_operational;
    public $manpower_operational;

    // input Administration
    public $manhours_administration;
    public $manpower_administration;

    protected $rules = [
        'date' => 'required',
        'entity_type' => 'required|string',
        'company' => 'required|string',
        'department' => 'required|string',

        'manhours_supervisor' => 'required|numeric',
        'manpower_supervisor' => 'required|numeric',
        'manhours_operational' => 'required|numeric',
        'manpower_operational' => 'required|numeric',
        'manhours_administration' => 'required|numeric',
        'manpower_administration' => 'required|numeric',
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
            $custodian = Contractor::firstWhere('contractor_name', 'LIKE', $this->company)->first()->id;
            $this->custodian = Custodian::where('contractor_id','LIKE', $custodian)->get();
        } else {
            $this->reset('department');
        }
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

        dd($bulan->format('Y/m/d'));

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
