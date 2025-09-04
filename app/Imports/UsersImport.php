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
        // skip kalau email kosong
        if (empty($row['email']) || strtoupper($row['email']) === 'NULL') {
            return null;
        }

        return new User([
            'name'             => $row['name'],
            'email'            => $this->uniqueEmail($row['email']),
            'gender'           => $row['gender'] ?? null,
            'date_birth'       => $this->parseDate($row['date_birth'] ?? null),
            'username'         => $this->uniqueUsername($row['username'] ?? null),
            'department_name'  => $row['department_name'] ?? null,
            'employee_id'      => $this->uniqueEmployeeId($row['employee_id'] ?? null),
            'date_commenced'   => $this->parseDate($row['date_commenced'] ?? null),
            'role_id'          => $row['role_id'] ?? null,
        ]);
    }

    private function parseDate($value)
    {
        if (empty($value) || strtoupper($value) === 'NULL') {
            return null;
        }
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function uniqueEmail($email)
    {
        if (empty($email) || strtoupper($email) === 'NULL') {
            // generate dummy unik
            $email = 'user' . uniqid() . '@dummy.local';
        }

        // kalau sudah ada di DB → tambahin uniqid
        if (User::where('email', $email)->exists()) {
            $parts = explode('@', $email);
            $email = $parts[0] . '_' . uniqid() . '@' . $parts[1];
        }

        return $email;
    }

    private function uniqueUsername($username)
    {
        if (empty($username) || strtoupper($username) === 'NULL') {
            $username = 'user' . uniqid();
        }

        if (User::where('username', $username)->exists()) {
            $username .= '_' . uniqid();
        }

        return $username;
    }

    private function uniqueEmployeeId($employeeId)
    {
        if (empty($employeeId) || strtoupper($employeeId) === 'NULL') {
            $employeeId = uniqid('emp_');
        }

        if (User::where('employee_id', $employeeId)->exists()) {
            $employeeId .= '_' . uniqid();
        }

        return $employeeId;
    }
}
