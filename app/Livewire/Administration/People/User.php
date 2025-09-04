<?php

namespace App\Livewire\Administration\People;

use App\Models\User as UserProfile;
use Livewire\Component;
use Livewire\WithPagination;

class User extends Component
{
     use WithPagination;

    public $userId;
    public $name, $gender, $date_birth, $username, $department_name, $employee_id, $date_commenced, $email;

    public $showModal = false;
    public $showDeleteModal = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:Male,Female',
            'date_birth' => 'nullable|date',
            'username' => 'required|string|max:255|unique:user_profiles,username,' . $this->userId,
            'department_name' => 'nullable|string|max:255',
            'employee_id' => 'required|string|max:255|unique:user_profiles,employee_id,' . $this->userId,
            'date_commenced' => 'nullable|date',
            'email' => 'required|email|max:255|unique:user_profiles,email,' . $this->userId,
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

    public function render()
    {
        return view('livewire.administration.people.user',[
              'users' => UserProfile::paginate(10),
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
                'department_name' => $this->department_name,
                'employee_id' => $this->employee_id,
                'date_commenced' => $this->date_commenced,
                'email' => $this->email,
            ]
        );

        $this->resetInput();
        $this->showModal = false;
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
    }

    private function resetInput()
    {
        $this->reset(['userId', 'name', 'gender', 'date_birth', 'username', 'department_name', 'employee_id', 'date_commenced', 'email']);
    }
}
