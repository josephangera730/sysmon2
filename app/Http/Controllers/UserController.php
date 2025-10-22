<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
           return redirect('/login');
        }
      
        if(!Gate::allows('isSuperadmin')){
            return redirect('dashboard');
        }

        $data = User::wherenot('id', Auth::user()->id)->get();

        return view('pages.user.index', compact('data'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }

        $request->validate([
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string'
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make('qwerty0987654321'),
            'role' => $request->role,
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }


        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string'
        ]);

        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;

      
        $user->password = Hash::make('123456789');
        

        $user->save();

        return redirect()->route('user.index')->with('success', 'User berhasil diupdate!');
    }

    public function destroy($id)
    {
        if (!Auth::check()) {
           return redirect('/login');
        }

        if(!Gate::allows('isSuperadmin')){
             return redirect('dashboard');
        }

        
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus!');
    }
}
