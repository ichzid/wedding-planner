<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Models\WeddingChecklist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChecklistController extends Controller
{
    private function getWedding(): Wedding
    {
        return Wedding::where('user_id', auth()->id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $wedding    = $this->getWedding();
        $checklists = $wedding->checklists()->orderBy('no')->orderBy('id')->get()->groupBy('bulan_range');
        $bulanOptions = WeddingChecklist::bulanRangeList();

        return Inertia::render('Checklist/Index', [
            'checklists'   => $checklists,
            'bulanOptions' => $bulanOptions,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bulan_range' => 'required|string',
            'persiapan'   => 'required|string|max:255',
            'detail'      => 'nullable|string',
        ]);

        $wedding = $this->getWedding();
        $lastNo  = $wedding->checklists()->max('no') ?? 0;

        WeddingChecklist::create(array_merge($data, [
            'wedding_id' => $wedding->id,
            'no'         => $lastNo + 1,
            'status'     => false,
        ]));

        return redirect()->route('checklist.index');
    }

    public function update(Request $request, WeddingChecklist $checklist)
    {
        $data = $request->validate([
            'bulan_range' => 'required|string',
            'persiapan'   => 'required|string|max:255',
            'detail'      => 'nullable|string',
        ]);

        $checklist->update($data);
        return redirect()->route('checklist.index');
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:wedding_checklist,id',
            'items.*.bulan_range' => 'required|string',
        ]);

        $wedding = $this->getWedding();

        foreach ($data['items'] as $index => $item) {
            WeddingChecklist::where('wedding_id', $wedding->id)
                ->where('id', $item['id'])
                ->update([
                    'bulan_range' => $item['bulan_range'],
                    'no' => $index + 1,
                ]);
        }

        return redirect()->route('checklist.index');
    }

    public function destroy(WeddingChecklist $checklist)
    {
        $checklist->delete();
        return redirect()->route('checklist.index');
    }

    public function toggle(WeddingChecklist $checklist)
    {
        $checklist->update(['status' => !$checklist->status]);
        return redirect()->route('checklist.index');
    }
}
