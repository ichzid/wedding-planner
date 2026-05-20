<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $wedding = Wedding::with(['budgets','seserahanList','checklists','kuaDocuments','guests'])->firstOrFail();

        // Checklist stats
        $totalChecklist    = $wedding->checklists->count();
        $doneChecklist     = $wedding->checklists->where('status', true)->count();
        $progressChecklist = $totalChecklist > 0 ? round($doneChecklist / $totalChecklist * 100) : 0;
        $pendingChecklists = $wedding->checklists->where('status', false)->take(3)->values();

        // Budget stats
        $totalEstimasi  = $wedding->budgets->sum('estimasi_budget');
        $totalAktual    = $wedding->budgets->sum('dp') + $wedding->budgets->sum('pelunasan');
        $progressBudget = $totalEstimasi > 0 ? min(100, round($totalAktual / $totalEstimasi * 100)) : 0;

        // Seserahan stats
        $totalSeserahan = $wedding->seserahanList->count();
        $sudahBeli      = $wedding->seserahanList->where('status','sudah_dibeli')->count();
        $belumBeli      = $totalSeserahan - $sudahBeli;

        // KUA stats
        $totalKua = $wedding->kuaDocuments->count();
        $doneKua  = $wedding->kuaDocuments->filter(fn($k) => $k->cpw_status && $k->cpp_status)->count();
        $doneCpw  = $wedding->kuaDocuments->where('cpw_status', true)->count();
        $doneCpp  = $wedding->kuaDocuments->where('cpp_status', true)->count();

        // Budget by category
        $budgetByKategori = $wedding->budgets->groupBy('kategori')->map(function($items) {
            return [
                'estimasi' => $items->sum('estimasi_budget'),
                'aktual'   => $items->sum('dp') + $items->sum('pelunasan'),
                'count'    => $items->count(),
            ];
        });

        // Guest stats
        $totalTamu = $wedding->guests->count();
        $hadir     = $wedding->guests->where('status', 'hadir')->count();
        $tamuCpw   = $wedding->guests->where('pihak', 'cpw')->count();
        $tamuCpp   = $wedding->guests->where('pihak', 'cpp')->count();

        return Inertia::render('Dashboard', [
            'totalChecklist'    => $totalChecklist,
            'doneChecklist'     => $doneChecklist,
            'progressChecklist' => $progressChecklist,
            'pendingChecklists' => $pendingChecklists,
            'totalEstimasi'     => $totalEstimasi,
            'totalAktual'       => $totalAktual,
            'progressBudget'    => $progressBudget,
            'totalSeserahan'    => $totalSeserahan,
            'sudahBeli'         => $sudahBeli,
            'belumBeli'         => $belumBeli,
            'totalKua'          => $totalKua,
            'doneKua'           => $doneKua,
            'doneCpw'           => $doneCpw,
            'doneCpp'           => $doneCpp,
            'budgetByKategori'  => $budgetByKategori,
            'totalTamu'         => $totalTamu,
            'hadir'             => $hadir,
            'tamuCpw'           => $tamuCpw,
            'tamuCpp'           => $tamuCpp,
            'weddingDate'       => $wedding->tanggal_nikah?->toDateString(),
            'namaCpw'           => $wedding->nama_cpw,
            'namaCpp'           => $wedding->nama_cpp,
            'lokasiAkad'        => $wedding->lokasi_akad,
            'lokasiResepsi'     => $wedding->lokasi_resepsi,
        ]);
    }
}
