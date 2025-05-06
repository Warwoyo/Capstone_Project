<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['mother', 'father'])->get();
        return view('components.menu.student-menu', [
            'mode' => 'view',
            'studentList' => $students
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // Simpan data siswa
        $student = Student::create(Arr::only($data, [
            'name', 'nik', 'birth_date', 'gender', 'address', 'medical_history', 'group'
        ]));

        if ($request->hasFile('photo')) {
            $student->update([
                'photo' => $request->file('photo')->store('student_photos', 'public')
            ]);
        }

        // Simpan data orang tua
        $student->parents()->createMany([
            [
                'relation' => 'mother',
                'name' => $data['mother_name'],
                'nik' => $data['mother_nik'] ?? null,
                'phone' => $data['mother_phone'],
                'email' => $data['mother_email'],
                'address' => $data['mother_address'],
                'occupation' => $data['mother_job'],
            ],
            [
                'relation' => 'father',
                'name' => $data['father_name'],
                'nik' => $data['father_nik'] ?? null,
                'phone' => $data['father_phone'],
                'email' => $data['father_email'],
                'address' => $data['father_address'],
                'occupation' => $data['father_job'],
            ],
        ]);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        $student->load(['mother', 'father']);
        $students = Student::with(['mother', 'father'])->get();

        return view('components.menu.student-menu', [
            'mode' => 'edit',
            'studentList' => $students,
            'student' => $student
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $data = $this->validateData($request);

        // Update data siswa
        $student->update(Arr::only($data, [
            'name', 'nik', 'birth_date', 'gender', 'address', 'medical_history', 'group'
        ]));

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $student->update([
                'photo' => $request->file('photo')->store('student_photos', 'public')
            ]);
        }

        // Update atau buat data orang tua
        foreach (['mother', 'father'] as $relation) {
            $student->parents()->updateOrCreate(
                ['relation' => $relation],
                [
                    'name' => $data["{$relation}_name"],
                    'nik' => $data["{$relation}_nik"] ?? null,
                    'phone' => $data["{$relation}_phone"],
                    'email' => $data["{$relation}_email"],
                    'address' => $data["{$relation}_address"],
                    'occupation' => $data["{$relation}_job"],
                ]
            );
        }

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();
        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            // Data anak
            'name' => 'required|string|max:100',
            'nik' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'group' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',

            // Ibu
            'mother_name' => 'required|string|max:100',
            'mother_nik' => 'nullable|string|max:20',
            'mother_phone' => 'nullable|string|max:20',
            'mother_email' => 'nullable|email|max:100',
            'mother_address' => 'nullable|string',
            'mother_job' => 'nullable|string|max:100',

            // Ayah
            'father_name' => 'required|string|max:100',
            'father_nik' => 'nullable|string|max:20',
            'father_phone' => 'nullable|string|max:20',
            'father_email' => 'nullable|email|max:100',
            'father_address' => 'nullable|string',
            'father_job' => 'nullable|string|max:100',
        ]);
    }
}
