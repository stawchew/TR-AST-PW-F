# Pemesanan Tiket Travel & Shuttle Antar-Kota

Aplikasi web Laravel (CRUD, Multi-Role & Business Logic) untuk Tugas Rancang Pengembangan Aplikasi Web.
User memesan tiket shuttle & upload bukti transfer. Admin memverifikasi pembayaran, kursi dipotong otomatis, kode tiket unik terbit.

## Fitur Utama
- Auth (Laravel Breeze) + kolom `role` (`admin` / `user`) + `RoleMiddleware`
- CRUD rute & jadwal (Admin), katalog jadwal + cari/filter (User)
- Pemesanan tiket: validasi sisa kursi, total bayar otomatis (`jumlah_penumpang × harga_tiket`)
- Upload bukti transfer (`Storage::disk('public')`, file lama otomatis dihapus)
- Verifikasi Admin: **Approve** (`DB::transaction()` kurangi kursi + terbit kode tiket), **Reject**, **Completed**
- Ownership check: user tidak bisa akses tiket milik user lain (403)
- Dashboard dinamis per role (agregasi `count()` / `sum()`)
- Pencarian, filter status, `paginate()->withQueryString()`

## Skema Relasi Tabel

```
users 1 ──< tickets >── 1 schedules >── 1 routes
```

| Tabel       | Kolom Penting                                                                                          |
|-------------|--------------------------------------------------------------------------------------------------------|
| `users`     | id, name, email, password, role (`admin`/`user`)                                                       |
| `routes`    | id, origin, destination                                                                                |
| `schedules` | id, route_id (FK), departure_time, price, total_seats, available_seats                                 |
| `tickets`   | id, user_id (FK), schedule_id (FK), passenger_count, total_price, payment_proof, status, ticket_code (unique, nullable) |

Relasi Eloquent:
- `User hasMany Ticket`, `Ticket belongsTo User`
- `Route hasMany Schedule`, `Schedule belongsTo Route`
- `Schedule hasMany Ticket`, `Ticket belongsTo Schedule`

Status tiket: `pending` → `approved` → `completed`, atau `pending` → `rejected`.

## Logika Bisnis
1. User pesan tiket → server cek kursi cukup → hitung total otomatis → status `pending`.
2. User upload bukti transfer.
3. Admin **Approve** dalam `DB::transaction()`:
   - lock baris tiket & jadwal (`lockForUpdate`)
   - cek status masih `pending` & kursi cukup
   - `available_seats` dikurangi `passenger_count`
   - `ticket_code` unik diterbitkan (format `TKT-XXXXXXXX`)
4. Admin tandai **Completed** setelah perjalanan selesai, atau **Reject** bila pembayaran tidak valid.

## Instalasi

```bash
git clone https://github.com/stawchew/TR-AST-PW-F.git
cd TR-AST-PW-F

composer install && composer update
npm install && npm run build

copy .env.example .env
php artisan key:generate
# atur DB_DATABASE, DB_USERNAME, DB_PASSWORD di file .env

php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buka http://127.0.0.1:8000

## Akun Demo

| Role  | Email             | Password   |
|-------|-------------------|------------|
| Admin | admin@tiket.test  | password   |
| User  | budi@tiket.test   | password   |

> Sesuaikan email/password dengan isi `DatabaseSeeder` di repo.

## Pembagian Tugas
| Anggota | Tugas |
|---------|-------|
| 1 | Setup proyek, migrasi & seeder database, autentikasi multi-role, dan RoleMiddleware |
| 2 | CRUD data master rute (routes) termasuk upload gambar dan jadwal keberangkatan (schedules) |
| 3 | Dashboard user, katalog jadwal, pemesanan tiket (kalkulasi otomatis & upload bukti), edit/batal tiket, serta ownership check |
| 4 | Verifikasi tiket, dashboard admin, dokumentasi (README) |

