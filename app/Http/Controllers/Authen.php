<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Authen extends Controller
{
    public function loginPage()
    {
        return Inertia::render('Auth/Auth', [
            'title' => 'Login'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password'    => 'required',
        ], [
            'username.required' => 'Nama pengguna harus diisi',
            'password.required'    => 'Kata sandi harus diisi',
        ]);

        // Cek apakah pengguna ada
        $user = User::where('username', $request->username)->first();

        // Verifikasi kata sandi
        if ($user && Hash::check($request->password, $user->password)) {
            $user->save();

            // Login pengguna dan regenerasi session
            Auth::login($user);
            $request->session()->regenerate();
            $username = auth()->user();

            if ($user->role == 1 || $user->role == 2) {
                return redirect()->route('dashboard')->with([
                    'notif_status' => 'success',
                    'message'      => 'Selamat Datang.' . $username->username,
                ]);
            } else {
                return redirect()->route('costumer')->with([
                    'notif_status' => 'success',
                    'message'      => 'Selamat Datang.' . $username->username,
                ]);
            }
        }

        // Jika login gagal
        return redirect()->back()->with([
            'notif_status' => 'error',
            'message'      => 'Username atau Password salah.',
        ]);
    }


    public function register(Request $request)
    {
        // dd($request->all());
        // Validasi data dari form
        $request->validate(
            [
                'name'     => 'required|max:20',
                'username' => 'required|unique:users,username|max:20',
                'password' => 'required|min:8',
                'phone'    => 'required|numeric|digits_between:10,15',
                'address'  => 'nullable',
                'role'     => 'required|numeric',
            ],
            [
                '*.required'    => 'Kolom wajib diisi',
                '*.numeric' => 'Kolom harus berupa angka.',
                'username.max'  => 'Nama pengguna maksimal 20 karakter',
                'password.min'  => 'Kata sandi minimal 8 karakter',
                'phone.digits_between' => 'Nomor telepon harus terdiri dari antara 10 hingga 15 digit.',
            ]
        );

        // Buat pengguna baru
        $user = User::create([
            'name'      => $request->name, // Menyimpan nama pengguna
            'username'  => $request->username,
            'password'  => Hash::make($request->password), // Enkripsi kata sandi
            'phone'     => $request->phone,
            'address'   => $request->address, // Perbaiki 'alamat' menjadi 'address'
            'role'      => $request->role,
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);

        // Periksa apakah penyimpanan berhasil
        if ($user) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil mendaftarkan akun.',
                'notif_show'   => true,
            ]);
        } else {
            // Jika gagal mendaftarkan akun
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal mendaftarkan akun.',
                'notif_show'   => true,
            ]);
        }
    }

    public function destroy(Request $request)
    {
        // Hapus session pengguna
        $request->session()->invalidate();

        // Hapus token pengguna
        $request->session()->regenerateToken();

        // Redirect ke halaman login
        return redirect()->route('login')->with([
            'notif_status' => 'success',
            'message'      => 'Anda telah berhasil logout.',
            'notif_show'   => true,
        ]);
    }
}
