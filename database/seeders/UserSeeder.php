<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TeacherProfile;
use App\Models\ParentProfile;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /* =========================
             | 1. ADMIN
             * =======================*/
            User::create([
                'name'         => 'Super Admin',
                'phone_number' => '081100000001',
                'password'     => Hash::make('admin123'),
                'role'         => 'admin',
            ]);

            /* =========================
             | 2. GURU
             * =======================*/
            $teacher = User::create([
                'name'         => 'Bu Rina',
                'phone_number' => '081100000002',
                'password'     => Hash::make('guru123'),
                'role'         => 'teacher',
            ]);

            // profil guru (relasi hasOne)
            TeacherProfile::create([
                'user_id' => $teacher->id,
                'nip'     => 'T-001',
                'address' => 'Jl. Mawar No. 1',
            ]);

            /* =========================
             | 3. ORANG TUA
             * =======================*/
            $parent = User::create([
                'name'         => 'Pak Budi',
                'phone_number' => '081100000003',
                'password'     => Hash::make('ortu123'),
                'role'         => 'parent',
            ]);

            // profil orang-tua (relasi hasOne)
            ParentProfile::create([
                'user_id' => $parent->id,
                'address' => 'Jl. Melati No. 9',
            ]);
        });
    }
}
