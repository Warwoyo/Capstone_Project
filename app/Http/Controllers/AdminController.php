<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ParentProfile;
use App\Models\UserContact;
use App\Models\RegistrationToken;
use App\Models\Student;
use App\Models\FailedLoginAttempt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



class AdminController extends Controller
{
    public function getParentList()
    {
        // Fetch parent data from database with related information
        $parents = User::where('role', 'parent')
            ->with(['contacts'])
            ->get()
            ->map(function ($user) {
                // Get parent profile data from parent_profiles table
                $parentProfiles = ParentProfile::where('user_id', $user->id)->get();
                $primaryContact = $user->contacts ? $user->contacts->where('is_primary', 1)->first() : null;
                
                // If user has parent profiles, use the first one for basic info
                $profile = $parentProfiles->first();
                
                // Get registration tokens for this parent's students
                $tokens = [];
                $status = 'Pending';
                if ($profile && $profile->student_id) {
                    $registrationTokens = RegistrationToken::where('student_id', $profile->student_id)
                        ->orderBy('created_at', 'desc')
                        ->get();
                    
                    foreach ($registrationTokens as $regToken) {
                        $tokens[] = [
                            'token' => $regToken->token,
                            'used_at' => $regToken->used_at,
                            'expires_at' => $regToken->expires_at,
                        ];
                    }
                    
                    // Determine status based on token usage and account verification
                    $isBlocked = FailedLoginAttempt::isBlocked($user->email) || 
                                FailedLoginAttempt::isBlocked($profile ? $profile->phone : '');
                    
                    if ($isBlocked) {
                        $status = 'Perlu Bantuan';
                    } elseif ($user->email_verified_at) {
                        $status = 'Aktif';
                    } elseif ($registrationTokens->where('used_at', '!=', null)->count() > 0) {
                        $status = 'Token Digunakan';
                    } else {
                        $status = 'Token Belum Digunakan';
                    }
                }
                
                return [
                    'id' => $user->id,
                    'nama' => $user->name,
                    'email' => $user->email,
                    'nomor_telepon' => $profile ? $profile->phone : ($primaryContact ? $primaryContact->phone_number : null),
                    'nik' => $profile ? $profile->nik : null,
                    'relation' => $profile ? ucfirst($profile->relation) : null,
                    'alamat' => $profile ? $profile->address : null,
                    'pekerjaan' => $profile ? $profile->occupation : null,
                    'created_at' => $user->created_at,
                    'status' => $status,
                    'tokens' => $tokens,
                    'token' => $tokens ? $tokens[0]['token'] ?? 'Belum ada' : 'Belum ada'
                ];
            })
            ->toArray();

        // Now add unused registration tokens as "Belum Registrasi" entries
        $unusedTokens = RegistrationToken::whereNull('used_at')
            ->with('student')
            ->get()
            ->map(function ($token) {
                return [
                    'id' => 'token_' . $token->id, // Use different ID format to distinguish
                    'nama' => $token->student ? $token->student->name : 'Siswa Tidak Ditemukan',
                    'email' => '-',
                    'nomor_telepon' => null,
                    'nik' => null,
                    'relation' => null,
                    'alamat' => null,
                    'pekerjaan' => null,
                    'created_at' => $token->created_at,
                    'status' => 'Belum Registrasi',
                    'tokens' => [
                        [
                            'token' => $token->token,
                            'used_at' => null,
                            'expires_at' => $token->expires_at,
                        ]
                    ],
                    'token' => $token->token,
                    'is_unused_token' => true // Flag to identify unused tokens
                ];
            })
            ->toArray();

        // Merge registered parents and unused tokens
        return array_merge($parents, $unusedTokens);
    }

    public function fetchParentList()
    {
        $parents = $this->getParentList();
        $teachers = $this->getTeacherList();
        return view('Admin.index', compact('parents', 'teachers'));
    }

    public function getTeacherList()
    {
        // Fetch teacher data from database
        $teachers = User::where('role', 'teacher')
            ->with(['contacts'])
            ->get()
            ->map(function ($user) {
                $primaryContact = $user->contacts ? $user->contacts->where('is_primary', 1)->first() : null;
                
                // Check if teacher is blocked due to failed login attempts
                $isBlocked = FailedLoginAttempt::isBlocked($user->email);
                
                // Check if teacher has temporary password
                $hasTempPassword = !is_null($user->temp_password);
                
                if ($isBlocked) {
                    $status = 'Perlu Bantuan';
                } elseif ($hasTempPassword) {
                    $status = "Temp Pass: {$user->temp_password}";
                } else {
                    $status = 'Aktif';
                }
                
                return [
                    'id' => $user->id,
                    'nama' => $user->name,
                    'email' => $user->email,
                    'nip' => null,
                    'nomor_telepon' => $primaryContact ? $primaryContact->phone_number : null,
                    'created_at' => $user->created_at,
                    'status' => $status,
                    'is_blocked' => $isBlocked,
                    'has_temp_password' => $hasTempPassword,
                ];
            })
            ->toArray();

        return $teachers;
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,teacher,parent',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('Admin.index')->with('success', 'User created successfully');
    }

    public function updateUser(Request $request, $userId)
    {
        // Find the user by ID
        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('Admin.index')->with('error', 'User not found');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('Admin.index')->with('success', 'User updated successfully');
    }

    public function deleteUser($userId)
    {
        // Find the user by ID
        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('Admin.index')->with('error', 'User not found');
        }

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('Admin.index')->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('Admin.index')->with('success', 'User deleted successfully');
    }

    public function approveParent(ParentProfile $parent)
    {
        if ($parent->user_id) {
            $user = User::find($parent->user_id);
            if ($user) {
                $user->update(['email_verified_at' => now()]);
            }
        }

        return redirect()->route('admin.parents')->with('success', 'Parent approved successfully');
    }

    public function deleteParent(ParentProfile $parent)
    {
        if ($parent->user_id) {
            $user = User::find($parent->user_id);
            if ($user) {
                $user->delete();
            }
        }
        
        $parent->delete();

        return redirect()->route('admin.parents')->with('success', 'Parent deleted successfully');
    }

    public function settings()
    {
        // You can add system settings here
        return view('admin.settings.index');
    }

    public function updateSettings(Request $request)
    {
        // Handle system settings update
        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully');
    }

    public function resetParentToken(User $parent)
    {
        // Verify this is a parent user
        if ($parent->role !== 'parent') {
            return redirect()->route('Admin.index')->with('error', 'User is not a parent');
        }

        // Get parent profile to find student_id for token generation
        $parentProfile = ParentProfile::where('user_id', $parent->id)->first();
        $studentId = $parentProfile ? $parentProfile->student_id : null;

        // Clear failed login attempts for this parent
        $clearedAttempts = 0;
        $clearedAttempts += FailedLoginAttempt::where('identifier', $parent->email)->count();
        FailedLoginAttempt::where('identifier', $parent->email)->delete();
        
        if ($parentProfile && $parentProfile->phone) {
            $clearedAttempts += FailedLoginAttempt::where('identifier', $parentProfile->phone)->count();
            FailedLoginAttempt::where('identifier', $parentProfile->phone)->delete();
        }

        // Delete existing unused tokens for this student (if any)
        if ($studentId) {
            RegistrationToken::where('student_id', $studentId)
                ->whereNull('used_at')
                ->delete();
        }

        // Delete the user account (this will cascade to related tables)
        $parent->delete();

        // Generate new token for the same student if we have student_id
        $newToken = null;
        if ($studentId) {
            // Generate a new 8-character registration token
            do {
                $newToken = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            } while (RegistrationToken::where('token', $newToken)->exists());
            
            // Create the new registration token
            RegistrationToken::create([
                'student_id' => $studentId,
                'token' => $newToken,
                'expires_at' => now()->addDays(30), // Token expires in 30 days
            ]);
        }

        $message = 'Parent account and token have been reset.';
        if ($clearedAttempts > 0) {
            $message .= " Cleared {$clearedAttempts} failed login attempts.";
        }
        if ($newToken) {
            $message .= " New token generated: {$newToken}";
        } else {
            $message .= " Warning: Could not generate new token - student ID not found.";
        }

        return redirect()->route('Admin.index')->with('success', $message);
    }

    public function generateNewToken()
    {
        // Get a random student that doesn't have an active unused token
        $studentsWithoutToken = Student::whereNotIn('id', function($query) {
            $query->select('student_id')
                  ->from('registration_tokens')
                  ->whereNull('used_at');
        })->get();
        
        if ($studentsWithoutToken->isEmpty()) {
            return redirect()->route('Admin.index')->with('error', 'Semua siswa sudah memiliki token registrasi yang belum digunakan.');
        }
        
        // Use the first available student
        $student = $studentsWithoutToken->first();
        
        // Generate a new 8-character registration token
        do {
            $token = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while (RegistrationToken::where('token', $token)->exists());
        
        // Create the registration token
        $registrationToken = RegistrationToken::create([
            'student_id' => $student->id,
            'token' => $token,
            'expires_at' => now()->addDays(30), // Token expires in 30 days
        ]);
        
        return redirect()->route('Admin.index')->with('success', "Token baru berhasil dibuat untuk siswa {$student->name}: {$token}");
    }

    public function deleteUnusedToken($token)
    {
        // Find the unused token
        $registrationToken = RegistrationToken::where('token', $token)
            ->whereNull('used_at')
            ->first();
        
        if (!$registrationToken) {
            return redirect()->route('Admin.index')->with('error', 'Token tidak ditemukan atau sudah digunakan.');
        }
        
        // Delete the token
        $registrationToken->delete();
        
        return redirect()->route('Admin.index')->with('success', 'Token yang belum digunakan berhasil dihapus.');
    }

    public function resetTeacherPassword($teacherId)
    {
        // Find the user by ID
        $teacher = User::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('Admin.index')->with('error', 'Teacher not found');
        }

        // Verify this is a teacher user
        if ($teacher->role !== 'teacher') {
            return redirect()->route('Admin.index')->with('error', 'User is not a teacher');
        }

        // Generate a new temporary password
        $tempPassword = 'temp' . rand(1000, 9999);
        
        // Clear failed login attempts for this teacher
        $clearedAttempts = FailedLoginAttempt::where('identifier', $teacher->email)->count();
        FailedLoginAttempt::where('identifier', $teacher->email)->delete();

        // Update teacher with temp password
        $teacher->update([
            'password' => bcrypt($tempPassword),
            'temp_password' => $tempPassword, // Store temp password to show in status
            'password_changed_at' => null,
        ]);

        return redirect()->route('Admin.index')->with('success', 'Teacher password has been reset and temp password is now visible in status column.');
    }

    public function generateTempPassword($teacherId)
    {
        // Find the user by ID
        $teacher = User::find($teacherId);
        
        if (!$teacher) {
            return redirect()->route('Admin.index')->with('error', 'Teacher not found');
        }

        // Verify this is a teacher user
        if ($teacher->role !== 'teacher') {
            return redirect()->route('Admin.index')->with('error', 'User is not a teacher');
        }

        // Generate a new temporary password
        $tempPassword = 'temp' . rand(1000, 9999);
        
        // Clear failed login attempts for this teacher
        $clearedAttempts = FailedLoginAttempt::where('identifier', $teacher->email)->count();
        FailedLoginAttempt::where('identifier', $teacher->email)->delete();

        // Update teacher with temp password
        $teacher->update([
            'password' => bcrypt($tempPassword),
            'temp_password' => $tempPassword, // Store temp password to show in status
            'password_changed_at' => null,
        ]);

        return redirect()->route('Admin.index')->with('success', "Temporary password generated for {$teacher->name}: {$tempPassword}");
    }
}
