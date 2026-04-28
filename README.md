# API Wallet

Project ini merupakan API sederhana untuk menangani transaksi member, meliputi:

* Cek saldo (inquiry)
* Deposit (penambahan saldo)
* Withdraw (penarikan saldo)

Tujuan utama dari project ini adalah memastikan proses transaksi berjalan dengan aman, terutama untuk mencegah saldo menjadi tidak konsisten saat ada request bersamaan.

---

## Struktur Database

Dalam implementasi ini saya menggunakan dua tabel utama, yaitu `members` dan `transactions`.

### Tabel members

Tabel ini digunakan untuk menyimpan data member dan saldo aktifnya.

Struktur kolom:

* id | BIGINT UNSIGNED | primary key, auto increment
* name | VARCHAR(255) | menyimpan nama member
* balance | DECIMAL(15,2) | menyimpan saldo aktif member
* created_at | TIMESTAMP | waktu data dibuat
* updated_at | TIMESTAMP | waktu terakhir data diperbarui

Catatan:
Saldo menggunakan tipe DECIMAL(15,2) karena lebih aman untuk data keuangan. Tipe seperti FLOAT bisa menyebabkan pembulatan yang tidak akurat.

---

### Tabel transactions

Tabel ini digunakan untuk mencatat semua aktivitas transaksi (deposit dan withdraw).

Struktur kolom:

* id | BIGINT UNSIGNED | primary key
* member_id | BIGINT UNSIGNED | relasi ke tabel members
* type | ENUM (deposit / withdraw) | jenis transaksi
* amount | DECIMAL(15,2) | nominal transaksi
* balance_before | DECIMAL(15,2) | saldo sebelum transaksi
* balance_after | DECIMAL(15,2) | saldo setelah transaksi
* created_at | TIMESTAMP | waktu transaksi dilakukan

Catatan:
Saya menyimpan balance_before dan balance_after untuk kebutuhan audit. Dengan cara ini, histori perubahan saldo bisa ditelusuri dengan jelas tanpa perlu menghitung ulang dari awal.

Relasi:
Satu member dapat memiliki banyak transaksi, dan setiap transaksi hanya dimiliki oleh satu member.

---

## API Documentation

Base URL:
http://localhost/api

Semua request dan response menggunakan format JSON.

Header yang digunakan:

* Content-Type: application/json
* Accept: application/json

---

## Format Response

Response success:

```json
{
  "status": "success",
  "message": "Pesan deskriptif",
  "data": { }
}
```

Response error:

```json
{
  "status": "error",
  "message": "Pesan error"
}
```

---

## Endpoint

### 1. Inquiry Balance

Endpoint ini digunakan untuk mengecek saldo member.

Method:
GET /api/wallet/inquiry/{member_id}

Contoh request:
GET /api/wallet/inquiry/1

Response success:

```json
{
  "status": "success",
  "message": "Inquiry balance berhasil.",
  "data": {
    "member_id": 1,
    "name": "Budi Indonesia",
    "balance": 500000
  }
}
```

Jika member tidak ditemukan:

```json
{
  "status": "error",
  "message": "Member tidak ditemukan."
}
```

---

### 2. Deposit

Endpoint ini digunakan untuk menambahkan saldo member.

Method:
POST /api/wallet/deposit

Contoh request:

```json
{
  "member_id": 1,
  "amount": 100000
}
```

Response success:

```json
{
  "status": "success",
  "message": "Deposit berhasil.",
  "data": {
    "member_id": 1,
    "name": "Budi Indonesia",
    "amount": 100000,
    "balance_before": 500000,
    "balance_after": 600000
  }
}
```

Jika validasi gagal:

```json
{
  "status": "error",
  "message": "The amount field must be at least 1000."
}
```

---

### 3. Withdraw

Endpoint ini digunakan untuk menarik saldo member.

Method:
POST /api/wallet/withdraw

Contoh request:

```json
{
  "member_id": 1,
  "amount": 50000
}
```

Response success:

```json
{
  "status": "success",
  "message": "Withdraw berhasil.",
  "data": {
    "member_id": 1,
    "name": "Budi Indonesia",
    "amount": 50000,
    "balance_before": 600000,
    "balance_after": 550000
  }
}
```

Jika saldo tidak mencukupi:

```json
{
  "status": "error",
  "message": "Saldo tidak mencukupi untuk melakukan penarikan."
}
```

Jika member tidak ditemukan:

```json
{
  "status": "error",
  "message": "Member tidak ditemukan."
}
```

---

## Catatan Implementasi

* Setiap transaksi disimpan ke tabel transactions
* Saldo tidak boleh minus
* Menggunakan database transaction untuk menjaga konsistensi data
* Menggunakan locking (lockForUpdate) untuk menghindari race condition saat ada request bersamaan

---

## Cara Menjalankan Project

Project ini menggunakan Laravel Sail (Docker), sehingga tidak perlu install PHP atau database secara manual.

Langkah menjalankan:

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

Setelah itu, sesuaikan konfigurasi database di file `.env` agar sesuai dengan service yang ada di Docker.

Contoh konfigurasi yang digunakan:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

Selanjutnya jalankan migration dan seeder:

```bash
./vendor/bin/sail artisan migrate --seed
```
