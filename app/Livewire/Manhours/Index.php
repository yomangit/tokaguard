<?php

namespace App\Livewire\Manhours;

use Carbon\Carbon;
use App\Models\Company;
use Livewire\Component;
use App\Models\Department;
use Livewire\Attributes\Validate;

class Index extends Component
{
    public $modalOpen;
    public $date;
    public $company_category;
    public $company;
    public $department;

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
        'company_category' => 'required|string',
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

    public function render()
    {

        return view('livewire.manhours.index', [
            'Departments'   => Department::all(),
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
