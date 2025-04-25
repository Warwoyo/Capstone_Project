<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\RegistrationToken;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /* ---------- LOGIN ---------- */
    public function showLogin() { return view('Auth.login'); }

    public function login(Request $r)
    {
        $raw = $r->phone_number;
        $normalized = preg_replace('/\D/', '', preg_replace('/^\+?62/', '0', $raw));
        $r->merge(['phone_number' => $normalized]);

        $r->validate([
            'phone_number' => ['required','regex:/^08\d{8,11}$/'],
            'password'     => ['required'],
        ]);
    
        if (Auth::attempt($r->only('phone_number','password'), $r->boolean('remember'))) {
            $r->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
    
        return back()->withErrors(['credential' => 'Nomor HP atau kata sandi salah!'])
                     ->onlyInput('phone_number');
    }
    

    /* ---------- LOGOUT ---------- */
    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/login');
    }

    /* ---------- REGISTER ORANG TUA ---------- */
    public function showParentRegister() { return view('Auth.register'); }

    public function parentRegister(Request $r)
    {
        /* normalisasi HP */
        $hp = preg_replace('/\D/','', preg_replace('/^\+?62/','0', $r->phone_number));
        $r->merge(['phone_number' => $hp]);
    
        /* validasi */
        $r->validate([
            'phone_number' => ['required','regex:/^08\d{8,11}$/',
                               Rule::unique('users','phone_number')],
            'password'     => ['required','confirmed','min:6'],
            'token'        => ['required','size:8'],
        ]);
    
        /* --- cek token --- */
        $token = RegistrationToken::where('token', $r->token)
                  ->whereNull('used_at')
                  ->where('expires_at','>',now())
                  ->first();
    
        if (!$token) {
            return back()
                ->withErrors(['token' => 'Token tidak ditemukan. Silakan hubungi wali kelas.'])
                ->onlyInput('phone_number');
        }
    
        /* --- buat akun parent --- */
        $student = $token->student;
        $parent  = User::create([
            'name'         => 'Ortu ' . $student->name,  // auto-generate
            'phone_number' => $hp,
            'password'     => Hash::make($r->password),
            'role'         => 'parent',
        ]);
    
        $parent->parentProfile()->create();
        $parent->students()->attach($student->id);
        $token->update(['used_at' => now()]);
    
        Auth::login($parent);
        return redirect('/dashboard')->with('success','Registrasi berhasil!');
    }
    
}
