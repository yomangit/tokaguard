<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'name'              => $row['name'],
            'email'             => $row['email'],
            'gender'            => $row['gender'] ?? null,
            'date_birth'        => $row['date_birth'] ?? null,
            'username'          => $row['username'] ?? null,
            'department_name'   => $row['department_name'] ?? null,
            'employee_id'       => $row['employee_id'] ?? null,
            'date_commenced'    => $row['date_commenced'] ?? null,
            'role_id' => $row['role_id'] ?? null,
        ]);
    }
}
