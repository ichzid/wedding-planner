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
            'nama_cpw'       => 'Anggi',
            'nama_cpp'       => 'Pramono',
            'tanggal_nikah'  => '2027-03-11',
            'lokasi_akad'    => 'Rumah CPW',
            'lokasi_resepsi' => 'Hotel Arus',
        ]);

        // ── BUDGET ─────────────────────────────────────────────────────────
        $budgets = [
            [1,'Venue','Sewa Gedung Resepsi','Hotel Arus',35000000,10000000,0,'dp_terbayar',null],
            [2,'Venue','Sewa Tempat Akad','Rumah CPW',0,0,0,'belum',null],
            [3,'Venue','Dekorasi Akad & Resepsi','Dekor Indah',15000000,5000000,0,'dp_terbayar',null],
            [4,'Venue','Sewa Tenda & Kursi','CV Cahaya',8000000,0,0,'belum',null],
            [5,'Catering','Catering 500 pax','Katering Berkah',50000000,15000000,0,'dp_terbayar',null],
            [6,'Catering','Kue Pengantin','Toko Kue Manis',3500000,0,0,'belum',null],
            [7,'Dokumentasi','Fotografer','Studio Lens',8000000,3000000,0,'dp_terbayar',null],
            [8,'Dokumentasi','Videografer','Studio Lens',10000000,3000000,0,'dp_terbayar',null],
            [9,'Attire & Makeup','Gaun Pengantin CPW','Butik Anggun',12000000,5000000,0,'dp_terbayar',null],
            [10,'Attire & Makeup','Jas Pengantin CPP','Butik Ganteng',5000000,2000000,0,'dp_terbayar',null],
            [11,'Attire & Makeup','MUA + Rambut','Salon Cantik',6000000,2000000,0,'dp_terbayar',null],
            [12,'Entertainment','Band / Hiburan','Band Harmoni',5000000,2000000,0,'dp_terbayar',null],
            [13,'Entertainment','MC','Budi MC',2000000,0,0,'belum',null],
            [14,'Transport','Mobil Pengantin','Rental Mewah',3000000,1000000,0,'dp_terbayar',null],
            [15,'Transport','Bus Tamu Undangan','Bus Wisata',4000000,0,0,'belum',null],
            [16,'Seserahan','Perlengkapan Seserahan','Toko Seserahan',5000000,0,0,'belum',null],
            [17,'Lainnya','Undangan Digital & Cetak','Percetakan XYZ',2000000,0,0,'belum',null],
            [18,'Lainnya','Souvenir','Souvenir Cantik',6000000,0,0,'belum',null],
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
            [1,'Umum','Cincin Nikah','groom',1,'set',5000000,'belum'],
            [2,'Umum','Al-Quran','groom',1,'buah',350000,'belum'],
            [3,'Umum','Mukena','groom',1,'set',500000,'belum'],
            [4,'Umum','Sajadah','groom',1,'buah',150000,'belum'],
            [5,'Umum','Tasbih','groom',1,'buah',100000,'belum'],
            [6,'Umum','Tas','groom',1,'buah',1200000,'sudah_dibeli'],
            [7,'Umum','Sepatu','groom',1,'pasang',800000,'belum'],
            [8,'Umum','Dompet','groom',1,'buah',350000,'belum'],
            [9,'Umum','Jam Tangan','groom',1,'buah',2000000,'belum'],
            [10,'Umum','Pakaian Set','groom',3,'set',1500000,'belum'],
            [11,'Toileteries','Sabun Mandi','groom',3,'buah',50000,'belum'],
            [12,'Toileteries','Sampo','groom',2,'buah',80000,'belum'],
            [13,'Toileteries','Parfum','groom',1,'buah',500000,'sudah_dibeli'],
            [14,'Toileteries','Sikat & Pasta Gigi','groom',1,'set',75000,'belum'],
            [15,'Toileteries','Deodoran','groom',2,'buah',60000,'belum'],
            [16,'Skincare','Pelembab Wajah','bride',1,'buah',250000,'belum'],
            [17,'Skincare','Serum','bride',1,'buah',350000,'belum'],
            [18,'Skincare','Toner','bride',1,'buah',200000,'belum'],
            [19,'Skincare','Sunscreen','bride',1,'buah',150000,'belum'],
            [20,'Skincare','Body Lotion','bride',2,'buah',120000,'belum'],
            [21,'Makeup','Foundation','bride',1,'buah',350000,'belum'],
            [22,'Makeup','Bedak','bride',1,'buah',250000,'belum'],
            [23,'Makeup','Lipstik','bride',3,'buah',200000,'belum'],
            [24,'Makeup','Blush On','bride',1,'buah',180000,'belum'],
            [25,'Makeup','Maskara','bride',1,'buah',150000,'belum'],
            [26,'Makeup','Eyeliner','bride',2,'buah',120000,'belum'],
            [27,'Makeup','Eyeshadow','bride',1,'palet',300000,'belum'],
            [28,'Makeup','Brush Set','bride',1,'set',400000,'belum'],
            [29,'Lainnya','Bunga Mawar','groom',99,'tangkai',500000,'belum'],
            [30,'Lainnya','Coklat Premium','groom',2,'kotak',350000,'belum'],
            [31,'Lainnya','Permen & Snack','groom',1,'set',200000,'belum'],
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
            [1,'Fotocopi KTP',true,true,2000,null],
            [2,'Fotokopi Kartu Keluarga (KK)',true,false,2000,null],
            [3,'Fotokopi Akta Kelahiran',true,false,0,null],
            [4,'Fotokopi Ijazah',false,false,2000,null],
            [5,'Formulir Surat Pengantar Nikah dari Lurah (N1)',false,false,0,'Diurus di kantor kelurahan'],
            [6,'Formulir Permohonan Kehendak Nikah (N2)',false,false,0,null],
            [7,'Surat Persetujuan Mempelai (N4)',false,false,0,null],
            [8,'Surat Izin Orang Tua (N5)',false,false,0,'Jika usia < 21 tahun'],
            [9,'Fotokopi KTP Wali dan 2 Saksi',false,false,0,null],
            [10,'Fotokopi Kutipan Akta Nikah Orang Tua CPW',false,false,0,null],
            [11,'Imunisasi Tetanus Toxoid (TT) bagi CPW',false,false,120000,'Dilakukan di Puskesmas'],
            [12,'Surat Keterangan Belum Kawin dari Desa/Kelurahan',false,false,0,null],
            [13,'Pas Foto Background Biru 4x6 (1 lembar)',false,false,100000,null],
            [14,'Pas Foto Background Biru 3x4 (5 lembar)',false,false,0,null],
            [15,'Pas Foto Background Biru 2x3 (5 lembar)',false,false,0,null],
            [16,'Jenis dan Besaran Mas Kawin',false,false,0,'Tentukan mahar bersama'],
            [17,'Pendaftaran KUA',false,false,600000,'Biaya resmi pendaftaran'],
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
