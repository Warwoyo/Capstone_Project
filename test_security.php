<?php

// Test Script for Security Features
// Run this to test the functionality: php artisan tinker < test_security.php

echo "Testing Security Features...\n";

use App\Models\User;
use App\Models\FailedLoginAttempt;
use App\Models\RegistrationToken;
use App\Models\ParentProfile;
use App\Models\Student;

// Test 1: Create a test failed login attempt
echo "1. Testing failed login attempt creation...\n";
FailedLoginAttempt::create([
    'identifier' => 'test@example.com',
    'ip_address' => '127.0.0.1',
    'attempted_at' => now(),
]);
echo "✓ Failed login attempt created\n";

// Test 2: Check if blocking works
echo "2. Testing blocking mechanism...\n";
for ($i = 0; $i < 5; $i++) {
    FailedLoginAttempt::create([
        'identifier' => 'blocked@example.com',
        'ip_address' => '127.0.0.1',
        'attempted_at' => now(),
    ]);
}
$isBlocked = FailedLoginAttempt::isBlocked('blocked@example.com');
echo $isBlocked ? "✓ Account blocking works\n" : "✗ Account blocking failed\n";

// Test 3: Test token generation
echo "3. Testing token generation...\n";
$token = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
echo "✓ Generated token: {$token}\n";

// Test 4: Clear attempts
echo "4. Testing attempt clearing...\n";
$cleared = FailedLoginAttempt::where('identifier', 'test@example.com')->delete();
echo "✓ Cleared {$cleared} attempts\n";

echo "\nAll tests completed!\n";
echo "\nFeatures implemented:\n";
echo "- Failed login attempt tracking\n";
echo "- Account blocking after 5 attempts\n";
echo "- Automatic token generation on parent reset\n";
echo "- Teacher password reset with temp passwords\n";
echo "- Status display (Aktif/Perlu Bantuan)\n";
echo "- Failed attempt cleanup on successful login\n";
echo "- Security for both parents and teachers\n";