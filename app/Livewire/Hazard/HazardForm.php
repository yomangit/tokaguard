<?php

namespace App\Livewire\Hazard;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Hazard;
use App\Models\Company;
use Livewire\Component;
use App\Models\Location;
use App\Models\EventType;
use App\Models\UnsafeAct;
use App\Models\Contractor;
use App\Models\Department;
use App\Models\Likelihood;
use App\Helpers\FileHelper;
use App\Models\EventSubType;
use App\Models\ErmAssignment;
use Livewire\WithFileUploads;
use App\Events\HazardSubmitted;
use App\Models\RiskConsequence;
use App\Models\UnsafeCondition;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Notification;
use App\Notifications\HazardSubmittedNotification;

class HazardForm extends Component
{
    use WithFileUploads;
    // field tambahan tanpa aturan validasi
    public $deptCont = 'department'; // default departemen
    public $search = '';
    public $searchLocation = '';
    public $searchPelapor = '';
    public $locations = [];
    public $pelapors = [];
    public $departments = [];
    public $showDropdown = false;
    public $showLocationDropdown = false;
    public $showPelaporDropdown = false;
    public $searchContractor = '';
    public $contractors = [];
    public $showContractorDropdown = false;
    public $penanggungJawabOptions = [];
    public $likelihoods, $consequences;
    public $selectedLikelihoodId = null;
    public $selectedConsequenceId = null;
    public $status;
    #[Validate('required')]
    public $likelihood_id;
    #[Validate('required')]
    public $consequence_id;
    #[Validate('required')]
    public $location_id;
    #[Validate('required')]
    public $pelapor_id;
    #[Validate('required|string')]
    public $description;
    #[Validate('required|string')]
    public $immediate_corrective_action;
    #[Validate('required_without:contractor_id')]
    public $department_id;
    #[Validate('required_without:department_id')]
    public $contractor_id;
    #[Validate('required|string')]
    public $penanggungJawab;
    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf|max:2048')]
    public $doc_deskripsi;
    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf|max:2048')]
    public $doc_corrective;
    #[Validate('required|string')]
    public $tipe_bahaya;
    #[Validate('required|string')]
    public $sub_tipe_bahaya;
    #[Validate('required|string')]
    public $location_specific;

    #[Validate('required')]
    public $keyWord = 'kta';
    #[Validate('required_without:tindakan_tidak_aman')]
    public $kondisi_tidak_aman;
    #[Validate('required_without:kondisi_tidak_aman')]
    public $tindakan_tidak_aman;
    #[Validate('required|date')]
    public $tanggal;
    protected $messages = [

        'likelihood_id.required'     => 'likelihood wajib diisi.',
        'consequence_id.required'     => 'consequence wajib diisi.',
        'location_id.required'     => 'Lokasi wajib diisi.',
        'location_specific.required'     => 'Lokasi Spesifik wajib diisi.',

        'description.required'     => 'Deskripsi wajib diisi.',
        'description.string'       => 'Deskripsi harus berupa teks.',

        'immediate_corrective_action.required'     => 'Tindakan perbaikan langsung wajib diisi.',
        'immediate_corrective_action.string'       => 'Tindakan perbaikan langsung harus berupa teks.',

        'department_id.required_without' => 'Departemen wajib dipilih jika kontraktor tidak diisi.',
        'contractor_id.required_without' => 'Kontraktor wajib dipilih jika departemen tidak diisi.',

        'kondisi_tidak_aman.required_without' => 'Kondisi Tidak Aman wajib dipilih jika Tindakan Tidak Aman tidak diisi.',
        'tindakan_tidak_aman.required_without' => 'Tindakan Tidak Aman wajib dipilih jika Kondisi Tidak Aman tidak diisi.',

        'pelapor_id.required' => 'Pelapor wajib dipilih.',
        'penanggungJawab.required' => 'Penanggung jawab area wajib dipilih.',
        'penanggungJawab.string'   => 'Penanggung jawab harus berupa teks.',

        'tipe_bahaya.required'     => 'Tipe Bahaya wajib dipilih.',
        'tipe_bahaya.string'       => 'Tipe Bahaya harus berupa teks.',

        'sub_tipe_bahaya.required' => 'Sub Tipe Bahaya wajib dipilih.',
        'sub_tipe_bahaya.string'   => 'Sub Tipe Bahaya harus berupa teks.',

        'tanggal.required'         => 'Tanggal wajib dipilih.',
        'tanggal.date'             => 'Tanggal harus berupa format tanggal valid.',
    ];
    public function mount()
    {
        $this->likelihoods = Likelihood::orderByDesc('level')->get();
        $this->consequences = RiskConsequence::orderBy('level')->get();
    }
    public function uploadImage()
    {
        if (request()->hasFile('upload')) {
            $file     = request()->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path     = $file->storeAs('uploads/ckeditor', $filename, 'public');

            return response()->json([
                'url' => asset('storage/' . $path),
            ]);
        }
    }
    public function updated($propertyName)
    {
        $fieldsToValidate = [
            'location_id',
            'location_specific',
            'description',
            'severity',
            'department_id',
            'contractor_id',
            'penanggungJawab',
            'tanggal',
        ];
        if (in_array($propertyName, $fieldsToValidate)) {
            $this->validateOnly($propertyName);
        }
    }
    public function updatedDeptCont($value)
    {
        if ($value === 'department') {
            // Reset kontraktor jika pindah ke departemen
            $this->resetErrorBag(['contractor_id']);
            $this->reset(['contractor_id', 'searchContractor', 'contractors']);
        }
        if ($value === 'company') {
            // Reset departemen jika pindah ke kontraktor
            $this->resetErrorBag(['department_id']);
            $this->reset(['department_id', 'search', 'departments']);
        }
    }
    public function updatedKeyWord($value)
    {
        if ($value === 'kta') {
            $this->resetErrorBag(['tindakan_tidak_aman']);
            $this->reset(['tindakan_tidak_aman']);
        } elseif ($value === 'tta') {
            $this->resetErrorBag(['kondisi_tidak_aman']);
            $this->reset(['kondisi_tidak_aman']);
        }
    }
    public function updatedSearch()
    {
        if (strlen($this->search) > 1) {
            $this->departments = Department::where('department_name', 'like', '%' . $this->search . '%')
                ->orderBy('department_name')
                ->limit(10)
                ->get();
            $this->showDropdown = true;
        } else {
            $this->departments = [];
            $this->showDropdown = false;
        }
    }
    public function selectDepartment($id, $name)
    {
        $this->reset('searchContractor', 'contractor_id');
        $this->department_id = $id;
        $this->search = $name;
        $this->showDropdown = false;

        // Ambil user relasi dari departemen
        $department = Department::with('users')->find($id);
        $this->penanggungJawabOptions = $department
            ? $department->users()->select('users.id', 'users.name')->get()->toArray()
            : [];

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
        $this->reset('search', 'department_id');
        $this->contractor_id = $id;
        $this->searchContractor = $name;
        $this->showContractorDropdown = false;
        // Ambil user relasi dari contractor
        $contractor = Contractor::with('users')->find($id);
        $this->penanggungJawabOptions = $contractor
            ? $contractor->users()->select('users.id', 'users.name')->get()->toArray()
            : [];
        $this->validateOnly('contractor_id');
    }
    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 1) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')
                ->limit(10)
                ->get();
            $this->showLocationDropdown = true;
        } else {
            $this->locations = [];
            $this->showLocationDropdown = false;
        }
    }
    public function selectLocation($id, $name)
    {
        $this->location_id = $id;
        $this->searchLocation = $name;
        $this->showLocationDropdown = false;
        $this->validateOnly('location_id');
    }
    public function updatedSearchPelapor()
    {
        if (strlen($this->searchPelapor) > 1) {
            $this->pelapors = User::where('name', 'like', '%' . $this->searchPelapor . '%')
                ->orderBy('name')
                ->limit(10)
                ->get();
            $this->showPelaporDropdown = true;
        } else {
            $this->pelapors = [];
            $this->showPelaporDropdown = false;
        }
    }
    public function selectPelapor($id, $name)
    {
        $this->pelapor_id = $id;
        $this->searchPelapor = $name;
        $this->showPelaporDropdown = false;
        $this->validateOnly('pelapor_id');
    }
    public function getIsFormValidProperty()
    {
        // Kalau user pilih department_id atau contractor_id salah satu boleh
        $validCompanyDept = $this->department_id || $this->contractor_id;
        return $validCompanyDept
            && !empty($this->location)
            && !empty($this->description)
            && !empty($this->severity);
    }
    public function submit()
    {
        $this->validate();
        $docDeskripsiPath = null;
        $docCorrectivePath = null;
        // Convert tanggal ke format Y-m-d H:i:s
        $tanggal = Carbon::createFromFormat('d-m-Y H:i', $this->tanggal)->format('Y-m-d H:i:s');
        if ($this->doc_deskripsi) {
            $docDeskripsiPath = FileHelper::compressAndStore($this->doc_deskripsi, 'sebelum_perbaikan');
        }
        if ($this->doc_corrective) {
            $docCorrectivePath = FileHelper::compressAndStore($this->doc_corrective, 'sesudah_perbaikan');
        }
        // Hitung risk level (opsional: ambil dari RiskMatrixCell atau kalkulasi manual)
        $riskLevel = null;
        if ($this->consequence_id && $this->likelihood_id) {
            $riskLevel = \App\Models\RiskMatrixCell::where('likelihood_id', $this->likelihood_id)
                ->where('risk_consequence_id', $this->consequence_id)
                ->value('severity');
        }
        $hazard = Hazard::create([
            'event_type_id'          => $this->tipe_bahaya,
            'event_sub_type_id'      => $this->sub_tipe_bahaya,
            'department_id'          => $this->department_id,
            'contractor_id'          => $this->contractor_id,
            'pelapor_id'             => $this->pelapor_id,
            'penanggung_jawab_id'    => $this->penanggungJawab,
            'location_id'            => $this->location_id,
            'location_specific'      => $this->location_specific,
            'tanggal'                => $tanggal, // ✅ sudah diformat
            'description'            => $this->description,
            'doc_deskripsi'          => $docDeskripsiPath,
            'immediate_corrective_action' => $this->immediate_corrective_action,
            'doc_corrective'         => $docCorrectivePath,
            'key_word'               => $this->keyWord,
            'kondisi_tidak_aman_id'  => $this->kondisi_tidak_aman,
            'tindakan_tidak_aman_id' => $this->tindakan_tidak_aman,
            'consequence_id'         => $this->consequence_id,
            'likelihood_id'          => $this->likelihood_id,
            'risk_level'             => $riskLevel,
        ]);
        $ermUsers = ErmAssignment::where('department_id', $hazard->department_id)
            ->orWhere('contractor_id', $hazard->contractor_id)
            ->with('user')
            ->get()
            ->pluck('user');
        $this->dispatch(
            'alert',
            [
                'text' => "Laporan berhasil dikirim!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
            ]
        );
        Notification::send($ermUsers, new HazardSubmittedNotification($hazard));

        $this->resetForm(); // reset form
    }
    public function edit($likelihoodId, $consequenceId)
    {
        $this->likelihood_id = $likelihoodId;
        $this->consequence_id = $consequenceId;

        $this->selectedLikelihoodId = $this->likelihood_id;
        $this->selectedConsequenceId = $this->consequence_id;
    }

    public function render()
    {
        return view('livewire.hazard.hazard-form', [
            'Department'   => Department::all(),
            'likelihoodss' => Likelihood::orderByDesc('level')->get(),
            'consequencess' => RiskConsequence::orderBy('level')->get(),
            'Contractors'  => Contractor::all(),
            'ktas' => UnsafeCondition::latest()->get(),
            'ttas' => UnsafeAct::latest()->get(),
            'eventTypes' => EventType::where('event_type_name', 'like', '%' . 'hazard' . '%')->get(),
            'subTypes' => EventSubType::where('event_type_id', $this->tipe_bahaya)->get()

        ]);
    }
    public function resetForm()
    {
        $this->reset([
            'tipe_bahaya',
            'sub_tipe_bahaya',
            'status',
            'department_id',
            'contractor_id',
            'pelapor_id',
            'penanggungJawab',
            'location_id',
            'location_specific',
            'tanggal',
            'description',
            'doc_deskripsi',
            'immediate_corrective_action',
            'doc_corrective',
            'keyWord',
            'kondisi_tidak_aman',
            'tindakan_tidak_aman',
            'consequence_id',
            'likelihood_id',
        ]);
    }
}
