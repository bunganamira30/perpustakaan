<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_with_identical_password_generates_distinct_hashes(): void
    {
        $identicalPassword = 'PustakaAman#01!';

        // Registrasi User 1 (Budi)
        $resp1 = $this->post('/register', [
            'full_name' => 'Budi Santoso',
            'username' => 'budi_santoso',
            'email' => 'budi@test.local',
            'password' => $identicalPassword,
            'password_confirmation' => $identicalPassword,
            'phone' => '081234567890',
        ]);
        $resp1->assertRedirect('/books');

        // Logout User 1 agar User 2 dapat mendaftar (guest middleware)
        $this->post('/logout');

        // Registrasi User 2 (Siti)
        $resp2 = $this->post('/register', [
            'full_name' => 'Siti Aminah',
            'username' => 'siti_aminah',
            'email' => 'siti@test.local',
            'password' => $identicalPassword,
            'password_confirmation' => $identicalPassword,
            'phone' => '081298765432',
        ]);
        $resp2->assertRedirect('/books');

        $user1 = User::where('username', 'budi_santoso')->firstOrFail();
        $user2 = User::where('username', 'siti_aminah')->firstOrFail();

        // Verifikasi SEC-02: Salt unik matematis menghasilkan hash berbeda
        $this->assertNotEquals($user1->password, $user2->password);
        $this->assertTrue(Hash::check($identicalPassword, $user1->password));
        $this->assertTrue(Hash::check($identicalPassword, $user2->password));
    }

    public function test_password_validation_enforces_min_8_and_max_72_bytes(): void
    {
        // 1. Password kurang dari 8 karakter
        $respShort = $this->post('/register', [
            'full_name' => 'User Pendek',
            'username' => 'user_pendek',
            'email' => 'pendek@test.local',
            'password' => '1234567',
            'password_confirmation' => '1234567',
            'phone' => '081234567890',
        ]);
        $respShort->assertSessionHasErrors('password');

        // 2. Password melebihi 72 byte (Bcrypt truncation limit guard)
        $tooLongPassword = str_repeat('A', 75);
        $respLong = $this->post('/register', [
            'full_name' => 'User Panjang',
            'username' => 'user_panjang',
            'email' => 'panjang@test.local',
            'password' => $tooLongPassword,
            'password_confirmation' => $tooLongPassword,
            'phone' => '081234567890',
        ]);
        $respLong->assertSessionHasErrors('password');

        // 3. Password valid dalam rentang 8-72 byte
        $validPassword = 'PasswordAman123!';
        $respValid = $this->post('/register', [
            'full_name' => 'User Valid',
            'username' => 'user_valid',
            'email' => 'valid@test.local',
            'password' => $validPassword,
            'password_confirmation' => $validPassword,
            'phone' => '081234567890',
        ]);
        $respValid->assertRedirect('/books');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Pengguna Uji',
            'username' => 'pengguna_uji',
            'email' => 'pengguna@test.local',
            'password' => Hash::make('PustakaAman#01!'),
            'role' => 'member',
        ]);

        $response = $this->post('/login', [
            'username' => 'pengguna_uji',
            'password' => 'PustakaAman#01!',
        ]);

        $response->assertRedirect('/books');
        $this->assertAuthenticatedAs($user);
    }
}
