<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Administrator
        $admin = User::create([
            'name' => 'Administrator Pustaka Utama',
            'username' => 'admin_bunga',
            'email' => 'admin.bunga@pustaka.local',
            'password' => Hash::make('AdminBunga#2026!'),
            'role' => 'admin',
        ]);

        Member::create([
            'user_id' => $admin->id,
            'full_name' => 'Administrator Pustaka Utama',
            'phone_encrypted' => '081122334455',
            'address' => 'Ruang Pengelola Perpustakaan Kampus Bukit Indah',
        ]);

        // 2. Akun Pengguna Biasa 1 (Budi Santoso)
        $budi = User::create([
            'name' => 'Budi Santoso',
            'username' => 'budi_santoso',
            'email' => 'budi.santoso@mahasiswa.local',
            'password' => Hash::make('PustakaAman#01!'),
            'role' => 'member',
        ]);

        Member::create([
            'user_id' => $budi->id,
            'full_name' => 'Budi Santoso, S.Kom.',
            'phone_encrypted' => '081234567890',
            'address' => 'Jl. Merdeka No. 45, Lhokseumawe',
        ]);

        // 3. Akun Pengguna Biasa 2 (Siti Aminah - Password sama persis dengan Budi untuk pengujian SEC-02)
        $siti = User::create([
            'name' => 'Siti Aminah',
            'username' => 'siti_aminah',
            'email' => 'siti.aminah@mahasiswa.local',
            'password' => Hash::make('PustakaAman#01!'),
            'role' => 'member',
        ]);

        Member::create([
            'user_id' => $siti->id,
            'full_name' => 'Siti Aminah, M.Kom.',
            'phone_encrypted' => '089876543210',
            'address' => 'Jl. Pendidikan No. 10, Lhokseumawe',
        ]);
    }
}
