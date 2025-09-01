<?php

namespace App\Livewire\Manhours;

use App\Models\Company;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Index extends Component
{
    public $modalOpen;
    // fild
    #[Validate('required', message: 'kolom tanggal tidak boleh kosong!!!')]
    public $date;
    public $tgl;
    public $search_company, $company_id, $company_name;
    public function open_modal()
    {
        $this->modalOpen = 'modal-open';
    }
    public function close_modal()
    {
        $this->reset('modalOpen');
    }
    public function id_company(Company $id)
    {
        $this->company_id = $id->id;
        $this->company_name = $id->company_name;
    }
    public function render()
    {
        if (empty($this->company_id)) {

            $this->company_name = "Semua Perusahaan";
        }

        $this->tgl = $this->date;
        return view('livewire.manhours.index', [
            'Companies' => Company::search(trim($this->search_company))->get()
        ]);
    }
    public function store()
    {
        $this->validate();
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
