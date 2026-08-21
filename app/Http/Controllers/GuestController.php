<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Models\WeddingGuest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GuestController extends Controller
{
    private function getWedding(): Wedding
    {
        return Wedding::where('user_id', auth()->id())->firstOrFail();
    }

    public function index()
    {
        $wedding = $this->getWedding();
        $guests  = $wedding->guests()->orderBy('no')->orderBy('id')->get();

        $totalTamu = $guests->count();
        $tamuCpw   = $guests->where('pihak', 'cpw')->count();
        $tamuCpp   = $guests->where('pihak', 'cpp')->count();
        $tamuUmum  = $guests->where('pihak', 'umum')->count();
        $hadir     = $guests->where('status', 'hadir')->count();

        return Inertia::render('Guest/Index', [
            'guests'    => $guests->values(),
            'totalTamu' => $totalTamu,
            'tamuCpw'   => $tamuCpw,
            'tamuCpp'   => $tamuCpp,
            'tamuUmum'  => $tamuUmum,
            'hadir'     => $hadir,
            'namaCpw'   => $wedding->nama_cpw,
            'namaCpp'   => $wedding->nama_cpp,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_tamu' => 'required|string|max:255',
            'pihak'     => 'required|in:cpw,cpp,umum',
            'status'    => 'required|in:belum_dikirim,sudah_dikirim,hadir,tidak_hadir',
            'catatan'   => 'nullable|string|max:500',
        ]);

        $wedding = $this->getWedding();
        $lastNo  = $wedding->guests()->max('no') ?? 0;

        WeddingGuest::create(array_merge($data, [
            'wedding_id' => $wedding->id,
            'no'         => $lastNo + 1,
        ]));

        return redirect()->route('tamu.index');
    }

    public function update(Request $request, WeddingGuest $tamu)
    {
        $wedding = $this->getWedding();
        abort_unless($tamu->wedding_id === $wedding->id, 404);

        $data = $request->validate([
            'nama_tamu' => 'required|string|max:255',
            'pihak'     => 'required|in:cpw,cpp,umum',
            'status'    => 'required|in:belum_dikirim,sudah_dikirim,hadir,tidak_hadir',
            'catatan'   => 'nullable|string|max:500',
        ]);

        $tamu->update($data);
        return redirect()->route('tamu.index');
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:wedding_guests,id',
        ]);

        $wedding = $this->getWedding();

        foreach ($data['ids'] as $index => $id) {
            WeddingGuest::where('wedding_id', $wedding->id)
                ->where('id', $id)
                ->update(['no' => $index + 1]);
        }

        return redirect()->route('tamu.index');
    }

    public function destroy(WeddingGuest $tamu)
    {
        $wedding = $this->getWedding();
        abort_unless($tamu->wedding_id === $wedding->id, 404);

        $tamu->delete();
        return redirect()->route('tamu.index');
    }
}
