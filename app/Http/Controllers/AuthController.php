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

    public function login(Request $r)
    {
        $id  = $r->identifier;
        $pw  = $r->password;

        // Login pakai email
        if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $id)->first();

            if ($user) {
                $hashInfo = Hash::info($user->password);

                // Cek apakah hash bukan bcrypt
                if ($hashInfo['algoName'] !== 'bcrypt') {
                    return back()->withErrors([
                        'identifier' => 'Akun mengalami masalah pada sistem. Silakan hubungi guru atau admin untuk bantuan lebih lanjut.'
                    ]);
                }

                if (Auth::attempt(['email' => $id, 'password' => $pw], $r->boolean('remember'))) {
                    $r->session()->regenerate();
                    return redirect()->intended('/dashboard');
                }

                return back()->withErrors(['password' => 'Password salah.']);
            }

            return back()->withErrors(['identifier' => 'Akun tidak ditemukan.']);
        }

        // Login pakai no HP
        $hp = preg_replace('/\D/', '', preg_replace('/^\+?62/', '0', $id));
        $userId = UserContact::where('phone_number', $hp)->value('user_id');

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $hashInfo = Hash::info($user->password);

                if ($hashInfo['algoName'] !== 'bcrypt') {
                    return back()->withErrors([
                        'identifier' => 'Akun ini bermasalah karena sistem tidak dapat membaca data. Silakan hubungi guru atau admin.'
                    ]);
                }

                if (Auth::attempt(['id' => $user->id, 'password' => $pw], $r->boolean('remember'))) {
                    $r->session()->regenerate();
                    return redirect()->intended('/dashboard');
                }

                return back()->withErrors(['password' => 'Password salah.']);
            }
        }

        return back()->withErrors(['identifier' => 'Akun tidak ditemukan.']);
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
