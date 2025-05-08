<?php

namespace App\Http\Controllers;

use App\Models\{ParentProfile, RegistrationToken, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash};
use Illuminate\Support\Str;

class ParentRegisterController extends Controller
{
    /** --- HALAMAN FORM --- */
    public function create()
    {
        return view('Auth.register');   // pakai view form yg sudah ada
    }

    /** --- PROSES REGISTER --- */
    public function store(Request $r)
    {
        $r->validate([
            'token'      => 'required|string|size:8',
            'phone_number' => 'required_without:email|nullable|regex:/^08\d{8,11}$/',
            'email'      => 'nullable|email',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        /** 1. Cari token valid */
        $token = RegistrationToken::where('token', $r->token)
                  ->whereNull('used_at')
                  ->where('expires_at', '>', now())
                  ->firstOrFail();

        /** 2. Cari parent profil yg cocok */
        $parent = ParentProfile::where('student_id', $token->student_id)
                  ->where(function ($q) use ($r) {
                      $q->when($r->phone_number, fn ($q) => $q->where('phone', $r->phone_number))
                        ->when($r->email, fn ($q) => $q->orWhere('email', $r->email));
                  })
                  ->firstOrFail();

        /** 3. Bikin akun utk SEMUA parent di siswa tsb */
        DB::transaction(function () use ($parent, $r, $token) {

            ParentProfile::where('student_id', $parent->student_id)
                ->each(function ($p) use ($r) {

                    // Skip kalau sudah punya user
                    if ($p->user_id) return;

                    $user = User::create([
                        'name'     => $p->name,
                        'email'    => $p->email,           // boleh null
                        'role'     => 'parent',
                        'password' => Hash::make($r->password),
                    ]);

                    // simpan kontak telepon
                    if ($p->phone) {
                        $user->contacts()->create([
                            'phone_number' => $p->phone,
                            'is_primary'   => true,
                            'verified_at'  => now(),
                        ]);
                    }

                    $p->update(['user_id' => $user->id]);
                    $user->students()->attach($p->student_id);
                });

            $token->update(['used_at' => now()]);
        });

        // Auto-login akun yg baru dibuat (parent pendaftar)
        Auth::attempt(['email' => $parent->email, 'password' => $r->password]);

        return redirect()->route('dashboard');
    }
}
