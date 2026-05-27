<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Amankan User Utama
        $user = User::firstOrCreate(
            ['name' => 'lutfiana'],
            ['password' => bcrypt('password'), 'balance' => 1000000]
        );

        // 2. Amankan Kategori Dasar jika belum ada
        $catMakan = Category::firstOrCreate(['name' => 'Makanan & Minuman'], ['color' => '#f43f5e']);
        $catBelanja = Category::firstOrCreate(['name' => 'Belanja'], ['color' => '#3b82f6']);

        // 3. Bersihkan sisa data lama agar tidak merusak format SQLite
        DB::table('incomes')->where('user_id', $user->id)->delete();
        DB::table('expenses')->where('user_id', $user->id)->delete();

        // 4. SUNTIK DATA DENGAN FORMAT TANGGAL VALID (NOW DITARIK MUNDUR PER MINGGU)

        // Blok Minggu Ini (Akan masuk ke batang paling kanan di grafik 1 Bulan)
        DB::table('incomes')->insert([
            'user_id' => $user->id, 'source' => 'gopay', 'description' => 'Gaji Pokok', 'amount' => 500000, 'created_at' => now(), 'updated_at' => now()
        ]);
        DB::table('expenses')->insert([
            'user_id' => $user->id, 'category_id' => $catMakan->id, 'source' => 'gopay', 'description' => 'Makan Siang', 'amount' => 75000, 'created_at' => now(), 'updated_at' => now()
        ]);

        // Blok 8 Hari Lalu (Masuk ke Minggu ke-3 di grafik)
        DB::table('incomes')->insert([
            'user_id' => $user->id, 'source' => 'mandiri', 'description' => 'Uang Saku', 'amount' => 300000, 'created_at' => now()->subDays(8), 'updated_at' => now()->subDays(8)
        ]);
        DB::table('expenses')->insert([
            'user_id' => $user->id, 'category_id' => $catBelanja->id, 'source' => 'mandiri', 'description' => 'Beli Jaket', 'amount' => 150000, 'created_at' => now()->subDays(8), 'updated_at' => now()->subDays(8)
        ]);

        // Blok 15 Hari Lalu (Masuk ke Minggu ke-2 di grafik)
        DB::table('expenses')->insert([
            'user_id' => $user->id, 'category_id' => $catMakan->id, 'source' => 'bank jago', 'description' => 'Makan Malam mewah', 'amount' => 200000, 'created_at' => now()->subDays(15), 'updated_at' => now()->subDays(15)
        ]);

        // Blok 22 Hari Lalu (Masuk ke Minggu ke-1 di grafik)
        DB::table('incomes')->insert([
            'user_id' => $user->id, 'source' => 'shopeepay', 'description' => 'Cashback', 'amount' => 45000, 'created_at' => now()->subDays(22), 'updated_at' => now()->subDays(22)
        ]);
    }
}
