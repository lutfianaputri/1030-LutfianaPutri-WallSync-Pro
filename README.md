# WallSync - Personal Financial Dashboard

WallSync adalah aplikasi manajemen keuangan pribadi (*personal finance tracker*) berbasis web yang dirancang untuk memantau arus kas secara dinamis dan *real-time*. Aplikasi ini memungkinkan pengguna mencatat pemasukan, melacak pengeluaran berdasarkan kategori, serta menganalisis tren finansial melalui visualisasi grafik yang interaktif.

## Fitur Utama

- **Otentikasi Sesi Aman**: Sistem login terintegrasi yang menjaga keamanan data finansial pengguna.
- **Manajemen Arus Kas**: Pencatatan data pemasukan (`incomes`) dan pengeluaran (`expenses`) secara instan.
- **Kategori Dinamis**: Pengelompokkan pengeluaran berdasarkan kategori visual dengan kode warna khusus.
- **Dashboard Grafik Interaktif**: Visualisasi arus kas (7 Hari & 1 Bulan Terakhir) menggunakan Chart.js yang disinkronkan langsung dengan database.
- **Arsitektur SQLite Optimized**: Menggunakan kueri berbasis fungsi `strftime` bawaan untuk menjamin akurasi data statistik waktu pada *engine* SQLite lokal.

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templating, Vanilla JavaScript, Tailwind CSS
- **Library Grafik**: Chart.js
- **Database**: SQLite (Development) / PostgreSQL/MySQL ready

## Prasyarat Sistem

Sebelum memulai instalasi, pastikan komputer Anda telah terpasang:
- PHP >= 8.2
- Composer
- Node.js & NPM

## Dokumentasi
<img width="1920" height="1080" alt="Screenshot (489)" src="https://github.com/user-attachments/assets/78b17ed2-5094-4ac4-b914-54d17a11387c" />

<img width="1920" height="1080" alt="Screenshot (490)" src="https://github.com/user-attachments/assets/a8c9fef4-b279-4a41-b51a-a9b3312ae843" />

<img width="1920" height="1080" alt="Screenshot (491)" src="https://github.com/user-attachments/assets/189dd9c0-1bbf-4217-bed7-ed504debf2b5" />

<img width="1920" height="1080" alt="Screenshot (490)" src="https://github.com/user-attachments/assets/8a0264ea-23b7-4abf-949a-65dfc7717c3c" />

<img width="1920" height="1080" alt="Screenshot (489)" src="https://github.com/user-attachments/assets/d17e1562-5022-475e-8c97-dd5a3c2f23fd" />
