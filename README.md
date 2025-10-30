# CafeBookingBackend

Backend resmi untuk aplikasi **CafeBooking**, sistem pemesanan kafe yang mengelola data pengguna, meja, jadwal reservasi, serta autentikasi admin dan pelanggan.  
Dikembangkan menggunakan **Laravel (PHP)** dan **MySQL** sebagai database utama.

---

## 1. Clone Repository

Jika kamu baru pertama kali ingin mengambil project ini:

```bash
git clone https://github.com/cutieziaa/Cafebookingbackend.git
cd Cafebookingbackend
```

Kemudian buka project di **Visual Studio Code**:
```bash
code .
```

---

## 2. Persiapan & Instalasi

Pastikan kamu sudah menginstal hal berikut:
- [Git](https://git-scm.com/)
- [PHP ≥ 8.1](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [MySQL](https://www.mysql.com/)
- [VS Code](https://code.visualstudio.com/)

### Langkah Setup:
1. Install dependensi Laravel:
   ```bash
   composer install
   ```

2. Duplikat file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```

3. Buat **App Key** Laravel:
   ```bash
   php artisan key:generate
   ```

---

## 3. Setup Database

1. Buka **phpMyAdmin** atau terminal MySQL, kemudian buat database baru:
   ```sql
   CREATE DATABASE cafebooking;
   ```

2. Ubah konfigurasi di file `.env` sesuai database kamu, contoh:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cafebooking
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. Jalankan migrasi tabel ke database:
   ```bash
   php artisan migrate
   ```

4. (Opsional) Jika terdapat data awal (seeding):
   ```bash
   php artisan db:seed
   ```

---

## 4. Menjalankan Server Lokal

Setelah semua siap, jalankan perintah berikut untuk memulai backend:

```bash
php artisan serve
```

Server akan berjalan di:
 http://127.0.0.1:8000  

---

## 5. Workflow di VS Code (Commit & Push)

### a. Update kode terbaru
Sebelum mulai edit:
```bash
git checkout main
git pull origin main
```

### b. Melakukan perubahan
Edit file yang diperlukan di VS Code.

### c. Commit & Push
```bash
git status
git add .
git commit -m "Menambahkan fitur baru pada modul booking"
git push origin main
```

---

## 6. Membuat Branch untuk Perbaikan atau Fitur Baru

Agar tidak langsung mengubah branch `main`, buat branch terpisah:

1. Buat dan pindah ke branch baru:
   ```bash
   git checkout -b nama-branch-baru
   ```
   Contoh:  
   ```bash
   git checkout -b fix-validasi-reservasi
   ```

2. Lakukan perubahan kode di branch ini.

3. Simpan dan kirim ke GitHub:
   ```bash
   git add .
   git commit -m "Memperbaiki validasi input pada form reservasi"
   git push origin nama-branch-baru
   ```

4. Buka GitHub → buat **Pull Request** ke `main` agar perubahan bisa direview dan digabungkan.

---

## 7. Menyinkronkan Branch dengan Main

Jika branch utama (`main`) berubah, sinkronkan branch kerjamu agar tetap update:
```bash
git checkout main
git pull origin main
git checkout nama-branch-baru
git merge main
```

Jika terjadi konflik:
- Buka file yang bermasalah di VS Code.
- Selesaikan konflik.
- Lalu commit ulang:
  ```bash
  git add .
  git commit -m "Selesaikan konflik merge"
  ```

---

## 8. Ringkasan Perintah Git

| Kebutuhan                            | Perintah Git |
|-------------------------------------|---------------|
| Clone repository                    | `git clone <url>` |
| Masuk folder proyek                 | `cd Cafebookingbackend` |
| Cek status perubahan                | `git status` |
| Tambahkan file ke commit            | `git add .` |
| Buat commit                         | `git commit -m "pesan"` |
| Kirim ke GitHub                     | `git push origin <branch>` |
| Tarik update dari GitHub            | `git pull origin <branch>` |
| Buat branch baru                    | `git checkout -b <nama-branch>` |
| Pindah branch                       | `git checkout <nama-branch>` |
| Gabungkan branch                    | `git merge <nama-branch>` |

---

## 9. Struktur Folder Umum

```
Cafebookingbackend/
├── app/
│   ├── Http/
│   ├── Models/
│   └── Providers/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── .env
├── composer.json
└── README.md
```

---

## Tips Kolaborasi
- Gunakan nama branch deskriptif seperti `feature/add-payment` atau `bugfix/fix-auth`.  
- Lakukan **commit kecil & sering** agar mudah dilacak.  
- Sebelum membuat PR, pastikan kode sudah diuji secara lokal.  
- Jika mengubah struktur database, selalu tambahkan migrasi baru, jangan ubah migrasi lama.

---

## Author
**Cutieziaa & Team**  
📧 [GitHub Profile](https://github.com/cutieziaa)

📅 **Terakhir Diperbarui:** 30 Oktober 2025  

---
