<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\laporanperangkat;
use App\Models\pengaturansistem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class LaporanController extends Controller
{
    public function gantiemail(request $request)
    {

        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }

        $data = pengaturansistem::where('namapengaturan','emailpenerima');
        $data->update($request->email);

        return back()->with('success','Email berhasil diganti');
    }

    // Fungsi User Non-Login Mengirimkan Laporan Jaringan
    public function sendReport(Request $request)
    {
        $validatedData = $request->validate([
            'pengirim' => 'required|max:100',
            'laporan' => 'required|string',
            'opd' => 'required|string'
        ]);

        $emailpenerima = pengaturansistem::where('namapengaturan', 'emailpenerima')->value('value');  
        $emailsistem   = pengaturansistem::where('namapengaturan', 'emailsistem')->value('value');
        $sandiaplikasiemail = pengaturansistem::where('namapengaturan', 'sandiaplikasiemail')->value('value');

        Config::set('mail.mailers.smtp.username', $emailsistem);
        Config::set('mail.mailers.smtp.password', $sandiaplikasiemail);
        Config::set('mail.from.address', $emailsistem);
        Config::set('mail.from.name', 'Sistem Monitoring Internet OPD');

                try {
            Mail::raw($validatedData['laporan'], function ($message) use ($validatedData, $emailpenerima, $emailsistem) {
                $message->to($emailpenerima)
                        ->from($emailsistem, 'Sistem Monitoring Internet OPD')
                        ->subject('Laporan Jaringan dari ' . $validatedData['pengirim'] . ' , OPD ' . $validatedData['opd']);
            });

            laporanperangkat::create($validatedData);
            return redirect()->back()->with('success', 'Laporan berhasil dikirim!');
            
        } catch (\Throwable $e) {
            return redirect()->back()->with('failed', 'Laporan gagal dikirim! Silakan coba lagi.');
        }


        return redirect()->back()->with('failed','Laporan gagal dikirim, coba lagi.');
    }

    public function showReport()
    {
        if (!Auth::check()) {
           return redirect('/login');
        }
        
        $data = laporanperangkat::orderby('created_at','desc')->simplepaginate('20');

        return view('pages.perangkat.laporanperangkat', compact('data'));
    }

    public function clearlaporan()
    {
        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }
        
        laporanperangkat::truncate();
        return redirect('laporan')->with('success','Laporan internet berhasil dibersihkan');
    }
}
