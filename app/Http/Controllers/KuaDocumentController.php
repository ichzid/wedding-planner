<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Models\KuaDocument;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KuaDocumentController extends Controller
{
    private function getWedding(): Wedding
    {
        return Wedding::firstOrFail();
    }

    public function index()
    {
        $wedding   = $this->getWedding();
        $documents = $wedding->kuaDocuments()->get();

        $totalBiaya = $documents->sum('biaya');
        $totalDok   = $documents->count();
        $doneCpw    = $documents->where('cpw_status', true)->count();
        $doneCpp    = $documents->where('cpp_status', true)->count();
        $allDone    = $documents->every(fn($d) => $d->cpw_status && $d->cpp_status);

        return Inertia::render('Kua/Index', [
            'documents'  => $documents->values(),
            'totalBiaya' => $totalBiaya,
            'totalDok'   => $totalDok,
            'doneCpw'    => $doneCpw,
            'doneCpp'    => $doneCpp,
            'allDone'    => $allDone,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'biaya'        => 'nullable|integer|min:0',
            'catatan'      => 'nullable|string',
        ]);

        $wedding = $this->getWedding();
        $lastNo  = $wedding->kuaDocuments()->max('no') ?? 0;
        $data['biaya'] = $data['biaya'] ?? 0;

        KuaDocument::create(array_merge($data, [
            'wedding_id' => $wedding->id,
            'no'         => $lastNo + 1,
            'cpw_status' => false,
            'cpp_status' => false,
        ]));

        return redirect()->route('dokumen-kua.index');
    }

    public function update(Request $request, KuaDocument $dokumen_kua)
    {
        $data = $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'biaya'        => 'nullable|integer|min:0',
            'catatan'      => 'nullable|string',
        ]);

        $data['biaya'] = $data['biaya'] ?? 0;
        $dokumen_kua->update($data);
        return redirect()->route('dokumen-kua.index');
    }

    public function destroy(KuaDocument $dokumen_kua)
    {
        $dokumen_kua->delete();
        return redirect()->route('dokumen-kua.index');
    }

    public function toggleCpw(KuaDocument $kuaDocument)
    {
        $kuaDocument->update(['cpw_status' => !$kuaDocument->cpw_status]);
        return redirect()->route('dokumen-kua.index');
    }

    public function toggleCpp(KuaDocument $kuaDocument)
    {
        $kuaDocument->update(['cpp_status' => !$kuaDocument->cpp_status]);
        return redirect()->route('dokumen-kua.index');
    }
}
