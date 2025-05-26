<?php
// app/Http/Controllers/StudentController.php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;          // ← untuk DB::transaction
use Illuminate\Support\Str;                 // ← untuk Str::random
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Models\{Student, Classroom, User, RegistrationToken, UserContact, ParentProfile};

class StudentController extends Controller
{
    

public function store(Request $request, Classroom $class)
{
    DB::beginTransaction();
    try {
        // Simpan data siswa
        $student = Student::create($request->only([
            'student_number', 'name', 'birth_date', 'gender', 'medical_history'
        ]));

        // Hubungkan siswa dengan kelas
        $class->students()->attach($student->id);

        // Generate registration token untuk orang tua
        RegistrationToken::generateFor($student);

        // Siapkan data orang tua berdasarkan tipe_data
        $parents = [];

        if ($request->tipe_data === 'ortu') {
            // Data ayah
            if (!empty(trim($request->father_name))) {
                $parents[] = [
                    'name' => $request->father_name,
                    'relation' => 'father',
                    'phone' => $request->father_phone ?: null,
                    'email' => $request->father_email ?: null,
                    'nik' => $request->father_nik ?: null,
                    'occupation' => $request->father_occupation ?: null,
                    'address' => $request->father_address ?: null,
                ];
            }

            // Data ibu
            if (!empty(trim($request->mother_name))) {
                $parents[] = [
                    'name' => $request->mother_name,
                    'relation' => 'mother',
                    'phone' => $request->mother_phone ?: null,
                    'email' => $request->mother_email ?: null,
                    'nik' => $request->mother_nik ?: null,
                    'occupation' => $request->mother_occupation ?: null,
                    'address' => $request->mother_address ?: null,
                ];
            }
        } else { // wali
            if (!empty(trim($request->guardian_name))) {
                $parents[] = [
                    'name' => $request->guardian_name,
                    'relation' => 'guardian',
                    'phone' => $request->guardian_phone ?: null,
                    'email' => $request->guardian_email ?: null,
                    'nik' => $request->guardian_nik ?: null,
                    'occupation' => $request->guardian_occupation ?: null,
                    'address' => $request->guardian_address ?: null,
                ];
            }
        }

        // Buang field kosong phone/email supaya tidak "duplicate NULL"
        foreach ($parents as &$parent) {
            if (empty($parent['phone'])) unset($parent['phone']);
            if (empty($parent['email'])) unset($parent['email']);
        }
        unset($parent);

        // Simpan data orang tua
        foreach ($parents as $parent) {
            $student->parents()->create($parent);
        }

        DB::commit();

        // Redirect ke rute dengan parameter yang benar
        return redirect()
            ->route('classroom.tab', ['classroom' => $class->id, 'tab' => 'peserta'])
            ->with('success', 'Data tersimpan');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('ROLLBACK', ['msg' => $e->getMessage()]);
        return back()->withErrors('Gagal simpan: ' . $e->getMessage());
    }
}
    

    public function destroy(Classroom $class, Student $student)
    {
        DB::transaction(function () use ($class, $student) {
            $class->students()->detach($student->id);
            $student->delete();
        });
        return back()->with('success','Siswa dihapus');
    }
    public function update(Request $r, Classroom $class, Student $student)
    {
        $r->validate([
            'student_number' => 'required|max:30',
            'name'           => 'required|max:100',
            'birth_date'     => 'required|date',
            'gender'         => 'required|in:male,female',
            'father_name'    => 'required_if:tipe_data,ortu|max:100',
            'mother_name'    => 'required_if:tipe_data,ortu|max:100',
            'guardian_name'  => 'required_if:tipe_data,wali|max:100',
            'father_email'   => 'nullable|email|unique:parent_profiles,email,' .
                                $student->father?->id,
            'mother_email'   => 'nullable|email|unique:parent_profiles,email,' .
                                $student->mother?->id,
            'guardian_email' => 'nullable|email|unique:parent_profiles,email,' .
                                $student->guardian?->id,
            'father_phone'   => 'nullable|unique:parent_profiles,phone,' .
                                $student->father?->id,
            'mother_phone'   => 'nullable|unique:parent_profiles,phone,' .
                                $student->mother?->id,
            'guardian_phone' => 'nullable|unique:parent_profiles,phone,' .
                                $student->guardian?->id,
        ]);

        DB::transaction(function () use ($r, $student) {

            /* ---------- update tabel students ---------- */
            $student->update([
                'student_number'  => $r->student_number,
                'name'            => $r->name,
                'birth_date'      => $r->birth_date,
                'gender'          => $r->gender,
                'photo'           => $r->file('photo')
                                        ? $r->file('photo')->store('students','public')
                                        : $student->photo,
                'medical_history' => $r->medical_history,
            ]);

            /* ---------- rebuild parentData ---------- */
            $parentData = [];

            if ($r->tipe_data === 'ortu') {
                if (trim($r->father_name) !== '') {
                    $parentData[] = [
                        'relation'   => 'father',
                        'name'       => $r->father_name,
                        'phone'      => $r->father_phone ?: null,
                        'email'      => $r->father_email ?: null,
                        'nik'        => $r->father_nik ?: null,
                        'occupation' => $r->father_occupation ?: null,
                        'address'    => $r->father_address ?: null,
                    ];
                }
                if (trim($r->mother_name) !== '') {
                    $parentData[] = [
                        'relation'   => 'mother',
                        'name'       => $r->mother_name,
                        'phone'      => $r->mother_phone ?: null,
                        'email'      => $r->mother_email ?: null,
                        'nik'        => $r->mother_nik ?: null,
                        'occupation' => $r->mother_occupation ?: null,
                        'address'    => $r->mother_address ?: null,
                    ];
                }
            } else { // wali
                if (trim($r->guardian_name) !== '') {
                    $parentData[] = [
                        'relation'   => 'guardian',
                        'name'       => $r->guardian_name,
                        'phone'      => $r->guardian_phone ?: null,
                        'email'      => $r->guardian_email ?: null,
                        'nik'        => $r->guardian_nik ?: null,
                        'occupation' => $r->guardian_occupation ?: null,
                        'address'    => $r->guardian_address ?: null,
                    ];
                }
            }

            // buang field kosong phone/email supaya tidak “duplicate NULL”
            foreach ($parentData as &$p) {
                if (empty($p['phone'])) unset($p['phone']);
                if (empty($p['email'])) unset($p['email']);
            }
            unset($p);

            /* ---------- simpan parent (UPSERT) ---------- */
            // hapus yang tidak dipakai lagi
            $student->parents()
                    ->whereNotIn('relation', collect($parentData)->pluck('relation'))
                    ->delete();

            // upsert berdasarkan (student_id, relation)
            foreach ($parentData as $data) {
                $student->parents()->updateOrCreate(
                    ['relation' => $data['relation']],   // key
                    $data
                );
            }
        });

        return back()->with('success','Data siswa diperbarui');
    }


}
