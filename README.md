# Sistem Inventaris Laboratorium Komputer & Informatika

## Mata Kuliah Pemrograman Web I

### Program Studi Teknik Komputer
---

## Kelompok 4

| No | Nama                 |NIM                |
| -- | -------------------- |-------------------| 
| 1  | Ardina Jihan Mariska |H1H024018| 
| 2  | Difa’ Tamaya Maulidina Adz Dzikro|H1H024019| 
| 3  | Nesa Dwi Cahyani|H1H024024| 

---

## Link Hosting
http://kelompok4-kelas-a.tekkom.web.id/login.php

## Deskripsi Sistem

Sistem Inventaris Laboratorium Komputer & Informatika merupakan aplikasi berbasis web yang digunakan untuk membantu pengelolaan inventaris alat laboratorium, proses peminjaman alat, pengembalian alat, serta monitoring status peminjaman secara terpusat.

Sistem ini menyediakan dua jenis hak akses, yaitu Administrator dan User. Administrator bertugas mengelola data alat dan memverifikasi peminjaman, sedangkan User dapat melakukan pengajuan peminjaman serta memantau status peminjaman yang dilakukan.

---

## Fitur Sistem

### Administrator

* Login Administrator
* Dashboard Monitoring
* Manajemen Data Alat
* Upload Foto Alat
* Manajemen Data Pengguna
* Persetujuan dan Penolakan Peminjaman
* Pengelolaan Pengembalian Alat
* Monitoring Status Inventaris
* Pencarian dan Filter Data Alat

### User

* Registrasi Akun
* Login User
* Melihat Data Alat
* Melihat Detail Alat
* Pencarian dan Filter Alat
* Pengajuan Peminjaman Alat
* Monitoring Status Peminjaman
* Riwayat Peminjaman
* Panduan Peminjaman dan Pengembalian

---

## Teknologi yang Digunakan

* PHP Native
* MySQL
* HTML
* CSS
* JavaScript
* Bootstrap 5
* Bootstrap Icons
* Laragon

---

## Struktur Database

Tabel utama yang digunakan:

1. user
2. alat
3. peminjaman

---

## Alur Peminjaman

```text
User Mengajukan Peminjaman
            ↓
Status Menunggu Persetujuan
            ↓
Admin Menyetujui / Menolak
      ↓                 ↓
 Dipinjam           Ditolak
      ↓
Pengembalian Alat
      ↓
Dikembalikan
```

---

## Akun Pengujian

| Role  | Username | Password |
| ----- | -------- | -------- |
| Admin | admin    | admin123 |
| User  | user1    | user123  |

---
## Kesimpulan

Sistem Inventaris Laboratorium Komputer & Informatika dikembangkan untuk membantu proses pengelolaan inventaris alat, peminjaman, dan pengembalian alat laboratorium secara lebih terstruktur dan terdokumentasi.

Melalui sistem ini, administrator dapat melakukan pengelolaan data alat dan monitoring aktivitas peminjaman, sedangkan pengguna dapat mengajukan peminjaman serta memantau status peminjaman secara online. Dengan adanya sistem ini, proses administrasi laboratorium diharapkan menjadi lebih efektif, efisien, dan mudah dipantau.
