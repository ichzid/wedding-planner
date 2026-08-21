# Wedding Planner App

Aplikasi pengelolaan pernikahan berbasis web dengan fitur lengkap untuk memudahkan persiapan hari istimewa Anda. Dibangun dengan stack modern **Laravel + Inertia.js + Vue.js + MySQL**, dengan tema desain **Rose Gold** yang elegan dan minimalis.

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | PHP 8.3, Laravel 13.x, Eloquent ORM |
| **Frontend** | Vue 3 (Composition API / `<script setup>`), Inertia.js 3.x |
| **UI Styling** | Tailwind CSS 3, Custom Theme CSS (Rose Gold Palette) |
| **Database** | MySQL (via Migration Laravel) |
| **Export** | SheetJS / `xlsx` library (format .xlsx) |
| **Routing** | Ziggy JS (Laravel route di sisi Vue) |
| **Auth** | Laravel Breeze (Login, Register, Reset Password, Verifikasi Email) |
| **Build Tool** | Vite 8 |

---

## 📋 Daftar Menu Utama

Sidebar menu dikelompokkan menjadi 2 kategori: **Menu Utama** dan **Pengaturan**.

### 🏠 Menu Utama
1. **Dashboard** — Ringkasan keseluruhan progress pernikahan
2. **Checklist** — Daftar tugas persiapan pernikahan
3. **Budget** — Pengelolaan anggaran & realisasi biaya
4. **Seserahan** — Daftar barang seserahan / mahar
5. **Dokumen KUA** — Kelengkapan administrasi KUA
6. **Daftar Undangan** — Manajemen tamu undangan

### ⚙️ Pengaturan
7. **Pengaturan Akun** — Profil user, password, & informasi pernikahan
8. **Keluar** — Logout dari aplikasi

---

## 🧩 Fitur Detail per Menu

### 1. 🔐 Autentikasi (Login / Register)
- **Halaman Login** — Masuk dengan email & password
- **Halaman Register** — Daftar akun baru (nama, email, password)
- **Lupa Password** — Kirim link reset password via email
- **Verifikasi Email** — Verifikasi alamat email setelah registrasi
- **Konfirmasi Password** — Validasi password sebelum aksi sensitif

---

### 2. 🚀 Onboarding Wizard (Setup Awal)
Halaman wizard multi-step yang muncul **hanya jika user belum memiliki data pernikahan**.

| Step | Fitur |
|------|-------|
| **Step 1 — Data Pasangan** | Input nama Mempelai Wanita (CPW) & Mempelai Pria (CPP), Tanggal Akad Nikah |
| **Step 2 — Pilih Cara Memulai** | Pilihan **Otomatis** (rekomendasi template) atau **Manual** (kosong, input sendiri) |
| **Step 3 — Setup Otomatis (jika dipilih)** | Input total budget → auto-generate 7 item budget (Katering 40%, Venue 15%, Dekorasi 15%, MUA 10%, Dokumentasi 8%, Undangan 5%, Lain-lain 7%) + 9 checklist persiapan standar pernikahan Indonesia |
| **Step 4 — Konfirmasi** | Review data sebelum submit |

Fitur tambahan:
- Tombol **Kembali** di setiap step
- Tombol **Batalkan & Keluar** di Step 1
- Dot navigation untuk jump ke step tertentu
- Validasi: Step 2 WAJIB pilih mode sebelum lanjut

---

### 3. 📊 Dashboard
Ringkasan seluruh data pernikahan dalam 1 halaman.

**Widget Informasi Pernikahan:**
- Nama CPW & CPP
- Tanggal Akad
- Lokasi Akad & Resepsi

**Widget Progress:**
| Widget | Isi |
|--------|-----|
| **Checklist** | Total tugas, Selesai, Progress % (dengan progress bar) + 3 checklist pending teratas |
| **Wedding Budget** | Total Estimasi (Rp), Total Sudah Dibayar (Rp), Progress % |
| **Seserahan** | Total Item, Sudah Dibeli, Belum Dibeli |
| **Dokumen KUA** | Total Dokumen, Selesai CPW, Selesai CPP, Selesai Keduanya |
| **Daftar Undangan** | Total Tamu, Pihak Wanita, Pihak Pria, Konfirmasi Hadir |
| **Budget per Kategori** | Rekap grouping budget berdasarkan kategori (estimasi vs aktual) |

---

### 4. ✅ Checklist Pernikahan
Pengelolaan daftar tugas persiapan pernikahan.

**Fitur:**
- **Grouping by Bulan** — Item dikelompokkan berdasarkan rentang bulan (contoh: "H-12 s/d 11 Bulan")
- **Toggle Selesai** — Klik lingkaran di kiri untuk tandai selesai/belum (strikethrough otomatis)
- **CRUD Item** — Tambah, Edit (Copy), Hapus
  - Field: Nomor, Bulan Range, Nama Persiapan, Detail
- **Drag & Drop Reorder** — Susun ulang item via drag (bisa cross-group ke bulan lain)
- **Toolbar Filter:**
  - Search box (cari nama persiapan)
  - Filter Bulan
  - Filter Status (Selesai / Belum)
  - Reset Filter
  - **Export Excel**
- **Empty State** — Tampilan friendly jika belum ada data

---

### 5. 💰 Wedding Budget
Pengelolaan anggaran pernikahan secara detail.

**Summary Cards (3 kolom):**
- Total Estimasi Budget (Rp) — jumlah item
- Sudah Dibayar — dengan progress bar
- Sisa Tagihan — rincian DP vs Pelunasan

**Tabel Budget:**
| Kolom | Keterangan |
|-------|------------|
| No | Nomor urut + drag handle |
| Kategori | Chip berwarna (Katering, Venue, Dekorasi, MUA, dll) |
| Item / Vendor | Nama item + nama vendor (jika ada) |
| Sumber Dana | **CPP** (Pria) / **CPW** (Wanita) |
| Est. Budget | Rupiah (sortable) |
| **Realisasi (2 kolom)** | DP + Pelunasan |
| Sisa | Estimasi − DP − Pelunasan (merah jika sisa > 0, hijau jika surplus) |
| Status | Belum / DP Terbayar / Lunas |
| Aksi | Copy · Edit · Hapus |

**Fitur Tambahan:**
- **Sumber Dana** — Kolom khusus untuk membedakan biaya dari pihak mana
- **Drag & Drop Reorder** — Susun urutan item
- **Sorting** — Klik header kolom (Kategori, Estimasi, Status) untuk sort asc/desc
- **Filter:** Search, Kategori, Status
- **Export Excel** — dengan baris TOTAL di akhir
- **Copy Row** — Duplikasi item budget untuk variasi vendor

---

### 6. 🎁 List Seserahan
Daftar barang seserahan / mahar pernikahan.

**Summary Cards:**
- Total Item (Sudah Dibeli / Belum)
- Progress % dengan progress bar
- Total Harga Keseluruhan (Rp)

**Tabel Seserahan:**
| Kolom | Keterangan |
|-------|------------|
| No | Nomor + drag handle |
| Nama Item & Kategori | Nama barang + chip kategori |
| **Untuk** | **CPP** (Pria) · **CPW** (Wanita) · **Kedua Mempelai** |
| Qty | Jumlah barang + Satuan |
| Harga Satuan | Rupiah |
| Total | Qty × Harga Satuan |
| Status | Sudah Dibeli / Belum Dibeli |
| Aksi | Copy · Edit · Hapus |

**Khusus Field "Untuk":**
- ENUM database: `cpp`, `cpw`, `kedua`
- 3 pilihan di select dropdown
- Badge warna berbeda untuk setiap opsi

**Fitur Lain:**
- Placeholder "Pilih Kategori" dan "Pilih Tujuan" di form select
- Drag & Drop Reorder
- Filter: Search & Status
- Export Excel

---

### 7. 📄 Dokumen KUA
Checklist kelengkapan dokumen administrasi KUA untuk kedua mempelai.

**Summary Cards (4 kolom):**
- Total Dokumen
- Mempelai Wanita Siap (jumlah / total + progress bar)
- Mempelai Pria Siap (jumlah / total + progress bar)
- Total Biaya (Rp)

**Tabel Dokumen:**
| Kolom | Keterangan |
|-------|------------|
| No | Nomor + drag handle |
| Nama Dokumen | Contoh: N1, N2, N4, KTP, KK, Pas Foto, dll |
| Biaya | Biaya pengurusan (Rp) |
| **CPW Status** | Toggle button (click to mark) — Selesai/Belum untuk pihak Wanita |
| **CPP Status** | Toggle button — Selesai/Belum untuk pihak Pria |
| Catatan | Keterangan tambahan |
| Aksi | Copy · Edit · Hapus |

**Fitur:**
- Toggle terpisah untuk CPW dan CPP (click langsung tanpa buka modal)
- Row highlight jika kedua status sudah selesai
- Drag & Drop Reorder
- Filter: Search & Status (Selesai Kedua Pihak / Belum Selesai)
- Export Excel

---

### 8. 👥 Daftar Undangan
Manajemen tamu undangan pernikahan.

**Summary Cards (4 kolom):**
- Total Tamu — dengan jumlah konfirmasi hadir
- Mempelai Wanita — jumlah tamu dari pihak CPW
- Mempelai Pria — jumlah tamu dari pihak CPP
- Konfirmasi Hadir — dari total tamu

**Tabel Undangan:**
| Kolom | Keterangan |
|-------|------------|
| No | Nomor + drag handle |
| Nama Tamu | Nama lengkap tamu |
| Pihak | CPW (Wanita) / CPP (Pria) / Umum |
| Status Undangan | Belum Dikirim · Sudah Dikirim · Hadir · Tidak Hadir |
| Catatan | Catatan tambahan (nomor meja, dll) |
| Aksi | Copy · Edit · Hapus |

**Fitur:**
- Drag & Drop Reorder
- Filter: Search (nama), Pihak, Status
- Export Excel
- Badge dengan warna berbeda untuk setiap status & pihak

---

### 9. ⚙️ Pengaturan Akun
Halaman yang berisi 3 section dalam card terpisah:

| Section | File Component | Fitur |
|---------|---------------|-------|
| **Update Profile Information** | `UpdateProfileInformationForm.vue` | Ubah Nama, Email, Verifikasi Email status |
| **Update Wedding Information** | `UpdateWeddingInformationForm.vue` | Ubah Nama CPW, Nama CPP, Tanggal Akad, Lokasi Akad, Lokasi Resepsi |
| **Update Password** | `UpdatePasswordForm.vue` | Ganti password (current password + new password + confirm) |

> **Note:** Form "Delete User" tersedia di codebase tapi di-hidden (uncomment jika dibutuhkan).

---

## 📤 Fitur Export Excel (Universal)
**DITERAPKAN DI SEMUA MENU:** Budget, Checklist, Seserahan, Dokumen KUA, Daftar Undangan.

**Library:** `xlsx` (SheetJS) — format file **.xlsx** (bukan CSV).

**Format Dokumen Standar:**
```
Baris 1:  [MERGE] JUDUL DOKUMEN (contoh: "WEDDING BUDGET - RENCANA PERNIKAHAN")
Baris 2:  [MERGE] "Dicetak pada: dd/mm/yyyy HH:mm:ss"
Baris 3:  (kosong)
Baris 4:  HEADER TABEL (No, Kolom1, Kolom2, dst.)
Baris 5+: DATA BARIS
Baris AKHIR: BARIS TOTAL (khusus Budget & Seserahan)
```

**Khusus Budget & Seserahan:**
- Baris **TOTAL** di akhir dengan penjumlahan otomatis
- Kolom nominal diformat **Rupiah** di Excel
- **Auto-width** kolom agar tidak terpotong saat dibuka MS Excel

**Yang Diekspor:**
✅ **Data sesuai filter aktif** (bukan semua data) — artinya jika user filter "Status: Hadir" di Daftar Undangan, yang ke-export hanya yang hadir saja.
✅ Selalu ada kolom **No** (nomor urut) di kolom pertama.

---

## 🎨 UI/UX Design System

### Tema Warna: Rose Gold Palette
| Variable | Hex | Kegunaan |
|----------|-----|----------|
| `--bg` | `#faf6f3` | Background utama (warm cream) |
| `--surface` | `#fffdfb` | Card surface |
| `--rose` | (custom) | Primary accent (Rose Gold) |
| `--text` | `#4a2c2a` | Text utama (warm brown) |
| `--text-muted` | `#8b6f6a` | Text secondary |

### Typography
- **Body:** Inter (400-800)
- **Display/Heading:** Playfair Display (serif untuk nuansa mewah)

### Layout Shell
- **Sidebar (kiri):** Brand Logo + Menu Navigasi
  - Header sidebar tinggi: `var(--topbar-h) = 56px` (sejajar dengan Topbar border)
  - Mobile: Drawer dengan overlay
- **Topbar (atas):**
  - Breadcrumb dinamis (Home → Nama Halaman)
  - Greeting user (Selamat Pagi/Siang/Sore/Malam — auto by jam)
  - Tanggal hari ini (format Indonesia)
  - Avatar User Dropdown (Profil / Keluar)
- **Content Area:** Card-based dengan shadow lembut

### Komponen UI Universal
- **Summary Cards Grid** — 3-4 kolom ringkasan di setiap halaman
- **Toolbar** — Search + Select Filter + Reset + Export Excel (height seragam 38px)
- **Data Table** — Responsive scroll horizontal, hover highlight, sortable header
- **Modal CRUD** — Create / Edit / Copy / Delete (dengan konfirmasi hapus)
- **Chip / Badge** — Status, Kategori, Pihak dengan warna semantic
- **Progress Bar** — Track + fill (rose color)
- **Drag & Drop Rows** — Semua tabel mendukung reorder via HTML5 Drag API
- **Toast Notification** — Flash message (success/error) dengan auto dismiss

---

## 🗄️ Ringkasan Tabel Database (Utama)

| Tabel | Keterangan |
|-------|------------|
| `users` | Data akun user (Laravel Breeze) |
| `weddings` | Data pernikahan (user_id, nama_cpw, nama_cpp, tanggal_nikah, lokasi_akad, lokasi_resepsi) |
| `wedding_budget` | Item budget (kategori, item, vendor, estimasi_budget, dp, pelunasan, **sumber_dana** [cpp/cpw], status) |
| `wedding_checklist` | Checklist (bulan_range, persiapan, detail, status boolean) |
| `seserahan_list` | Seserahan (nama_item, kategori, **untuk** [cpp/cpw/`kedua`], qty, satuan, harga, status) |
| `kua_documents` | Dokumen KUA (nama_dokumen, biaya, cpw_status, cpp_status, catatan) |
| `wedding_guests` | Daftar Undangan (nama_tamu, pihak [cpp/cpw/umum], status, catatan) |

---

## 🚀 Instalasi & Menjalankan

### Prasyarat
- PHP 8.3+
- Composer
- Node.js 18+ & npm
- MySQL 8+ atau MariaDB

### Setup (Satu Perintah)
```bash
composer run setup
```

### Setup Manual
```bash
# 1. Install dependensi PHP
composer install

# 2. Copy env & generate key
cp .env.example .env
php artisan key:generate

# 3. Setting database di .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 4. Jalankan migrasi
php artisan migrate --force

# 5. Install dependensi JS & build
npm install
npm run build
```

### Development Mode (Auto-reload)
```bash
composer run dev
```
Atau jalankan terpisah:
```bash
php artisan serve   # Backend: http://localhost:8000
npm run dev          # Vite HMR
```

### Build Production
```bash
npm run build
```

---

## 🖥️ Deployment ke Server (Ubuntu / aaPanel / BT Panel)

```bash
# 1. Pull perubahan terbaru
cd /www/wwwroot/nikah.domainmu.my.id

# (Jika ada konflik package-lock.json)
git checkout -- package-lock.json
# ATAU
git stash

git pull origin main

# 2. Install/update dependensi
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Jalankan migrasi (HATI-HATI di production!)
php artisan migrate --force

# 4. Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📂 Struktur Direktori Penting

```
wedding-planner/
├── app/Http/Controllers/
│   ├── OnboardingController.php     # Setup awal wizard
│   ├── DashboardController.php      # Dashboard
│   ├── BudgetController.php         # CRUD Budget
│   ├── ChecklistController.php      # CRUD Checklist
│   ├── SeserahanController.php      # CRUD Seserahan
│   ├── KuaDocumentController.php    # CRUD Dokumen KUA
│   ├── GuestController.php          # CRUD Daftar Undangan
│   └── ProfileController.php        # Profil + Pengaturan Wedding
│
├── resources/js/Pages/
│   ├── Onboarding/Index.vue         # Multi Step Wizard
│   ├── Dashboard.vue                # Dashboard
│   ├── Budget/Index.vue             # Budget Page
│   ├── Checklist/Index.vue          # Checklist Page
│   ├── Seserahan/Index.vue          # Seserahan Page
│   ├── Kua/Index.vue                # Dokumen KUA Page
│   ├── Guest/Index.vue              # Daftar Undangan Page
│   ├── Profile/Edit.vue             # Pengaturan Akun
│   │   └── Partials/
│   │       ├── UpdateProfileInformationForm.vue
│   │       ├── UpdateWeddingInformationForm.vue
│   │       └── UpdatePasswordForm.vue
│   └── Auth/                        # Login, Register, Forgot, dll
│
├── resources/js/Layouts/
│   └── AppLayout.vue                # Sidebar, Topbar, Breadcrumb, Toast
│
└── database/migrations/             # Schema DB
    └── 2026_06_02_050100_add_kedua_option_to_seserahan_untuk_enum.php
```

---

*Dokumentasi ini dibuat sebagai referensi lengkap fitur Wedding Planner App.*
