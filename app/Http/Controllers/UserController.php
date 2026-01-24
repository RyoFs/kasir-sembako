<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('nama')->get();
        return view('pengaturan.user', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'nama'     => 'required',
            'pin'      => 'required|min:4',
            'role'     => 'required',
        ]);

        User::create([
            'username' => $request->username,
            'nama'     => $request->nama,
            'pin'      => $request->pin, // Enkripsi PIN
            'role'     => $request->role,
        ]);

        Alert::success('Berhasil!', 'User Berhasil ditambah.');
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|unique:users,username,' . $id,
            'nama'     => 'required',
            'role'     => 'required',
        ]);

        $user->username = $request->username;
        $user->nama     = $request->nama;
        $user->role     = $request->role;

        if ($request->filled('pin')) {
            $user->pin = $request->pin;
        }

        $user->save();

        Alert::success('Berhasil!', 'User Berhasil diperbarui.');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        Alert::success('Berhasil!', 'User Berhasil dihapus.');
        return redirect()->back();
    }
}
