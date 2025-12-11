<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

// Impor semua model yang akan di-seed
use App\Models\User;
use App\Models\MejaTipe;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Voucher;
use App\Models\Booking;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pickup;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Nonaktifkan foreign key checks untuk mempermudah truncate table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan tabel-tabel terlebih dahulu agar tidak duplikat saat di-run ulang
        $this->truncateTables();

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // === 1. Seed User ===
        $admin = User::create([
            'name' => 'Admin Resto',
            'email' => 'admin@resto.com',
            'password' => Hash::make('password'),
            'phone' => '08123456789',
            'role' => 'admin',
        ]);

        $customer1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'phone' => '08111111111',
            'role' => 'customer',
        ]);

        $customer2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'password' => Hash::make('password'),
            'phone' => '08222222222',
            'role' => 'customer',
        ]);

        // === 2. Seed MejaTipe dan Meja ===
        $tipeMeja2 = MejaTipe::create(['nama' => 'Meja 2 Orang']);
        $tipeMeja4 = MejaTipe::create(['nama' => 'Meja 4 Orang']);
        $tipeMeja6 = MejaTipe::create(['nama' => 'Meja 6 Orang']);

        Meja::create(['meja_tipe_id' => $tipeMeja2->id, 'nomor' => 'A-01', 'tersedia' => true]);
        Meja::create(['meja_tipe_id' => $tipeMeja2->id, 'nomor' => 'A-02', 'tersedia' => false]);
        Meja::create(['meja_tipe_id' => $tipeMeja4->id, 'nomor' => 'B-01', 'tersedia' => true]);
        Meja::create(['meja_tipe_id' => $tipeMeja4->id, 'nomor' => 'B-02', 'tersedia' => true]);
        Meja::create(['meja_tipe_id' => $tipeMeja6->id, 'nomor' => 'C-01', 'tersedia' => true]);

        // === 3. Seed Menu ===
        $menu1 = Menu::create(['nama' => 'Nasi Goreng Spesial', 'harga' => 35000, 'deskripsi' => 'Nasi goreng dengan telur mata sapi, ayam suwir, dan kerupuk', 'gambar_url' => 'https://via.placeholder.com/150', 'tersedia' => true]);
        $menu2 = Menu::create(['nama' => 'Mie Ayam Bakso', 'harga' => 30000, 'deskripsi' => 'Mie ayam dengan bakso urat dan pangsit goreng', 'gambar_url' => 'https://via.placeholder.com/150', 'tersedia' => true]);
        $menu3 = Menu::create(['nama' => 'Ayam Bakar Madu', 'harga' => 45000, 'deskripsi' => 'Ayam bakar dengan saus madu khas, nasi, lalapan, dan sambal', 'gambar_url' => 'https://via.placeholder.com/150', 'tersedia' => true]);
        $menu4 = Menu::create(['nama' => 'Es Teh Manis', 'harga' => 5000, 'deskripsi' => 'Teh manis dingin', 'gambar_url' => 'https://via.placeholder.com/150', 'tersedia' => true]);
        $menu5 = Menu::create(['nama' => 'Jus Alpukat', 'harga' => 15000, 'deskripsi' => 'Jus alpukat segar dengan susu dan madu', 'gambar_url' => 'https://via.placeholder.com/150', 'tersedia' => true]);
        
        // === 4. Seed Voucher ===
        $voucherPersen = Voucher::create(['kode' => 'PROMO15', 'diskon_persen' => 15, 'diskon_nominal' => null, 'minimum_order' => 50000, 'limit_penggunaan' => 100, 'expired_at' => Carbon::now()->addMonths(2)]);
        $voucherNominal = Voucher::create(['kode' => 'DISKON25K', 'diskon_persen' => null, 'diskon_nominal' => 25000, 'minimum_order' => 0, 'limit_penggunaan' => 50, 'expired_at' => Carbon::now()->addMonth()]);
        $voucherExpired = Voucher::create(['kode' => 'KADALUARSA', 'diskon_persen' => 20, 'diskon_nominal' => null, 'minimum_order' => 0, 'limit_penggunaan' => 10, 'expired_at' => Carbon::now()->subDays(1)]);

        // === 5. Seed Booking (untuk Dine-In) ===
        $mejaTersedia = Meja::where('tersedia', true)->first();
        $booking1 = Booking::create([
            'user_id' => $customer1->id,
            'meja_id' => $mejaTersedia->id,
            'tanggal' => Carbon::tomorrow()->setTime(19, 0),
            'waktu_selesai' => Carbon::tomorrow()->setTime(21, 0),
            'jumlah_orang' => 2,
            'status' => 'confirmed'
        ]);
        // Tandai meja tidak tersedia setelah di-booking
        $mejaTersedia->update(['tersedia' => false]);

        // === 6. Seed Order ===
        
        // --- Order Dine-In ---
        $orderItemsDineIn = [
            ['menu' => $menu1, 'qty' => 2],
            ['menu' => $menu4, 'qty' => 2],
        ];
        $totalDineIn = ($menu1->harga * 2) + ($menu4->harga * 2); // 70000 + 10000 = 80000
        $discountDineIn = $totalDineIn * ($voucherPersen->diskon_persen / 100); // 80000 * 0.15 = 12000
        $finalTotalDineIn = $totalDineIn - $discountDineIn; // 68000

        $orderDineIn = Order::create([
            'booking_id' => $booking1->id,
            'user_id' => $customer1->id,
            'total' => $finalTotalDineIn,
            'jenis_order' => 'dine_in',
            'status' => 'pending',
            'voucher_id' => $voucherPersen->id,
            'discount_amount' => $discountDineIn,
        ]);

        // --- Order Pickup ---
        $orderItemsPickup = [
            ['menu' => $menu3, 'qty' => 1],
            ['menu' => $menu5, 'qty' => 1],
        ];
        $totalPickup = ($menu3->harga * 1) + ($menu5->harga * 1); // 45000 + 15000 = 60000
        $discountPickup = $voucherNominal->diskon_nominal; // 25000
        $finalTotalPickup = $totalPickup - $discountPickup; // 35000

        $orderPickup = Order::create([
            'booking_id' => null, // Pickup tidak punya booking
            'user_id' => $customer2->id,
            'total' => $finalTotalPickup,
            'jenis_order' => 'pickup',
            'status' => 'paid',
            'voucher_id' => $voucherNominal->id,
            'discount_amount' => $discountPickup,
        ]);

        // === 7. Seed OrderItem ===
        foreach ($orderItemsDineIn as $item) {
            OrderItem::create([
                'order_id' => $orderDineIn->id,
                'menu_id' => $item['menu']->id,
                'qty' => $item['qty'],
                'harga' => $item['menu']->harga,
            ]);
        }

        foreach ($orderItemsPickup as $item) {
            OrderItem::create([
                'order_id' => $orderPickup->id,
                'menu_id' => $item['menu']->id,
                'qty' => $item['qty'],
                'harga' => $item['menu']->harga,
            ]);
        }

        // === 8. Seed Pickup ===
        Pickup::create([
            'user_id' => $customer2->id,
            'order_id' => $orderPickup->id,
            'nama_penerima' => 'Siti Nurhaliza',
            'catatan' => 'Jangan pakai es ya, thanks.',
            'status' => 'ready',
        ]);
    }

    /**
     * Truncate semua tabel yang terlibat untuk memastikan data bersih.
     */
    private function truncateTables()
    {
        // Urutan truncate harus benar (dari yang tidak punya foreign key ke yang punya)
        OrderItem::truncate();
        Pickup::truncate();
        Order::truncate();
        Booking::truncate();
        Voucher::truncate();
        Menu::truncate();
        Meja::truncate();
        MejaTipe::truncate();
        User::truncate();
    }
}