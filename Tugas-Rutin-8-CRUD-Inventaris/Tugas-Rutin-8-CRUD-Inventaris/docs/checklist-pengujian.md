# Checklist Pengujian Tugas Rutin 8

Gunakan checklist ini sebelum screenshot/presentasi.

## Database

- [ ] Database `inventaris_db` sudah dibuat.
- [ ] Tabel `kategori` ada.
- [ ] Tabel `supplier` ada.
- [ ] Tabel `produk` ada.
- [ ] Foreign key `produk.kategori_id -> kategori.id` ada.
- [ ] Foreign key `produk.supplier_id -> supplier.id` ada.
- [ ] Minimal 5 data kategori.
- [ ] Minimal 5 data supplier.
- [ ] Minimal 5 data produk.

## CRUD

- [ ] Halaman daftar produk dapat dibuka.
- [ ] Kategori tampil dari hasil JOIN.
- [ ] Supplier tampil dari hasil JOIN.
- [ ] Create berhasil.
- [ ] Dropdown kategori bekerja.
- [ ] Dropdown supplier bekerja.
- [ ] Update berhasil.
- [ ] Form update otomatis terisi data lama.
- [ ] Delete menampilkan konfirmasi.
- [ ] Delete berhasil.
- [ ] Flash message muncul setelah create/update/delete.

## Keamanan dasar

- [ ] Query input memakai prepared statement.
- [ ] Output menggunakan `htmlspecialchars()` melalui helper `e()`.
- [ ] POST delete digunakan, bukan GET delete.
- [ ] Token CSRF ada pada form POST.

## Bonus

- [ ] Search produk bekerja.
- [ ] Pagination bekerja jika data lebih dari 6.
