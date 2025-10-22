<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pengaturansistem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Artisan;

class PengaturansistemController extends Controller
{
    public function index()
    {
        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }
        
        $emailpenerima = pengaturansistem::where('namapengaturan','emailpenerima')->value('value');
        $emailsistem = pengaturansistem::where('namapengaturan','emailsistem')->value('value');
        $sandiaplikasiemail = pengaturansistem::where('namapengaturan','sandiaplikasiemail')->value('value');

        return view('pages.pengaturan.pengaturansistem', compact('emailpenerima','emailsistem','sandiaplikasiemail'));
        
    }

    public function update(Request $request)
    {
        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }

        $validated = $request->validate([
            'emailpenerima'      => 'required|email',
            'emailsistem'        => 'required|email',
            'sandiaplikasiemail' => 'required|string',
        ]);

        pengaturansistem::updateOrInsert(
            ['namapengaturan' => 'emailpenerima'],
            ['value' => $validated['emailpenerima']]
        );

        pengaturansistem::updateOrInsert(
            ['namapengaturan' => 'emailsistem'],
            ['value' => $validated['emailsistem']]
        );

        pengaturansistem::updateOrInsert(
            ['namapengaturan' => 'sandiaplikasiemail'],
            ['value' => $validated['sandiaplikasiemail']]
        );

        return redirect()->route('pengaturansistem.index')->with('success', 'Pengaturan berhasil diperbarui');
    }

    public function reset()
    {
        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }

        try {
            Artisan::call('migrate:fresh --seed');

            Auth::logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login')
                ->with('success', 'Sistem berhasil direset ulang. Silakan login kembali.');
        } catch (\Exception $e) {
            return redirect()->route('pengaturansistem.index')
                ->with('failed', 'Gagal mereset sistem: ' . $e->getMessage());
        }
    }
}
