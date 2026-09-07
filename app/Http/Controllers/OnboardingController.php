<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Models\WeddingBudget;
use App\Models\WeddingChecklist;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class OnboardingController extends Controller
{
    public function index()
    {
        // If user already has a wedding setup, redirect to dashboard
        if (Wedding::where('user_id', auth()->id())->exists()) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Onboarding/Index');
    }

    public function store(Request $request)
    {
        // Prevent duplicate setups
        if (Wedding::where('user_id', auth()->id())->exists()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'setup_type' => 'required|in:manual,auto',
            'budget' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create the Wedding base record
            $wedding = Wedding::create([
                'user_id' => auth()->id(),
                'nama_cpw' => 'Calon Pengantin Wanita',
                'nama_cpp' => 'Calon Pengantin Pria',
                'tanggal_nikah' => now()->addYear()->toDateString(),
            ]);

            // 2. Generate Automatic Data if setup_type == 'auto'
            if ($validated['setup_type'] === 'auto' && !empty($validated['budget'])) {
                $totalBudget = $validated['budget'];
                $this->generateAutoBudget($wedding->id, $totalBudget);
                $this->generateAutoChecklist($wedding->id);
            }

            DB::commit();
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyiapkan data.']);
        }
    }

    private function generateAutoBudget($weddingId, $totalBudget)
    {
        // Standard Indonesian wedding budget percentages
        $categories = [
            ['no' => 1, 'kategori' => 'Katering', 'item' => 'Catering Buffet & Food Stall', 'percent' => 0.40],
            ['no' => 2, 'kategori' => 'Venue', 'item' => 'Sewa Gedung / Venue', 'percent' => 0.15],
            ['no' => 3, 'kategori' => 'Dekorasi', 'item' => 'Dekorasi Pelaminan & Ruangan', 'percent' => 0.15],
            ['no' => 4, 'kategori' => 'MUA & Busana', 'item' => 'Rias & Baju Pengantin', 'percent' => 0.10],
            ['no' => 5, 'kategori' => 'Dokumentasi', 'item' => 'Foto & Video Hari H', 'percent' => 0.08],
            ['no' => 6, 'kategori' => 'Undangan & Souvenir', 'item' => 'Cetak Undangan & Souvenir', 'percent' => 0.05],
            ['no' => 7, 'kategori' => 'Lain-lain', 'item' => 'Biaya Tak Terduga', 'percent' => 0.07],
        ];

        foreach ($categories as $cat) {
            WeddingBudget::create([
                'wedding_id' => $weddingId,
                'no' => $cat['no'],
                'kategori' => $cat['kategori'],
                'item' => $cat['item'],
                'estimasi_budget' => round($totalBudget * $cat['percent']),
                'status' => 'belum',
                'sumber_dana' => 'cpp',
            ]);
        }
    }

    private function generateAutoChecklist($weddingId)
    {
        // A minimal set of crucial checklists
        $checklists = [
            ['no' => 1, 'bulan_range' => 'H-12 s/d 11 Bulan', 'persiapan' => 'Tentukan tanggal & tempat', 'detail' => 'Koordinasikan dengan keluarga', 'status' => false],
            ['no' => 2, 'bulan_range' => 'H-12 s/d 11 Bulan', 'persiapan' => 'Survey dan booking venue', 'detail' => 'Minimal 3 venue untuk perbandingan', 'status' => false],
            ['no' => 3, 'bulan_range' => 'H-10 s/d 9 Bulan', 'persiapan' => 'Pilih & booking katering', 'detail' => 'Lakukan test food', 'status' => false],
            ['no' => 4, 'bulan_range' => 'H-10 s/d 9 Bulan', 'persiapan' => 'Booking MUA & Fotografer', 'detail' => 'Pastikan jadwal mereka kosong di hari H', 'status' => false],
            ['no' => 5, 'bulan_range' => 'H-8 s/d 7 Bulan', 'persiapan' => 'Beli cicin kawin', 'detail' => 'Sesuaikan dengan ukuran jari terbaru', 'status' => false],
            ['no' => 6, 'bulan_range' => 'H-6 s/d 5 Bulan', 'persiapan' => 'Urus Dokumen KUA', 'detail' => 'Siapkan N1, N2, N4, KTP, KK, foto', 'status' => false],
            ['no' => 7, 'bulan_range' => 'H-4 s/d 3 Bulan', 'persiapan' => 'Pesan undangan & suvenir', 'detail' => 'Pastikan desain & proofing tulisan benar', 'status' => false],
            ['no' => 8, 'bulan_range' => 'H-2 Bulan', 'persiapan' => 'Mulai sebar undangan', 'detail' => 'Utamakan undangan fisik untuk keluarga jauh', 'status' => false],
            ['no' => 9, 'bulan_range' => 'H-1 Minggu', 'persiapan' => 'Fitting akhir baju pengantin', 'detail' => 'Pastikan ukuran pas tidak kelonggaran/kesempitan', 'status' => false],
        ];

        foreach ($checklists as $cl) {
            WeddingChecklist::create([
                'wedding_id' => $weddingId,
                'no' => $cl['no'],
                'bulan_range' => $cl['bulan_range'],
                'persiapan' => $cl['persiapan'],
                'detail' => $cl['detail'],
                'status' => $cl['status'],
            ]);
        }
    }
}

