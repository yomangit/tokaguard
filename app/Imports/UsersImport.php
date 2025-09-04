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
        // skip kalau salah satu key utama kosong
        if (
            empty($row['email']) || strtoupper($row['email']) === 'NULL' ||
            empty($row['username']) || strtoupper($row['username']) === 'NULL' ||
            empty($row['employee_id']) || strtoupper($row['employee_id']) === 'NULL'
        ) {
            return null; // 👈 skip baris ini
        }
         if (
        User::where('email', $row['email'])->exists() ||
        User::where('username', $row['username'])->exists() ||
        User::where('employee_id', $row['employee_id'])->exists()
    ) {
        return null; // skip baris
    }
        return new User([
            'name'              => $row['name'],
            'email'             => $row['email'],
            'gender'            => $row['gender'] ?? null,
            'date_birth'        => $this->parseDate($row['date_birth'] ?? null),
            'username'          => $row['username'] ?? null,
            'department_name'   => $row['department_name'] ?? null,
            'employee_id'       => $row['employee_id'] ?? null,
            'date_commenced'    => $this->parseDate($row['date_commenced'] ?? null),
            'role_id' => $row['role_id'] ?? null,
        ]);
    }
    private function parseDate($value)
    {
        // Kalau kosong atau string "NULL" -> return null
        if (empty($value) || strtoupper($value) === 'NULL') {
            return null;
        }

        try {
            // Excel biasanya kirim dalam format yyyy-mm-dd atau numeric (serial Excel)
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null; // fallback aman
        }
    }
}
