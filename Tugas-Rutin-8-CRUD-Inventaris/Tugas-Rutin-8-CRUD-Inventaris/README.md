# Tugas Rutin 8 — CRUD Inventaris Barang

Project ini dibuat untuk memenuhi requirement CRUD Inventaris menggunakan **PHP Native + PDO + MySQL**.

## 1. Requirement yang dipenuhi

1. Database `inventaris_db` dengan 3 tabel dan relasi foreign key.
2. Minimal 5 data seed pada setiap tabel.
3. Koneksi database menggunakan PDO dengan pola Singleton.
4. Halaman daftar produk menggunakan `JOIN` tabel produk, kategori, dan supplier.
5. Form Create dengan dropdown kategori dan supplier.
6. Input database menggunakan prepared statement.
7. Fitur Update dengan form pre-filled.
8. Fitur Delete dengan konfirmasi.
9. Output HTML menggunakan `htmlspecialchars()` melalui helper `e()`.
10. Flash message sukses/gagal menggunakan pola redirect.
11. UI responsif menggunakan CSS.
12. Bonus: pencarian dan pagination sederhana.

## 2. Struktur folder

```text
Tugas-Rutin-8-CRUD-Inventaris/
├── assets/
│   └── css/
│       └── style.css
├── config/
│   ├── database.php
│   └── helpers.php
├── partials/
│   ├── header.php
│   └── footer.php
├── create.php
├── database.sql
├── delete.php
├── edit.php
├── index.php
└── README.md
```

## 3. Cara menjalankan di XAMPP

### Langkah 1 — Simpan project

Ekstrak folder `Tugas-Rutin-8-CRUD-Inventaris` ke:

```text
C:\xampp_sbd\htdocs\
```

Jika folder XAMPP kamu berbeda, sesuaikan dengan lokasi `htdocs` milikmu.

### Langkah 2 — Aktifkan Apache dan MySQL

Buka XAMPP Control Panel lalu jalankan:

- Apache
- MySQL

### Langkah 3 — Buat database

Buka phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Pilih menu **Import**, kemudian pilih file:

```text
database.sql
```

Jalankan import sampai muncul pesan berhasil.

File SQL sudah membuat database `inventaris_db`, tiga tabel, foreign key, dan data awal.

### Langkah 4 — Cek konfigurasi database

File:

```text
config/database.php
```

Konfigurasi default:

```php
$host = 'localhost';
$dbName = 'inventaris_db';
$username = 'root';
$password = '';
```

Jika MySQL di komputer kamu menggunakan password root, ubah bagian `$password`.

### Langkah 5 — Buka aplikasi

Jika folder berada langsung di `htdocs`, buka:

```text
http://localhost/Tugas-Rutin-8-CRUD-Inventaris/
```

## 4. Pengujian CRUD

### Read
Halaman awal menampilkan data produk dan hasil `JOIN` dengan kategori serta supplier.

### Create
Klik **+ Tambah Produk**, isi seluruh form, lalu klik **Simpan Produk**.

### Update
Klik **Edit** pada salah satu data. Form akan otomatis berisi data lama.

### Delete
Klik **Hapus**. Browser akan menampilkan konfirmasi sebelum data dihapus.

## 5. Catatan keamanan

- Query dengan data dari pengguna menggunakan prepared statement.
- Output dari database/user input ditampilkan menggunakan `htmlspecialchars()`.
- Form POST dilengkapi token CSRF sederhana.
- ID divalidasi sebagai integer.
- Data penting divalidasi sebelum masuk database.
- Setelah operasi create/update/delete, aplikasi menggunakan redirect sehingga tidak terjadi pengiriman ulang form ketika halaman di-refresh.
