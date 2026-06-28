# 🛒 Kasir POS Sembako

Aplikasi Point of Sale (POS) berbasis web yang dikembangkan menggunakan Laravel untuk membantu proses penjualan pada toko sembako. Sistem ini menyediakan fitur manajemen produk, transaksi penjualan, laporan pemasukan, serta pencatatan jurnal umum secara otomatis.

---

## Demo

> Coming Soon

---

## Tech Stack

- Laravel 12
- PHP 8.3
- MySQL
- Bootstrap 5
- JavaScript
- jQuery
- HTML5
- CSS3

---

## Features

### Admin

- Dashboard
- Login Authentication
- CRUD Produk
- CRUD Kategori
- CRUD Supplier
- CRUD User
- Transaksi Penjualan
- Cetak Struk
- Laporan Penjualan
- Laporan Pemasukan
- Jurnal Umum Otomatis

### User

- Dashboard
- Transaksi Penjualan
- Riwayat Transaksi
- Cetak Struk

---

## Screenshots

### Dashboard

![Dashboard](images/dashboard.png)

### Produk

![Produk](images/produk.png)

### Transaksi

![Transaksi](images/transaksi.png)

### Laporan

![Laporan](images/laporan.png)

---

## Database

Relasi database terdiri dari:

- users
- products
- categories
- suppliers
- sales
- sale_details
- journals

---

## Installation

```bash
git clone repository

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate

php artisan db:seed

php artisan serve
```

---

## My Contribution

Project ini saya kembangkan mulai dari tahap analisis kebutuhan hingga implementasi.

Beberapa bagian yang saya kerjakan:

- UI Dashboard
- Authentication
- CRUD Master Data
- POS Transaction
- Report System
- General Journal
- Database Design
- Laravel Backend
- Responsive Layout

---

## Future Improvement

- Export Excel
- Barcode Scanner
- Stock Notification
- Multi Outlet
- Dashboard Analytics

---

## Author

**Ryo Fahrezi**

Laravel Developer