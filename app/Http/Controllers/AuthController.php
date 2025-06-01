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

                return back()->withErrors(['password' => 'Email atau Password salah.']);
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

                return back()->withErrors(['password' => 'No telpon atau Password salah..']);
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

    /* ---------- PARENT REGISTRATION ---------- */
    public function showParentRegister()
    {
        return view('Auth.register');
    }

    public function parentRegister(Request $request)
    {
        // Validation rules
        $rules = [
            'token' => 'required|string|size:8',
            'login_method' => 'required|in:phone,email',
            'password' => 'required|string|min:6|confirmed',
        ];

        if ($request->login_method === 'phone') {
            $rules['phone_number'] = 'required|string';
        } else {
            $rules['email'] = 'required|email';
        }

        $request->validate($rules);

        // Normalize phone number if provided
        $phoneNumber = null;
        if ($request->login_method === 'phone' && $request->phone_number) {
            $phoneNumber = preg_replace('/\D/', '', 
                preg_replace('/^\+?62/', '0', trim($request->phone_number))
            );
            
            // Additional validation for Indonesian phone numbers
            if (!preg_match('/^08\d{8,11}$/', $phoneNumber)) {
                return back()->withErrors(['phone_number' => 'Format nomor telepon tidak valid. Gunakan format 08xxxxxxxxxx.']);
            }
        }

        // Validate token
        $token = RegistrationToken::where('token', $request->token)
            ->where('is_used', false)
            ->first();

        if (!$token) {
            return back()->withErrors(['token' => 'Token registrasi tidak valid atau sudah digunakan.']);
        }

        // Check if phone number already exists in user_contacts
        if ($request->login_method === 'phone') {
            // Debug: Log the normalized phone number
            \Log::info('Checking phone number: ' . $phoneNumber);
            \Log::info('Existing contacts count: ' . UserContact::count());
            
            $existingContact = UserContact::where('phone_number', $phoneNumber)->first();
            if ($existingContact) {
                \Log::info('Found existing contact: ' . $existingContact->phone_number . ' for user_id: ' . $existingContact->user_id);
                return back()->withErrors(['phone_number' => 'Nomor telepon sudah terdaftar. Silakan gunakan nomor lain atau login jika Anda sudah memiliki akun.']);
            }
        }

        // Check if email already exists
        if ($request->login_method === 'email') {
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return back()->withErrors(['email' => 'Email sudah terdaftar. Silakan gunakan email lain atau login jika Anda sudah memiliki akun.']);
            }
        }

        // Create parent user
        $user = User::create([
            'name' => $token->child_name . ' (Orang Tua)',
            'email' => $request->login_method === 'email' ? $request->email : null,
            'password' => Hash::make($request->password),
            'role' => 'parent',
            'child_id' => $token->child_id,
        ]);

        // Create contact record if phone registration
        if ($request->login_method === 'phone') {
            UserContact::create([
                'user_id' => $user->id,
                'phone_number' => $phoneNumber,
                'is_primary' => true,
                'verified_at' => now(),
            ]);
        }

        // Mark token as used
        $token->update(['is_used' => true]);

        // Auto login
        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Registrasi berhasil! Selamat datang.');
    }
}
