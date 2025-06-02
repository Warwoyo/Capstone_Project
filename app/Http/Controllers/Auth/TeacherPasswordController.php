<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\FailedLoginAttempt;

class TeacherPasswordController extends Controller
{
    public function showChangePasswordForm()
    {
        $user = Auth::user();
        
        // Only teachers with temp passwords can access this
        if (!$user || $user->role !== 'teacher' || !$user->temp_password) {
            return redirect('/dashboard');
        }
        
        return view('auth.teacher-change-password');
    }
    
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        // Verify current password (should match temp password)
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak benar.']);
        }
        
        // Update password and clear temp password
        $user->update([
            'password' => Hash::make($request->new_password),
            'temp_password' => null, // Clear temp password
            'password_changed_at' => now(),
        ]);
        
        // Clear any remaining failed login attempts
        FailedLoginAttempt::where('identifier', $user->email)->delete();
        
        return redirect('/dashboard')->with('success', 'Password berhasil diubah!');
    }
}