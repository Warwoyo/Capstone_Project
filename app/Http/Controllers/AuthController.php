<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\RegistrationToken;
use Illuminate\Validation\Rule;
use App\Models\UserContact;


class AuthController extends Controller
{
    /* ---------- LOGIN ---------- */
    public function showLogin() { return view('Auth.login'); }

    /* ---------- LOGIN ---------- */
    public function login(Request $r)
    {
        $r->validate([
            'identifier' => 'required',
            'password'   => 'required',
        ]);

        $id  = $r->identifier;
        $pw  = $r->password;

        // ── Cek apakah email ──────────────────────────────
        if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
            if (Auth::attempt(['email' => $id, 'password' => $pw], $r->boolean('remember'))) {
                $r->session()->regenerate();
                return redirect()->intended('/dashboard');
            }
        }
        // ── Kalau bukan email → anggap nomor HP ───────────
        $hp = preg_replace('/\D/','', preg_replace('/^\+?62/','0', $id));

        $userId = UserContact::where('phone_number', $hp)->value('user_id');

        if ($userId) {
            $user = User::find($userId);
            if ($user && Hash::check($pw, $user->password)) {
                Auth::login($user, $r->boolean('remember'));
                $r->session()->regenerate();
                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors(['identifier' => 'Email/No HP atau password salah'])
                    ->onlyInput('identifier');
    }
    

    /* ---------- LOGOUT ---------- */
    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/login');
    }
     
}
