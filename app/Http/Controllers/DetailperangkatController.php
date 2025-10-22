<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\detailperangkat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DetailperangkatController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }
        $validated = $request->validate([
            'namaperangkat' => 'required|string|max:255',
            'ip_address' => 'nullable|string|max:50',
            'mac_address' => 'nullable|string|max:50',
            'jenisperangkat_id' => 'required|exists:jenisperangkats,id',
            'perangkat_id' => 'required|exists:perangkats,id',
        ]);

        detailperangkat::create($validated);

        return redirect('/perangkat/'. $validated['perangkat_id'])->with('success', 'Perangkat berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        $validated = $request->validate([
            'namaperangkat' => 'required|string|max:255',
            'ip_address' => 'nullable|string|max:50',
            'mac_address' => 'nullable|string|max:50',
            'jenisperangkat_id' => 'required|exists:jenisperangkats,id',
        ]);

        $perangkat = detailperangkat::findOrFail($id);
        $perangkat->update($validated);

        return redirect('/perangkat/'. $perangkat->perangkat_id)->with('success', 'Perangkat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }
        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }

        $detailperangkat = detailperangkat::findOrFail($id);
        $perangkat_id = $detailperangkat->perangkat_id;
        $detailperangkat->delete();

        return redirect('/perangkat/'. $perangkat_id)->with('success', 'Perangkat berhasil dihapus.');
    }

    public function showByPerangkat($perangkat_id)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        $detail = DetailPerangkat::where('perangkat_id', $perangkat_id)->get();

        return response()->json($detail);
    }


   
}
