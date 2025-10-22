<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\jenisperangkat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class JenisperangkatController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
           return redirect('/login');
        }
        
        $data = JenisPerangkat::all();
        return view('pages.jenisperangkat.index', compact('data'));
    }

    public function create()
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        return view('pages.jenisperangkat.create');
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        $validated = $request->validate([
            'jenisperangkat' => 'required|string|max:255',
            'keterangan'     => 'nullable|string',
        ]);

        JenisPerangkat::create($validated);

        return redirect()->route('jenisperangkat.index')
                         ->with('success', 'Data berhasil ditambahkan');
    }

    public function show($id)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        $jenis = JenisPerangkat::findOrFail($id);
        return view('pages.jenisperangkat.show', compact('jenis'));
    }

    public function edit($id)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        $jenis = JenisPerangkat::findOrFail($id);
        return view('pages.jenisperangkat.edit', compact('jenis'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        $validated = $request->validate([
            'jenisperangkat' => 'required|string|max:255',
            'keterangan'     => 'nullable|string',
        ]);

        $jenis = JenisPerangkat::findOrFail($id);
        $jenis->update($validated);

        return redirect()->route('jenisperangkat.index')
                         ->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }
        $jenis = JenisPerangkat::findOrFail($id);
        $jenis->delete();

        return redirect()->route('jenisperangkat.index')
                         ->with('success', 'Data berhasil dihapus');
    }
}


