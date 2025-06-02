<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\{
    User, TeacherProfile, Classroom, Student, ParentProfile, UserContact, RegistrationToken
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'guru1@paud.local'],
            [
                'name'     => 'Bu Rani',
                'role'     => 'teacher',
                'password' => Hash::make('guru123'),
            ]
        );

        // simpan nomor HP di user_contacts
        $user->contacts()->updateOrCreate(
            ['phone_number' => '081234567890'],
            ['is_primary' => true, 'verified_at' => now()]
        );

        // profil guru (tanpa phone)
        TeacherProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['nip' => '198509102024042001', 'address' => 'Jl. Merdeka 1']
        );
    }
}
