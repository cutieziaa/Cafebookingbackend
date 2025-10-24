<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use App\Models\User;
use App\Models\MejaTipe;
use App\Models\Meja;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@cafe.com',
            'password' => Hash::make('password'), // Pastikan menggunakan Hash::make
            'peran' => 'admin',
            'nomor_wa' => '081234567890'
        ]);

        // CS User
        User::create([
            'nama' => 'Customer Service',
            'email' => 'cs@cafe.com',
            'password' => Hash::make('password'), // Pastikan menggunakan Hash::make
            'peran' => 'cs',
            'nomor_wa' => '081234567891'
        ]);

        // Customer contoh
        User::create([
            'nama' => 'John Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'), // Pastikan menggunakan Hash::make
            'peran' => 'customer',
            'nomor_wa' => '081234567892'
        ]);

    // Tipe Meja
        $tipe1 = MejaTipe::create([
            'nama_tipe' => 'Standard',
            'deskripsi' => 'Meja standar untuk 2-4 orang'
        ]);

        $tipe2 = MejaTipe::create([
            'nama_tipe' => 'Family',
            'deskripsi' => 'Meja besar untuk 6-8 orang'
        ]);

        $tipe3 = MejaTipe::create([
            'nama_tipe' => 'VIP',
            'deskripsi' => 'Meja VIP dengan view terbaik'
        ]);

    // Meja
        for ($i = 1; $i <= 10; $i++) {
            Meja::create([
                'tipe_id' => $tipe1->id,
                'kode_meja' => 'STD' . $i,
                'kapasitas' => 4,
                'status' => 'aktif'
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            Meja::create([
                'tipe_id' => $tipe2->id,
                'kode_meja' => 'FAM' . $i,
                'kapasitas' => 8,
                'status' => 'aktif'
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            Meja::create([
                'tipe_id' => $tipe3->id,
                'kode_meja' => 'VIP' . $i,
                'kapasitas' => 6,
                'status' => 'aktif'
            ]);
        }

        // Menu
        $menus = [
            ['nama' => 'Kopi Hitam', 'harga' => 15000, 'deskripsi' => 'Kopi hitam original'],
            ['nama' => 'Cappuccino', 'harga' => 25000, 'deskripsi' => 'Cappuccino dengan foam susu'],
            ['nama' => 'Latte', 'harga' => 28000, 'deskripsi' => 'Latte dengan art pada foam'],
            ['nama' => 'Croissant', 'harga' => 18000, 'deskripsi' => 'Croissant butter asli'],
            ['nama' => 'Sandwich', 'harga' => 32000, 'deskripsi' => 'Sandwich dengan daging asap'],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }

        // Booking contoh
        \App\Models\Booking::create([
            'pengguna_id' => 3, // ID customer
            'meja_id' => 1,
            'tanggal_booking' => now()->addDays(1),
            'waktu_mulai' => '14:00:00',
            'durasi_minutes' => 120,
            'jumlah_orang' => 4,
            'total_amount' => 100000,
            'booking_status' => 'menunggu'
        ]);
    }
}