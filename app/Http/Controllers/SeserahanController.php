<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Models\SeserahanItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeserahanController extends Controller
{
    private function getWedding(): Wedding
    {
        return Wedding::firstOrFail();
    }

    public function index(Request $request)
    {
        $wedding = $this->getWedding();
        $items   = $wedding->seserahanList()->orderBy('no')->orderBy('id')->get();

        $totalItem  = $items->count();
        $totalHarga = $items->sum(fn($i) => $i->qty * $i->harga);
        $sudahBeli  = $items->where('status', 'sudah_dibeli')->count();
        $belumBeli  = $totalItem - $sudahBeli;

        return Inertia::render('Seserahan/Index', [
            'items'           => $items->values(),
            'kategoriOptions' => SeserahanItem::kategoriList(),
            'totalItem'       => $totalItem,
            'totalHarga'      => $totalHarga,
            'sudahBeli'       => $sudahBeli,
            'belumBeli'       => $belumBeli,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori'  => 'required|string',
            'nama_item' => 'required|string|max:255',
            'untuk'     => 'required|in:cpp,cpw,kedua',
            'qty'       => 'required|integer|min:1',
            'satuan'    => 'nullable|string|max:50',
            'harga'     => 'required|integer|min:0',
            'status'    => 'required|in:belum,sudah_dibeli',
        ]);

        $wedding = $this->getWedding();
        $lastNo  = $wedding->seserahanList()->max('no') ?? 0;

        SeserahanItem::create(array_merge($data, [
            'wedding_id' => $wedding->id,
            'no'         => $lastNo + 1,
        ]));

        return redirect()->route('seserahan.index');
    }

    public function update(Request $request, SeserahanItem $seserahan)
    {
        $data = $request->validate([
            'kategori'  => 'required|string',
            'nama_item' => 'required|string|max:255',
            'untuk'     => 'required|in:cpp,cpw,kedua',
            'qty'       => 'required|integer|min:1',
            'satuan'    => 'nullable|string|max:50',
            'harga'     => 'required|integer|min:0',
            'status'    => 'required|in:belum,sudah_dibeli',
        ]);

        $seserahan->update($data);
        return redirect()->route('seserahan.index');
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:seserahan_list,id',
        ]);

        $wedding = $this->getWedding();

        foreach ($data['ids'] as $index => $id) {
            SeserahanItem::where('wedding_id', $wedding->id)
                ->where('id', $id)
                ->update(['no' => $index + 1]);
        }

        return redirect()->route('seserahan.index');
    }

    public function destroy(SeserahanItem $seserahan)
    {
        $seserahan->delete();
        return redirect()->route('seserahan.index');
    }

    public function toggle(SeserahanItem $seserahan)
    {
        $newStatus = $seserahan->status === 'sudah_dibeli' ? 'belum' : 'sudah_dibeli';
        $seserahan->update(['status' => $newStatus]);
        return redirect()->route('seserahan.index');
    }
}
