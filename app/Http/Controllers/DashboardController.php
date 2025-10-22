<?php

namespace App\Http\Controllers;

use App\Models\perangkat;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function showdashboard()
    {
        if (!Auth::check()) {
           return redirect('/login');
        }
        $data = perangkat::all();
        $aktif = $data->where('status', '1')->count();
        $nonaktif = $data->where('status', '0')->count();
        $total = $data->count();

        return view('pages.dashboard', compact('data','aktif','nonaktif','total'));
    }

    public function reload()
    {
        $aktif = Perangkat::where('status', '1')->count();
        $nonaktif = Perangkat::where('status', '0')->count();
        $total = Perangkat::count();

        $data = Perangkat::select('id','hostname','latitude','longitude','status')->get();

        return response()->json([
            'aktif' => $aktif,
            'nonaktif' => $nonaktif,
            'total' => $total,
            'data' => $data
        ]);
    }

    public function generalDashboard(){
        
        $data = perangkat::all();

        $aktif = $data->where('status', '1')->count();
        $nonaktif = $data->where('status', '0')->count();
        $total = $data->count();

        return view('pages.generaldashboard', compact('data','aktif','nonaktif','total'));
    }
}
