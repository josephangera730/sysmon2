<?php

namespace App\Http\Controllers;

use App\Models\perangkat;
use App\Models\log_status;
use Illuminate\Http\Request;
use App\Jobs\PingPerangkatJob;
use App\Models\jenisperangkat;
use App\Models\detailperangkat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PerangkatController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        $data = perangkat::all();
        return view('pages.perangkat.index', compact('data'));
    }

    public function show($id)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        $jenisperangkat = jenisperangkat::all();
        $perangkat = perangkat::findorfail($id);
        $data = detailperangkat::where('perangkat_id',$id)->get();

        return view('pages.perangkat.show', compact('data','perangkat','jenisperangkat'))->with('success','Data berhasil ditambahakan');
    }

    public function store(Request $request)
    {
        
        if (!Auth::check()) {
           return redirect('/login');
        }

        $validatedData = $request->validate([
            'hostname' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'mac_address' => 'nullable|string|max:50',
            'latitude' => 'required|string|max:100',
            'longitude' => 'required|string|max:100',
        ]);

        $newData =  Perangkat::create($validatedData);

        if(!$newData){
            return back()->with('failed','Maaf, data gagal ditambahkan');
        }

        return redirect('/perangkat/'.$newData->id);
    }

    public function perangkatTerkoneksi($id)
    {

        if (!Auth::check()) {
           return redirect('/login');
        }

        $jenisperangkat = jenisperangkat::all();
        $perangkat = perangkat::findorfail($id);
        $data = detailperangkat::where('perangkat_id',$id)->get();

        return view('pages.perangkat.show', compact('data','perangkat','jenisperangkat'))->with('success','Data berhasil ditambahakan');  
    }

    public function update(Request $request, Perangkat $perangkat)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        $validatedData = $request->validate([
            'hostname' => 'nullable|string|max:255',
            'ip_address' => 'required|ip',
            'mac_address' => 'nullable|string|max:50',
            'latitude' => 'nullable|string|max:100',
            'longitude' => 'nullable|string|max:100',
        ]);

        $newData = $perangkat->update($validatedData);

        if(!$newData){
            return back()->with('failed','Maaf, data gagal di ubah');
        }

        return redirect()->back()->with('success','Data berhasil di ubah');
    }

   
    public function destroy(Perangkat $perangkat)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }
        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }
        $data = $perangkat->delete();

        if(!$data){
            return back()->with('failed','Maaf, data gagal dihapus');
        }

        return redirect()->route('perangkat.index')->with('success','Data berhasil dihapus');
    }

    public function checkPerangkat()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
    
        $perangkat = Perangkat::all();

        return response()->json([
            'perangkat' => $perangkat->values()
        ], 200);
    }   

    public function logperangkat()
    {
        if (!Auth::check()) {
           return redirect('/login');
        }
        
        $data = log_status::orderby('created_at','desc')->simplepaginate('20');

        return view('pages.perangkat.logperangkat', compact('data'));
    }
    
    public function clearlog()
    {
        if(!Auth::check()) {
           return redirect('/login');
        }
        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }
        log_status::truncate();

        return redirect('logperangkat')->with('success','Log berhasil dibersihkan');
    }

    // public function import(Request $request)
    // {
       
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,xls'
    //     ]);

    //     try {
    //         Excel::import(new PerangkatImport, $request->file('file'));
    //         return redirect()->back()->with('success', 'File berhasil diimport!');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('failed', 'Gagal import file: ' . $e->getMessage());
    //     }
    // }

    public function pingPerangkat(){
         PingPerangkatJob::dispatch();
    }
}
