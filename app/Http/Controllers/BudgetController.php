<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Models\WeddingBudget;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BudgetController extends Controller
{
    private function getWedding(): Wedding
    {
        return Wedding::firstOrFail();
    }

    public function index(Request $request)
    {
        $wedding = $this->getWedding();
        $budgets = $wedding->budgets()->get();

        $totalEstimasi  = $budgets->sum('estimasi_budget');
        $totalDp        = $budgets->sum('dp');
        $totalPelunasan = $budgets->sum('pelunasan');
        $totalAktual    = $totalDp + $totalPelunasan;
        $totalSisa      = $totalEstimasi - $totalAktual;

        return Inertia::render('Budget/Index', [
            'budgets'         => $budgets->values(),
            'kategoriOptions' => WeddingBudget::kategoriList(),
            'statusOptions'   => WeddingBudget::statusList(),
            'totalEstimasi'   => $totalEstimasi,
            'totalDp'         => $totalDp,
            'totalPelunasan'  => $totalPelunasan,
            'totalAktual'     => $totalAktual,
            'totalSisa'       => $totalSisa,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori'        => 'required|string',
            'item'            => 'required|string|max:255',
            'vendor'          => 'nullable|string|max:255',
            'estimasi_budget' => 'required|integer|min:0',
            'dp'              => 'nullable|integer|min:0',
            'pelunasan'       => 'nullable|integer|min:0',
            'status'          => 'required|in:belum,dp_terbayar,lunas',
            'catatan'         => 'nullable|string',
        ]);

        $wedding = $this->getWedding();
        $lastNo  = $wedding->budgets()->max('no') ?? 0;
        $data['dp']        = $data['dp'] ?? 0;
        $data['pelunasan'] = $data['pelunasan'] ?? 0;

        WeddingBudget::create(array_merge($data, [
            'wedding_id' => $wedding->id,
            'no'         => $lastNo + 1,
        ]));

        return redirect()->route('budget.index');
    }

    public function update(Request $request, WeddingBudget $budget)
    {
        $data = $request->validate([
            'kategori'        => 'required|string',
            'item'            => 'required|string|max:255',
            'vendor'          => 'nullable|string|max:255',
            'estimasi_budget' => 'required|integer|min:0',
            'dp'              => 'nullable|integer|min:0',
            'pelunasan'       => 'nullable|integer|min:0',
            'status'          => 'required|in:belum,dp_terbayar,lunas',
            'catatan'         => 'nullable|string',
        ]);

        $data['dp']        = $data['dp'] ?? 0;
        $data['pelunasan'] = $data['pelunasan'] ?? 0;

        $budget->update($data);
        return redirect()->route('budget.index');
    }

    public function destroy(WeddingBudget $budget)
    {
        $budget->delete();
        return redirect()->route('budget.index');
    }
}
