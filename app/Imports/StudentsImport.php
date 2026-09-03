<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Cari atau buat kelas berdasarkan nama kelas di Excel
        $class = ClassModel::firstOrCreate([
            'class_name' => trim($row['kelas'])
        ]);

        // 2. Buat Akun Orang Tua (jika email ortu diisi)
        $parentId = null;
        if (!empty($row['email_ortu'])) {
            $parent = User::firstOrCreate(
                ['email' => trim($row['email_ortu'])],
                [
                    'name' => trim($row['nama_ortu']),
                    'password' => Hash::make('password123'),
                    'role' => 'parent'
                ]
            );
            $parentId = $parent->id;
        }

        // 3. Buat Akun User untuk Siswa
        $studentUser = User::create([
            'name' => trim($row['nama_siswa']),
            'email' => trim($row['email_siswa']),
            'password' => Hash::make('password123'),
            'role' => 'student'
        ]);

        // 4. Buat Data Detail Siswa
        return new Student([
            'nisn' => trim($row['nisn']),
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'parent_id' => $parentId,
            'parent_phone' => trim($row['no_wa_ortu']),
            'qr_code_token' => Str::random(32),
        ]);
    }
}