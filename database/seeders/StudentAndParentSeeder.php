<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\ParentProfile;
use Illuminate\Support\Facades\DB;

class StudentAndParentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function() {
            // Create a classroom first
            $classroom = Classroom::create([
                'name' => 'Kelas Pelangi',
                'description' => 'Kelas untuk anak usia 4-5 tahun',
                'owner_id' => 1 // Assuming teacher ID 1 exists
            ]);

            // Create students with their parents
            $students = [
                [
                    'student' => [
                        'student_number' => 'S2024001',
                        'name' => 'Anita Putri',
                        'birth_date' => '2019-05-15',
                        'gender' => 'female',
                        'medical_history' => 'Alergi kacang'
                    ],
                    'parents' => [
                        [
                            'relation' => 'father',
                            'name' => 'Budi Santoso',
                            'phone' => '081234567890',
                            'email' => 'budi@example.com',
                            'occupation' => 'Wiraswasta',
                            'address' => 'Jl. Mawar No. 10'
                        ],
                        [
                            'relation' => 'mother',
                            'name' => 'Siti Aminah',
                            'phone' => '081234567891',
                            'email' => 'siti@example.com',
                            'occupation' => 'Guru',
                            'address' => 'Jl. Mawar No. 10'
                        ]
                    ]
                ],
                [
                    'student' => [
                        'student_number' => 'S2024002',
                        'name' => 'Dimas Pratama',
                        'birth_date' => '2019-08-20',
                        'gender' => 'male',
                        'medical_history' => null
                    ],
                    'parents' => [
                        [
                            'relation' => 'guardian',
                            'name' => 'Ratna Dewi',
                            'phone' => '081234567892',
                            'email' => 'ratna@example.com',
                            'occupation' => 'Dokter',
                            'address' => 'Jl. Melati No. 15'
                        ]
                    ]
                ],
                [
                    'student' => [
                        'student_number' => 'S2024003',
                        'name' => 'Rizki Ahmad',
                        'birth_date' => '2019-03-10',
                        'gender' => 'male',
                        'medical_history' => 'Asma ringan'
                    ],
                    'parents' => [
                        [
                            'relation' => 'father',
                            'name' => 'Ahmad Fauzi',
                            'phone' => '081234567893',
                            'email' => 'ahmad@example.com',
                            'occupation' => 'Karyawan',
                            'address' => 'Jl. Dahlia No. 5'
                        ],
                        [
                            'relation' => 'mother',
                            'name' => 'Nina Wahyuni',
                            'phone' => '081234567894',
                            'email' => 'nina@example.com', 
                            'occupation' => 'Ibu Rumah Tangga',
                            'address' => 'Jl. Dahlia No. 5'
                        ]
                    ]
                ]
            ];

            foreach ($students as $data) {
                // Create student
                $student = Student::create($data['student']);

                // Attach student to classroom
                $classroom->students()->attach($student->id);

                // Create parents/guardian
                foreach ($data['parents'] as $parentData) {
                    $student->parents()->create($parentData);
                }
            }
        });
    }
}   