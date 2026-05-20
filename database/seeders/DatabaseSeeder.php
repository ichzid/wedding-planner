<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wedding;
use App\Models\WeddingBudget;
use App\Models\SeserahanItem;
use App\Models\WeddingChecklist;
use App\Models\KuaDocument;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $wedding = Wedding::create([
            'user_id'        => 1,
            'nama_cpw'       => 'Triana',
            'nama_cpp'       => 'Ihmal',
            'tanggal_nikah'  => '2027-06-25',
            'lokasi_akad'    => 'Coffeshop xxx',
            'lokasi_resepsi' => 'Coffeshop xxx',
        ]);

        // ── BUDGET ─────────────────────────────────────────────────────────
        $budgets = [
            [1, 'Venue', 'Sewa Gedung / Aula', 'Gedung Serbaguna', 15000000, 5000000, 0, 'dp_terbayar', 'Sudah termasuk charge catering & kebersihan'],
            [2, 'Venue', 'Dekorasi Pelaminan & Akad', 'Sanggar Nuansa', 15000000, 5000000, 0, 'dp_terbayar', 'Paket standar + bunga hidup'],
            [3, 'Catering', 'Catering Prasmanan 400 Pax', 'Berkah Katering', 28000000, 10000000, 0, 'dp_terbayar', 'Harga 70rb/pax, free 2 gubukan'],
            [4, 'Catering', 'Snack Box / Kue Basah', 'Toko Kue Mawar', 1500000, 0, 0, 'belum', 'Untuk panitia dan keluarga inti'],
            [5, 'Dokumentasi', 'Paket Foto & Video Liputan', 'Lensa Project', 8000000, 2000000, 0, 'dp_terbayar', '2 fotografer, 1 videografer, cetak album'],
            [6, 'Attire & Makeup', 'Paket Rias & Busana Pengantin', 'Putri MUA', 15000000, 5000000, 0, 'dp_terbayar', 'Akad + 2x ganti baju resepsi, rias ortu & besan'],
            [7, 'Attire & Makeup', 'Kain Seragam Keluarga & Bridesmaid', 'Toko Tekstil', 2500000, 2500000, 0, 'lunas', 'Beli kain kebaya dan batik'],
            [8, 'Entertainment', 'Electone, Singer & Sound System', 'Nada Musik', 3000000, 1000000, 0, 'dp_terbayar', 'Durasi 4 jam'],
            [9, 'Entertainment', 'MC Pernikahan', 'Budi MC', 1500000, 500000, 0, 'dp_terbayar', 'Untuk acara akad dan resepsi'],
            [10, 'Seserahan', 'Paket Kotak Seserahan', 'Toko Seserahan', 3000000, 3000000, 0, 'lunas', 'Isi seserahan wanita'],
            [11, 'Seserahan', 'Mahar Pernikahan', 'Galeri Mahar', 5000000, 5000000, 0, 'lunas', 'Uang tunai & logam mulia'],
            [12, 'Lainnya', 'Undangan Cetak & Digital', 'Print Shop', 1500000, 1500000, 0, 'lunas', 'Cetak 300 lembar + undangan website'],
            [13, 'Lainnya', 'Souvenir Pernikahan', 'Grosir Souvenir', 2500000, 1000000, 0, 'dp_terbayar', 'Pouch custom 400 pcs @ 6000'],
            [14, 'Lainnya', 'Biaya KUA & Penghulu', 'KUA Setempat', 1500000, 1500000, 0, 'lunas', 'Biaya pendaftaran dan transport penghulu'],
        ];

        foreach ($budgets as $b) {
            WeddingBudget::create([
                'wedding_id'      => $wedding->id,
                'no'              => $b[0],
                'kategori'        => $b[1],
                'item'            => $b[2],
                'vendor'          => $b[3],
                'estimasi_budget' => $b[4],
                'dp'              => $b[5],
                'pelunasan'       => $b[6],
                'status'          => $b[7],
                'catatan'         => $b[8],
            ]);
        }

        // ── SESERAHAN ──────────────────────────────────────────────────────
        $seserahan = [
            [1, 'Perhiasan', 'Cincin Nikah Emas', 'bride', 1, 'set', 4000000, 'belum'],
            [2, 'Ibadah', 'Seperangkat Alat Sholat', 'bride', 1, 'set', 1000000, 'belum'],
            [3, 'Pakaian', 'Kain Kebaya & Bawahan', 'bride', 1, 'set', 1500000, 'sudah_dibeli'],
            [4, 'Aksesoris', 'Tas Pesta Wanita', 'bride', 1, 'buah', 1000000, 'belum'],
            [5, 'Aksesoris', 'Sepatu Wanita', 'bride', 1, 'pasang', 700000, 'belum'],
            [6, 'Kosmetik', 'Skincare & Makeup', 'bride', 1, 'set', 1000000, 'belum'],
            [7, 'Bodycare', 'Perawatan Tubuh & Parfum', 'bride', 1, 'set', 500000, 'sudah_dibeli'],
            [8, 'Makanan', 'Kue Basah Hantaran', 'bride', 1, 'kotak', 150000, 'belum'],
            [9, 'Makanan', 'Parcel Buah Segar', 'bride', 1, 'kotak', 150000, 'belum'],
        ];

        foreach ($seserahan as $s) {
            SeserahanItem::create([
                'wedding_id' => $wedding->id,
                'no'         => $s[0],
                'kategori'   => $s[1],
                'nama_item'  => $s[2],
                'untuk'      => $s[3],
                'qty'        => $s[4],
                'satuan'     => $s[5],
                'harga'      => $s[6],
                'status'     => $s[7],
            ]);
        }

        // ── CHECKLIST ──────────────────────────────────────────────────────
        $checklists = [
            // H-12
            [1,'H-12 s/d 11 Bulan','Tentukan tanggal & tempat akad/resepsi','Koordinasikan dengan kedua keluarga',true],
            [2,'H-12 s/d 11 Bulan','Buat anggaran pernikahan','Estimasikan semua biaya yang diperlukan',true],
            [3,'H-12 s/d 11 Bulan','Tentukan tema & konsep pernikahan','Pilih warna, dekor, dan suasana yang diinginkan',true],
            [4,'H-12 s/d 11 Bulan','Survey dan booking venue','Minimal 3 venue untuk perbandingan',true],
            [5,'H-12 s/d 11 Bulan','Buat daftar tamu undangan','Perkiraan jumlah tamu dari kedua belah pihak',false],
            // H-10
            [6,'H-10 s/d 9 Bulan','Booking fotografer & videografer','Pastikan tanggal sudah tersedia',true],
            [7,'H-10 s/d 9 Bulan','Pilih dan booking katering','Lakukan food tasting sebelum konfirmasi',true],
            [8,'H-10 s/d 9 Bulan','Tentukan konsep dekorasi','Diskusikan detail dengan vendor dekor',false],
            [9,'H-10 s/d 9 Bulan','Mulai cari gaun & jas pengantin','Butik biasanya butuh 3-4 bulan untuk order',false],
            // H-8
            [10,'H-8 s/d 7 Bulan','Booking MUA (Make Up Artist)','Lakukan test make up sebelum booking',false],
            [11,'H-8 s/d 7 Bulan','Pesan undangan pernikahan','Desain dan cetak undangan fisik & digital',false],
            [12,'H-8 s/d 7 Bulan','Booking entertainment / band','Tentukan lagu-lagu yang ingin dimainkan',false],
            [13,'H-8 s/d 7 Bulan','Urus dokumen ke KUA','Siapkan semua persyaratan dokumen',false],
            // H-6
            [14,'H-6 s/d 5 Bulan','Lakukan fitting gaun pengantin','Pastikan ukuran sudah pas',false],
            [15,'H-6 s/d 5 Bulan','Siapkan hadiah seserahan','Mulai beli item-item seserahan',false],
            [16,'H-6 s/d 5 Bulan','Kirim undangan gelombang pertama','Untuk tamu VIP dan luar kota',false],
            [17,'H-6 s/d 5 Bulan','Booking mobil pengantin','Pastikan unit dan supir sudah terkonfirmasi',false],
            // H-4
            [18,'H-4 s/d 3 Bulan','Konfirmasi ulang semua vendor','Pastikan semua masih sesuai kesepakatan',false],
            [19,'H-4 s/d 3 Bulan','Kirim sisa undangan','Untuk tamu umum dan kerabat',false],
            [20,'H-4 s/d 3 Bulan','Beli souvenir pernikahan','Sesuaikan jumlah dengan tamu undangan',false],
            [21,'H-4 s/d 3 Bulan','Persiapkan rundown acara','Koordinasikan dengan MC dan vendor',false],
        ];

        foreach ($checklists as $c) {
            WeddingChecklist::create([
                'wedding_id' => $wedding->id,
                'no'         => $c[0],
                'bulan_range'=> $c[1],
                'persiapan'  => $c[2],
                'detail'     => $c[3],
                'status'     => $c[4],
            ]);
        }

        // ── KUA DOCUMENTS ──────────────────────────────────────────────────
        $kua = [
            [1, 'Surat Pengantar Nikah (N1)', false, false, 0, 'Didapat dari kelurahan atau desa setempat'],
            [2, 'Surat Persetujuan Mempelai (N4)', false, false, 0, 'Surat pernyataan setuju untuk menikah tanpa paksaan'],
            [3, 'Surat Izin Orang Tua (N5)', false, false, 0, 'Wajib jika calon pengantin berusia di bawah 21 tahun'],
            [4, 'Fotokopi KTP dan KK', false, false, 0, 'Masing-masing calon pengantin dan orang tua/wali'],
            [5, 'Fotokopi Akta Kelahiran', false, false, 0, 'Atau surat keterangan kelahiran dari kelurahan/desa'],
            [6, 'Pasfoto Latar Biru 2x3', false, false, 0, 'Masing-masing 4 lembar'],
            [7, 'Pasfoto Latar Biru 3x4', false, false, 0, 'Masing-masing 4 lembar'],
            [8, 'Surat Keterangan Imunisasi TT', false, false, 0, 'Dari Puskesmas (untuk calon pengantin wanita)'],
            [9, 'Surat Pernyataan Belum Pernah Menikah', false, false, 0, 'Ditandatangani di atas meterai Rp10.000'],
            [10, 'Akta Cerai Asli', false, false, 0, 'Kondisional: Jika status duda/janda cerai hidup'],
            [11, 'Surat Keterangan Kematian (N6)', false, false, 0, 'Kondisional: Jika status duda/janda cerai mati'],
            [12, 'Surat Rekomendasi Nikah', false, false, 0, 'Kondisional: Jika menikah di luar kecamatan asal'],
            [13, 'Surat Izin/Dispensasi Atasan', false, false, 0, 'Kondisional: Jika calon anggota TNI/Polri'],
            [14, 'Dispensasi Pengadilan Agama', false, false, 0, 'Kondisional: Jika berusia di bawah 19 tahun'],
            [15, 'Surat Keterangan Wali', false, false, 0, 'Kondisional: Jika wali nikah bukan ayah kandung'],
        ];

        foreach ($kua as $k) {
            KuaDocument::create([
                'wedding_id'  => $wedding->id,
                'no'          => $k[0],
                'nama_dokumen'=> $k[1],
                'cpw_status'  => $k[2],
                'cpp_status'  => $k[3],
                'biaya'       => $k[4],
                'catatan'     => $k[5],
            ]);
        }
    }
}
