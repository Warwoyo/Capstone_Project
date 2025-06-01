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
            ->first();

        if (!$token) {
            return back()->withErrors([
                'token' => 'Token tidak valid atau sudah kadaluarsa. Silakan hubungi guru atau admin.',
            ])->withInput();
        }

        // Check if phone number already exists in user_contacts
        if ($r->phone_number) {
            // Only check if there's a valid user-contact relationship
            $existingContact = \App\Models\UserContact::where('phone_number', $r->phone_number)
                ->whereExists(function ($query) {
                    $query->select('id')
                        ->from('users')
                        ->whereColumn('users.id', 'user_contacts.user_id');
                })
                ->first();
                
            if ($existingContact) {
                return back()->withErrors([
                    'phone_number' => 'Nomor telepon sudah terdaftar. Silakan gunakan nomor lain atau login jika Anda sudah memiliki akun.',
                ])->withInput();
            }
            
            // Clean up any orphaned contacts for this phone number
            \App\Models\UserContact::where('phone_number', $r->phone_number)
                ->whereNotExists(function ($query) {
                    $query->select('id')
                        ->from('users')
                        ->whereColumn('users.id', 'user_contacts.user_id');
                })
                ->delete();
        }

        // Check if email already exists
        if ($r->email) {
            $existingUser = User::where('email', $r->email)->first();
            if ($existingUser) {
                return back()->withErrors([
                    'email' => 'Email sudah terdaftar. Silakan gunakan email lain atau login jika Anda sudah memiliki akun.',
                ])->withInput();
            }
        }

        /** 2. Cari parent profil yg cocok */
        $parent = ParentProfile::where('student_id', $token->student_id)
            ->where(function ($q) use ($r) {
                if ($r->phone_number) {
                    $q->where('phone', $r->phone_number);
                } elseif ($r->email) {
                    $q->where('email', $r->email);
                }
            })
            ->first();

        if (!$parent) {
            return back()->withErrors([
                'phone_number' => 'Data tidak ditemukan. Pastikan token dan nomor/email sesuai. Silakan hubungi guru atau admin.',
            ])->withInput();
        }



        /** 3. Bikin akun utk SEMUA parent di siswa tsb */
        DB::transaction(function () use ($parent, $r, $token) {

            ParentProfile::where('student_id', $parent->student_id)
                ->each(function ($p) use ($r) {

                    // Skip kalau sudah punya user
                    if ($p->user_id) return;

                    try {
                        $user = User::create([
                            'name'     => $p->name,
                            'email'    => $p->email,           // boleh null
                            'role'     => 'parent',
                            'password' => Hash::make($r->password),
                        ]);

                        // simpan kontak telepon
                        if ($p->phone) {
                            try {
                                $user->contacts()->create([
                                    'phone_number' => $p->phone,
                                    'is_primary'   => true,
                                    'verified_at'  => now(),
                                ]);
                            } catch (\Illuminate\Database\QueryException $e) {
                                // If phone number already exists, skip creating contact
                                // This handles edge cases where multiple parents have same phone
                                if ($e->getCode() == 23000) {
                                    // Duplicate entry error - continue without creating contact
                                    \Log::warning("Duplicate phone number skipped for user {$user->id}: {$p->phone}");
                                } else {
                                    throw $e; // Re-throw other database errors
                                }
                            }
                        }

                        $p->update(['user_id' => $user->id]);
                        $user->students()->attach($p->student_id);
                        
                    } catch (\Exception $e) {
                        // Log the error and re-throw to rollback transaction
                        \Log::error("Failed to create user for parent profile {$p->id}: " . $e->getMessage());
                        throw $e;
                    }
                });

            $token->update(['used_at' => now()]);
        });

        // Auto-login akun yg baru dibuat (parent pendaftar)
        $loginCredentials = [];
        if ($parent->email) {
            $loginCredentials = ['email' => $parent->email, 'password' => $r->password];
        } else {
            // If no email, find the user by ID and login directly
            $parentUser = User::where('id', $parent->user_id)->first();
            if ($parentUser) {
                Auth::login($parentUser);
            }
        }
        
        if (!empty($loginCredentials)) {
            Auth::attempt($loginCredentials);
        }

        return redirect()->route('dashboard.index')
            ->with('success', 'Akun berhasil dibuat. Selamat datang di aplikasi sekolah!');
    }
}
