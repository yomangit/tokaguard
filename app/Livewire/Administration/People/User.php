<?php

namespace App\Livewire\Administration\People;

use App\Models\Role;
use Livewire\Component;
use App\Imports\UsersImport;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\User as UserProfile;
use Maatwebsite\Excel\Facades\Excel;

class User extends Component
{
    use WithPagination, WithFileUploads;

    public $userId;
    public $name, $gender, $date_birth, $username, $department_name, $employee_id, $date_commenced, $email, $role_id;
    public $showModal = false;
    public $showDeleteModal = false;
    public $showImportModal = false; // 🔹 untuk modal import
    public $file;
    // Property untuk menampilkan hasil
    public $importedCount = 0;
    public $skippedCount = 0;
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:L,P',
            'date_birth' => 'nullable|date',
            'role_id' => 'nullable',
            'username' => 'required|string|max:255|unique:users,username,' . $this->userId,
            'department_name' => 'nullable|string|max:255',
            'employee_id' => 'required|string|max:255|unique:users,employee_id,' . $this->userId,
            'date_commenced' => 'nullable|date',
            'email' => 'required|email|max:255|unique:users,email,' . $this->userId,
        ];
    }
    protected function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'employee_id.required' => 'Employee ID wajib diisi.',
            'employee_id.unique' => 'Employee ID sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'date_birth.date' => 'Tanggal lahir harus berupa format tanggal.',
            'date_commenced.date' => 'Tanggal mulai kerja harus berupa format tanggal.',
        ];
    }
    // 🔹 Jalankan validasi realtime
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:2048',
        ], [
            'file.required' => 'File wajib diunggah.',
            'file.mimes'    => 'Format file harus xlsx, csv, atau xls.',
            'file.max'      => 'Ukuran file maksimal 2MB.',
        ]);
        $imported = 0;
        $skipped = 0;
        $rows = Excel::toCollection(new UsersImport, $this->file->getRealPath());
        foreach ($rows[0] as $row) {
            $user = (new UsersImport)->model($row->toArray());
            if ($user) {
                $user->save();
                $imported++;
            } else {
                $skipped++;
            }
        }

        $this->importedCount = $imported;
        $this->skippedCount = $skipped;

        $this->reset('file');
        $this->showImportModal = false;

        session()->flash('success', "$imported row berhasil diimport, $skipped row dilewati (kosong/duplikat).");
        $this->dispatch(
            'alert',
            [
                'text' => "Data user berhasil diimport!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
            ]
        );
    }


    public function render()
    {
        return view('livewire.administration.people.user', [
            'users' => UserProfile::paginate(10),
            'role' => Role::all()
        ]);
    }
    public function create()
    {
        $this->resetInput();
        $this->showModal = true;
    }
    public function edit($id)
    {
        $user = UserProfile::findOrFail($id);
        $this->fill($user->toArray());
        $this->userId = $user->id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        UserProfile::updateOrCreate(
            ['id' => $this->userId],
            [
                'name' => $this->name,
                'gender' => $this->gender,
                'date_birth' => $this->date_birth,
                'username' => $this->username,
                'role_id' => $this->role_id,
                'department_name' => $this->department_name,
                'employee_id' => $this->employee_id,
                'date_commenced' => $this->date_commenced,
                'email' => $this->email,
            ]
        );

        $this->resetInput();
        $this->showModal = false;
        $text = $this->userId ? 'user berhasil diupdate!' : 'user berhasil ditambahkan!';
        $this->dispatch(
            'alert',
            [
                'text' =>  $text,
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
            ]
        );
    }

    public function confirmDelete($id)
    {
        $this->userId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        UserProfile::findOrFail($this->userId)->delete();
        $this->resetInput();
        $this->showDeleteModal = false;
        $this->dispatch(
            'alert',
            [
                'text' => "User berhasil dihapus!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "background: linear-gradient(135deg, #f44336, #d32f2f);",
            ]
        );
    }

    private function resetInput()
    {
        $this->reset(['userId', 'name', 'gender', 'date_birth', 'username', 'role_id', 'department_name', 'employee_id', 'date_commenced', 'email']);
    }
}
