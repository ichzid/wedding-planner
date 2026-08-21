# Ruang Pasangan — Business Process Specification

## 1. Tujuan Dokumen

Dokumen ini menjelaskan **proses bisnis dan alur pengguna** untuk funnel Ruang Pasangan, mulai dari traffic Instagram/TikTok sampai user menjadi pengguna Wedding Planner.

Dokumen ini **bukan dokumentasi teknis UI atau daftar seluruh fitur aplikasi**.

Fokus utama:

```text
Sosial Media
    ↓
Landing Page
    ↓
Budget Estimator
    ↓
Hasil Estimasi
    ↓
Simpan Hasil
    ↓
Login / Register
    ↓
Onboarding
    ↓
Existing Wedding Planner
```

---

# 2. Business Model

Ruang Pasangan memiliki dua komponen utama:

### A. Content / Audience

Instagram dan TikTok digunakan untuk membangun audience melalui konten seputar:

- Persiapan menikah
- Hubungan pasangan
- Keuangan pasangan
- Wedding planning
- Rumah tangga
- Kehidupan setelah menikah

Format konten dapat berupa carousel, Reels, TikTok, dan format lainnya.

### B. Wedding Planner

Wedding Planner adalah produk utama yang digunakan setelah user tertarik dan membuat rencana pernikahan.

Budget Estimator berfungsi sebagai **entry point gratis / lead magnet**, bukan sebagai produk utama.

---

# 3. Prinsip Funnel

Prinsip utama:

> **Value dulu → commitment kemudian.**

User tidak langsung diminta:

- Login
- Email
- Password
- Nama CPP
- Nama CPW
- Tanggal pernikahan

User terlebih dahulu diberikan value melalui Budget Estimator.

Setelah user mendapatkan hasil dan ingin menyimpannya, barulah user diminta login/register.

Setelah user benar-benar ingin membuat Wedding Plan, barulah data pernikahan diminta.

---

# 4. Funnel Utama

```text
INSTAGRAM / TIKTOK
        ↓
LANDING PAGE
        ↓
"Hitung Budget Nikah Gratis"
        ↓
BUDGET ESTIMATOR
        ↓
HASIL ESTIMASI
        ↓
"Simpan Hasil & Buat Wedding Plan"
        ↓
LOGIN / REGISTER
        ↓
ONBOARDING
        ↓
BUDGET AWAL
(bisa diedit)
        ↓
OTOMATIS / MANUAL
        ↓
NAMA CPP
NAMA CPW
TANGGAL PERNIKAHAN
        ↓
EXISTING WEDDING PLANNER
```

---

# 5. Tahap 1 — Traffic dari Sosial Media

User berasal dari Instagram atau TikTok Ruang Pasangan.

Konten memberikan edukasi atau informasi yang relevan dengan masalah calon pengantin.

Contoh:

> "Budget nikah Rp30 juta cukup nggak?"

CTA:

> **💰 Hitung estimasi budget nikahmu gratis di link bio.**

CTA tersebut mengarahkan user ke:

```text
/budget-estimator
```

Untuk konten yang memperkenalkan produk secara umum, user dapat diarahkan ke Landing Page.

---

# 6. Tahap 2 — Landing Page

Landing Page adalah **halaman marketing**, bukan halaman estimator.

Landing Page bertugas menjawab:

> **"Apa itu Ruang Pasangan dan mengapa saya harus menggunakannya?"**

## Isi utama

### Hero

Pesan utama:

> **Siapkan Pernikahanmu Tanpa Bikin Pusing**

Subheadline:

> Atur budget, persiapan, seserahan, dokumen, dan daftar undangan dalam satu tempat.

CTA utama:

> **Hitung Budget Nikah Gratis**

CTA mengarah ke:

```text
/budget-estimator
```

### Problem

Jelaskan masalah umum calon pengantin:

- Budget sulit dikontrol
- Banyak persiapan yang harus diingat
- Seserahan belum terorganisir
- Dokumen belum lengkap
- Data undangan berantakan

### Product Value

Jelaskan bahwa Ruang Pasangan membantu mengatur persiapan pernikahan dalam satu tempat.

### Feature Overview

Tampilkan fitur utama Wedding Planner yang sudah tersedia:

- Dashboard
- Daftar Persiapan
- Anggaran Pernikahan
- Seserahan
- Dokumen KUA
- Daftar Undangan

Tidak perlu membangun fitur baru untuk kebutuhan MVP funnel ini.

### Cara Kerja

Jelaskan secara sederhana:

```text
Hitung
   ↓
Rencanakan
   ↓
Atur
   ↓
Pantau
```

### FAQ

Contoh:

- Apakah gratis?
- Apakah harus membuat akun?
- Apakah data aman?
- Apa yang bisa dilakukan di Wedding Planner?
- Apakah hasil estimator bisa diubah?

### Final CTA

> **Hitung Budget Nikah Gratis**

---

# 7. Tahap 3 — Budget Estimator

Budget Estimator adalah halaman terpisah dari Landing Page.

URL yang disarankan:

```text
/budget-estimator
```

Halaman ini fokus hanya pada satu tujuan:

> **Memberikan estimasi awal kebutuhan budget pernikahan.**

Tidak meminta login.

Tidak meminta nama pasangan.

Tidak meminta tanggal pernikahan.

---

# 8. Input Budget Estimator

Estimator menggunakan input sesederhana mungkin.

## 8.1 Konsep Pernikahan — Wajib

Pilihan:

- Akad saja
- Intimate Wedding
- Akad + Resepsi
- Resepsi besar
- Belum menentukan

## 8.2 Lokasi Pernikahan — Wajib

User memilih kota/kabupaten.

Contoh:

> Medan

Lokasi menjadi salah satu parameter estimasi.

## 8.3 Budget yang Tersedia — Wajib

User memasukkan budget yang sudah dimiliki/direncanakan.

Contoh:

> Rp30.000.000

Budget tersedia digunakan sebagai pembanding terhadap estimasi kebutuhan.

**Budget tersedia bukan berarti budget tersebut menjadi hasil estimasi.**

## 8.4 Jumlah Tamu — Opsional

Contoh:

> 300 orang

User dapat melewati input ini.

Jumlah tamu digunakan untuk meningkatkan akurasi estimasi, terutama komponen seperti catering dan kebutuhan yang berkaitan dengan jumlah tamu.

---

# 9. Data yang Tidak Diminta di Estimator

Jangan meminta:

- Nama CPP
- Nama CPW
- Tanggal pernikahan
- Email
- Password
- Login

Data tersebut baru diperlukan setelah user memilih menyimpan hasil dan membuat Wedding Plan.

---

# 10. Logika Bisnis Budget Estimator

Estimator harus membedakan:

### Estimasi kebutuhan

> Berapa kira-kira biaya yang diperlukan untuk konsep pernikahan tersebut?

### Budget tersedia

> Berapa uang yang sudah disiapkan user?

Contoh:

```text
Estimasi kebutuhan
Rp35–45 juta

Budget tersedia
Rp30 juta

Perkiraan gap
Rp5–15 juta
```

Jangan menggunakan budget user sebagai hasil estimasi.

---

# 11. Bentuk Hasil Estimator

Jika data masih terbatas, gunakan **range**.

Contoh:

> ## Estimasi Budget Pernikahanmu
>
> **Rp35–45 juta**

Jika jumlah tamu diberikan dan data lebih lengkap:

> **±Rp42 juta**

Jangan memberikan angka terlalu presisi jika input belum cukup.

Tidak disarankan:

> Rp41.275.000

Karena hasil tersebut memberikan kesan akurasi yang tidak dimiliki estimator.

---

# 12. Breakdown Estimasi

Contoh hasil:

| Kebutuhan | Estimasi |
|---|---:|
| Catering | Rp15 jt |
| Venue | Rp4 jt |
| Dekorasi | Rp3 jt |
| MUA & Busana | Rp4 jt |
| Dokumentasi | Rp3 jt |
| Undangan | Rp1 jt |
| Seserahan | Rp2 jt |
| Lain-lain | Rp3–13 jt |

Tambahkan:

> **Estimasi ini merupakan gambaran awal. Biaya sebenarnya dapat berbeda berdasarkan vendor, lokasi, jumlah tamu, konsep, dan kebutuhan pernikahan.**

---

# 13. Tahap 4 — User Melihat Hasil

Setelah estimator selesai, user mendapatkan hasil.

Pada tahap ini user **belum perlu login**.

Tujuan utama tahap ini adalah memberikan value dan membangun keinginan untuk menyimpan hasil.

Tampilkan:

- Estimasi total/range
- Breakdown
- Budget tersedia
- Perkiraan gap

---

# 14. CTA Setelah Hasil

CTA utama:

> **💾 Simpan Hasil & Buat Wedding Plan**

CTA sekunder:

> **Hitung Ulang**

CTA simpan membawa user ke Login/Register.

Pesan:

> Simpan estimasi ini dan gunakan sebagai titik awal untuk membuat Wedding Plan.

---

# 15. Tahap 5 — Login / Register

Login/register baru muncul ketika user memilih:

> **Simpan Hasil & Buat Wedding Plan**

Alasan user melakukan login harus jelas:

> **Untuk menyimpan estimasi dan membuat Wedding Plan.**

## Aturan penting

Hasil estimator tidak boleh hilang.

Contoh:

```text
Estimator
    ↓
Estimasi Rp40 juta
    ↓
Klik Simpan
    ↓
Login/Register
    ↓
User berhasil login
    ↓
Estimasi Rp40 juta tetap tersedia
```

Data estimator kemudian dikaitkan dengan akun user.

---

# 16. Tahap 6 — Onboarding

Setelah login, user masuk ke onboarding.

Onboarding yang sudah tersedia tetap digunakan, tetapi urutannya disesuaikan agar lebih natural.

## Step 1 — Budget

Tampilkan budget dari hasil estimator.

Contoh:

> **Budget awal: Rp40.000.000**

Berikan opsi edit.

Pesan:

> Estimasi sebelumnya digunakan sebagai titik awal. Kamu bebas menyesuaikannya dengan budget yang sebenarnya ingin digunakan.

### Aturan

Budget hasil estimator:

- Menjadi default
- Tidak mengikat
- Bisa diubah user

Contoh:

```text
Estimasi: Rp40 juta

User mengubah menjadi:
Rp35 juta
```

Maka Rp35 juta menjadi budget yang digunakan untuk Wedding Plan.

---

# 17. Tahap 7 — Cara Menyusun Rencana

User memilih bagaimana Wedding Plan dibuat.

## Otomatis — Recommended

> Sistem membantu menyiapkan pembagian budget dan daftar persiapan awal berdasarkan rencana kamu.

## Manual

> Mulai dari data kosong dan atur semuanya sendiri.

---

# 18. Jika Memilih Otomatis

Sistem menggunakan budget dan informasi estimator sebagai dasar untuk membuat starting point.

Contoh pembagian:

```text
Catering          Rp15 jt
Venue              Rp4 jt
Dekorasi           Rp4 jt
MUA & Busana       Rp3 jt
Dokumentasi        Rp3 jt
Undangan           Rp1 jt
Seserahan          Rp2 jt
Lain-lain           Rp3 jt
```

Sistem juga dapat membuat daftar persiapan awal.

Contoh:

```text
☐ Tentukan venue
☐ Cari catering
☐ Tentukan MUA
☐ Cari fotografer
☐ Siapkan dokumen KUA
☐ Tentukan dekorasi
☐ Buat daftar tamu
☐ Siapkan seserahan
```

Hasil otomatis bukan data final.

User tetap dapat mengubah data tersebut setelah masuk ke Wedding Planner.

---

# 19. Jika Memilih Manual

Sistem tidak membuat pembagian budget atau checklist otomatis.

User masuk ke Wedding Planner dan mengatur semuanya sendiri menggunakan fitur yang sudah tersedia.

---

# 20. Tahap 8 — Data Pernikahan

Setelah user menentukan budget dan metode penyusunan rencana, baru minta data identitas pernikahan.

### Nama Calon Pengantin Pria

### Nama Calon Pengantin Wanita

### Tanggal Pernikahan

Data ini digunakan oleh Wedding Planner untuk:

- Identitas pasangan
- Countdown
- Informasi pernikahan
- Perencanaan waktu

Ini adalah titik yang tepat untuk meminta data karena user sudah berkomitmen membuat Wedding Plan.

---

# 21. Tahap 9 — Masuk ke Existing Wedding Planner

Setelah onboarding selesai:

```text
Wedding Plan berhasil dibuat
        ↓
Dashboard
```

Semua fitur Wedding Planner yang sudah ada tetap digunakan.

Tidak ada kebutuhan untuk membangun ulang dashboard sebagai bagian dari MVP funnel ini.

Fitur existing:

- Dashboard
- Daftar Persiapan
- Anggaran Pernikahan
- Seserahan
- Dokumen KUA
- Daftar Undangan
- Profil Akun
- Informasi Pernikahan
- Keamanan Akun

---

# 22. Hubungan Estimator dengan Anggaran Pernikahan

Budget Estimator dan fitur Anggaran Pernikahan **bukan fitur yang sama**.

## Budget Estimator

Fungsi:

> Memberikan perkiraan awal sebelum user membuat Wedding Plan.

Output:

> Rp35–45 juta

## Anggaran Pernikahan

Fungsi:

> Mengelola budget sebenarnya setelah user masuk ke aplikasi.

User dapat:

- Mengubah rencana pengeluaran
- Mengisi estimasi
- Mengisi biaya aktual
- Menentukan sumber dana
- Menandai pembayaran
- Mengubah/menghapus data

Flow:

```text
Budget Estimator
       ↓
Estimasi Rp35–45 juta
       ↓
User memilih budget Rp40 juta
       ↓
Onboarding
       ↓
Budget awal Rp40 juta
       ↓
Anggaran Pernikahan
       ↓
User mengatur dan mengubah budget sebenarnya
```

---

# 23. Data Flow Utama

Data harus mengalir tanpa meminta user mengulang input.

```text
Estimator
│
├── Konsep
├── Lokasi
├── Budget tersedia
└── Jumlah tamu (opsional)
│
↓
Estimasi
│
├── Total/range
├── Breakdown
└── Gap
│
↓
Login/Register
│
↓
Onboarding
│
├── Budget default dari estimator
│   └── editable
│
├── Otomatis / Manual
│
├── Nama CPP
├── Nama CPW
└── Tanggal pernikahan
│
↓
Existing Wedding Planner
```

---

# 24. CTA Sosial Media

CTA harus menyesuaikan konteks konten.

## Konten Budget

> **💰 Penasaran berapa budget yang kamu butuhkan untuk menikah? Hitung gratis di link bio.**

Link:

```text
/budget-estimator
```

## Konten Pengeluaran

> **📊 Mau tahu estimasi total biaya pernikahanmu? Coba Budget Estimator gratis di link bio.**

## Konten Persiapan

> **📋 Takut ada persiapan yang terlewat? Buat Wedding Plan gratis di link bio.**

## Konten Produk

> **💍 Atur persiapan pernikahanmu dalam satu tempat. Coba Ruang Pasangan gratis.**

CTA harus memberikan alasan yang jelas untuk diklik.

---

# 25. Tracking Funnel

Tracking tidak perlu rumit pada tahap awal.

Minimal catat event:

```text
landing_view
estimator_start
estimator_complete
estimator_result_view
save_estimate_click
login_started
register_completed
onboarding_started
onboarding_completed
wedding_plan_created
```

Tujuan tracking:

Mengetahui titik mana yang menyebabkan user berhenti.

Contoh:

```text
10.000 Landing Views
        ↓
4.000 Estimator Started
        ↓
3.000 Estimator Completed
        ↓
800 Save Estimate
        ↓
500 Register
        ↓
400 Onboarding Completed
        ↓
350 Wedding Plan Created
```

Dari data tersebut dapat diketahui apakah masalah ada di:

- Landing Page
- Estimator
- CTA hasil
- Login/Register
- Onboarding

---

# 26. MVP Scope

MVP funnel **tidak berarti membangun Wedding Planner dari nol**.

Wedding Planner sudah tersedia.

Fokus pembangunan baru:

## A. Landing Page

- [ ] Hero
- [ ] Problem
- [ ] Value proposition
- [ ] Feature overview
- [ ] Cara kerja
- [ ] FAQ
- [ ] CTA menuju Budget Estimator
- [ ] Final CTA

## B. Budget Estimator

- [ ] Konsep
- [ ] Lokasi
- [ ] Budget tersedia
- [ ] Jumlah tamu opsional
- [ ] Perhitungan estimasi
- [ ] Breakdown
- [ ] Range / estimasi
- [ ] Gap terhadap budget
- [ ] Disclaimer
- [ ] Hitung ulang

## C. Save Estimate

- [ ] CTA simpan hasil
- [ ] Login/Register
- [ ] Mempertahankan hasil estimator setelah login
- [ ] Menghubungkan hasil estimator dengan user

## D. Onboarding Integration

- [ ] Mengambil budget hasil estimator
- [ ] Budget dapat diedit
- [ ] Otomatis / Manual
- [ ] Generate starting budget jika otomatis
- [ ] Generate starting checklist jika otomatis
- [ ] Nama CPP
- [ ] Nama CPW
- [ ] Tanggal pernikahan

## E. Existing Wedding Planner

Gunakan aplikasi yang sudah tersedia.

**Tidak perlu membangun ulang:**

- Dashboard
- Daftar Persiapan
- Anggaran
- Seserahan
- Dokumen KUA
- Daftar Undangan
- Profil
- Informasi Pernikahan
- Keamanan

---

# 27. Hal yang Tidak Masuk MVP

Jangan memperluas scope dengan:

- Marketplace vendor
- Wedding Invitation
- AI Wedding Planner
- Kolaborasi pasangan
- Auto-posting Instagram/TikTok
- Analytics kompleks
- Recommendation engine kompleks
- Vector database
- AI agent kompleks
- Sistem harga vendor real-time

Semua itu dapat menjadi pengembangan berikutnya setelah funnel dasar terbukti menghasilkan user.

---

# 28. Prinsip Produk

### 1. Jangan meminta data sebelum ada alasan

User tidak perlu memberikan identitas untuk mencoba estimator.

### 2. Berikan value sebelum meminta login

Estimator harus bisa digunakan tanpa akun.

### 3. Jangan kehilangan data

Hasil estimator harus tetap tersedia setelah login.

### 4. Jangan mengunci budget

Budget hasil estimator adalah default dan dapat diedit.

### 5. Jangan mengulang input

Data yang sudah dimasukkan di estimator harus diteruskan ke onboarding.

### 6. Jangan membuat estimator menjadi aplikasi terpisah

Estimator adalah pintu masuk menuju Wedding Planner.

### 7. Jangan menjual angka sebagai kepastian

Estimator memberikan perkiraan/range, bukan harga pasti.

### 8. Wedding Planner adalah produk inti

Estimator hanya menjadi lead magnet dan entry point.

---

# 29. Proses Bisnis Lengkap

```text
                    TRAFFIC
                Instagram / TikTok
                       │
                       ↓
                LANDING PAGE
                       │
                       │
              "Hitung Budget Gratis"
                       │
                       ↓
              BUDGET ESTIMATOR
                       │
          ┌────────────┼────────────┐
          │            │            │
       Konsep       Lokasi       Budget
                                    │
                           Tamu (opsional)
          │            │            │
          └────────────┼────────────┘
                       ↓
                 HASIL ESTIMASI
                       │
             ┌─────────┴─────────┐
             │                   │
          Hitung Lagi       Simpan Hasil
                                 │
                                 ↓
                         LOGIN / REGISTER
                                 │
                                 ↓
                             ONBOARDING
                                 │
                    ┌────────────┼────────────┐
                    │            │            │
                 Budget      Otomatis/Manual  │
                    │                         │
                    └────────────┬────────────┘
                                 ↓
                         Nama CPP / CPW
                                 │
                         Tanggal Pernikahan
                                 │
                                 ↓
                    EXISTING WEDDING PLANNER
                                 │
                    ┌────────────┼────────────┐
                    ↓            ↓            ↓
                 Budget       Persiapan    Undangan
                    │            │            │
                    └────────────┼────────────┘
                                 ↓
                         USER ACTIVE
```

---

# 30. Tujuan Akhir Funnel

Tujuan funnel bukan sekadar mendapatkan orang yang mencoba kalkulator.

Tujuan akhirnya adalah:

> **Membuat calon pengantin mendapatkan value gratis, tertarik menyimpan hasil, kemudian masuk dan menggunakan Wedding Planner Ruang Pasangan.**

Urutan nilai bisnis:

```text
Social Media
     ↓
Attention
     ↓
Budget Estimator
     ↓
Value
     ↓
Account
     ↓
Wedding Plan
     ↓
Active User
     ↓
Future Monetization
```

Monetisasi lanjutan dapat dikembangkan setelah funnel dasar berjalan, misalnya:

- Premium Wedding Planner
- Wedding Invitation
- Vendor/partner
- Produk digital lainnya

Namun monetisasi tersebut **bukan bagian dari MVP funnel ini**.
