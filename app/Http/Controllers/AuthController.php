<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Models\pengaturansistem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }


        return back()->with('error', 'Email atau password salah.');
    }

    public function update(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        $user = Auth::user();
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id . ',id',
        ]);

        
        $newData = $user->update($validated);
        if(!$newData){
            return redirect('/dashboard')->with('failed', 'Profil gagal diperbarui.');

        }
         return redirect('/dashboard')->with('success', 'Profil berhasil diperbarui.');

    }

    public function changePassword(Request $request)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = Auth::user();

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('failed', 'Password lama tidak sesuai.');
        }

        // Update password baru
        $user->password = Hash::make($request->password);
        $update = $user->save();


        if (!$update) {
            return redirect('/dashboard')->with('failed', 'Password gagal diperbarui.');
        }

        return redirect('/dashboard')->with('success', 'Password berhasil diperbarui.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil logout.');
    }

    public function showRequestForm() 
    {
        return view('pages.auth.request');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar']);
        }

        $otp = rand(100000, 999999);

        session([
            'reset_email' => $request->email,
        ]);

        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),
            ]
        );

        $emailsistem = pengaturansistem::where('namapengaturan','emailsistem')->value('value');
        $sandiaplikasiemail = pengaturansistem::where('namapengaturan','sandiaplikasiemail')->value('value');

        Config::set('mail.mailers.smtp.username', $emailsistem);
        Config::set('mail.mailers.smtp.password', $sandiaplikasiemail);
        Config::set('mail.from.address', $emailsistem);
        Config::set('mail.from.name', 'Sistem Monitoring Internet OPD');

        // Kirim via email
        Mail::raw("Kode OTP reset password Anda adalah: $otp (berlaku 5 menit)", function ($message) use ($request, $emailsistem) {
            $message->to($request->email)
                    ->subject('Reset Password OTP')
                    ->from($emailsistem, 'Sistem Monitoring Internet OPD');
        });


        return back()->with('OTP', 'OTP telah dikirim ke email Anda');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $reset = PasswordReset::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$reset) {
            return back()->withErrors(['otp' => 'OTP tidak valid']);
        }

        if (Carbon::now()->greaterThan($reset->expires_at)) {
            return back()->withErrors(['otp' => 'OTP sudah kadaluarsa']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        $reset->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset');
    }
}
