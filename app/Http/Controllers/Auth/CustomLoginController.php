<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FailedLoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomLoginController extends Controller
{
    public function login(Request $request)
    {
        $identifier = $request->input('identifier');
        $password = $request->input('password');
        
        // Check if user is blocked
        if (FailedLoginAttempt::isBlocked($identifier)) {
            return back()->withErrors([
                'identifier' => 'Akun Anda telah diblokir karena terlalu banyak percobaan login yang gagal. Silakan hubungi pihak sekolah untuk bantuan.'
            ]);
        }
        
        // Determine if identifier is email or phone
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        
        $user = null;
        if ($isEmail) {
            $user = User::where('email', $identifier)->first();
        } else {
            // Clean phone number
            $cleanPhone = preg_replace('/^\+?62/', '0', $identifier);
            $cleanPhone = preg_replace('/\D/', '', $cleanPhone);
            
            // Find user by phone in parent_profiles or user_contacts
            $user = User::whereHas('contacts', function($query) use ($cleanPhone) {
                $query->where('phone_number', $cleanPhone);
            })->orWhereHas('parentProfile', function($query) use ($cleanPhone) {
                $query->where('phone', $cleanPhone);
            })->first();
        }
        
        if ($user && Hash::check($password, $user->password)) {
            // Check if account is blocked due to failed attempts (applies to all roles)
            if (FailedLoginAttempt::isBlocked($identifier)) {
                $roleMessage = $user->role === 'parent' ? 
                    'Akun Anda ditangguhkan karena terlalu banyak percobaan login. Hubungi pihak sekolah untuk reset token.' :
                    'Akun Anda ditangguhkan karena terlalu banyak percobaan login. Hubungi pihak sekolah untuk bantuan.';
                    
                return back()->withErrors([
                    'identifier' => $roleMessage
                ]);
            }
            
            // Clear failed login attempts on successful login
            FailedLoginAttempt::where('identifier', $identifier)->delete();
            
            // Also clear attempts for email if identifier was phone
            if (!$isEmail && $user->email) {
                FailedLoginAttempt::where('identifier', $user->email)->delete();
            }
            
            Auth::login($user);
            
            // If teacher has temp password, redirect to password change form
            if ($user->role === 'teacher' && $user->temp_password) {
                return redirect()->route('teacher.password.form');
            }
            
            return redirect()->intended('/dashboard');
        }
        
        // Record failed attempt
        FailedLoginAttempt::create([
            'identifier' => $identifier,
            'ip_address' => $request->ip(),
            'attempted_at' => now(),
        ]);
        
        return back()->withErrors([
            'identifier' => 'Email/Nomor HP atau kata sandi salah.'
        ]);
    }
}