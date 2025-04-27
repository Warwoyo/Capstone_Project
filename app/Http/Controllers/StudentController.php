<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\RegistrationToken;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function store(Request $r)
    {
        // validasi input
        $r->validate([
            'name'       => ['required','string','max:100'],
            'birth_date' => ['required','date'],
            'gender'     => ['required','in:male,female'],
        ]);

        // buat siswa
        $student = Student::create($r->only('name','birth_date','gender'));

        // generate token unik 8 karakter
        do {
            $token = Str::upper(Str::random(8));
        } while (RegistrationToken::where('token', $token)->exists());

        // simpan token
        RegistrationToken::create([
            'student_id' => $student->id,
            'token'      => $token,
            'expires_at' => now()->addDays(7),
        ]);

        // flash pesan sukses + token
        return back()->with('success', "Siswa “{$student->name}” ditambah. Token: $token");
    }
}
