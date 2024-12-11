<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\categories;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Kategori extends Controller
{
    public function kategoriPage()
    {
        $category = categories::all();
        return Inertia::render('Admin/Kategori', [
            'title' => 'Kategori',
            'category' => $category
        ]);
    }

    public function AddKategori(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|unique:categories,name'
        ], [
            '*.required' => 'Kolom wajib diisi!',
            'name.unique' => 'Nama kategori telah digunakan!'
        ]);

        $insert = categories::create([
            'name' =>  ucwords($request->name),
            'created_at' =>  Carbon::now('Asia/Jayapura')
        ]);

        if ($insert) {
            return redirect()->route('kategori')->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil menbambahkan kategori: ' . $request->name,
            ]);
        } else {
            return redirect()->route('kategori')->with([
                'notif_status' => 'error',
                'message'      => 'Gagal menambahkan kategori: ' . $request->name,
            ]);
        }
    }

    public function editKategori(Request $request, $id)
    {
        // Validasi input form, memastikan nama kategori unik untuk kategori lain
        $request->validate([
            'name' => 'required|unique:categories,name,' . $id . ',id', // Abaikan validasi unik untuk kategori yang memiliki id ini
        ], [
            '*.required' => 'Kolom wajib diisi!',
            'name.unique' => 'Nama kategori telah digunakan!',
        ]);

        // Cari kategori berdasarkan id
        $category = categories::where('id', $id)->first();

        // Jika kategori tidak ditemukan, kembalikan pesan error
        if (!$category) {
            return redirect()->route('kategori')->with([
                'notif_status' => 'error',
                'message'      => 'Kategori tidak ditemukan!',
            ]);
        }

        // Perbarui data kategori
        $category->update([
            'name'       => ucwords($request->name),  // Menggunakan ucwords untuk kapitalisasi pertama setiap kata
            'updated_at' => Carbon::now('Asia/Jayapura'),
        ]);

        // Berikan notifikasi berdasarkan hasil pembaruan
        if ($category->wasChanged()) {
            return redirect()->route('kategori')->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil memperbarui kategori: ' . $request->name,
            ]);
        } else {
            return redirect()->route('kategori')->with([
                'notif_status' => 'error',
                'message'      => 'Gagal memperbarui kategori: ' . $request->name,
            ]);
        }
    }

    public function deleteKategori($id)
    {
        // Cari kategori berdasarkan id
        $category = categories::where('id', $id)->first(); // Cari berdasarkan id

        // Jika kategori tidak ditemukan, kembalikan pesan error
        if (!$category) {
            return redirect()->route('kategori')->with([
                'notif_status' => 'error',
                'message'      => 'Kategori tidak ditemukan!',
            ]);
        }

        // Hapus kategori
        $delete = $category->delete();

        // Berikan notifikasi berdasarkan hasil penghapusan
        if ($delete) {
            return redirect()->route('kategori')->with([
                'notif_status' => 'success',
                'message'      => 'Kategori berhasil dihapus!',
            ]);
        } else {
            return redirect()->route('kategori')->with([
                'notif_status' => 'error',
                'message'      => 'Gagal menghapus kategori!',
            ]);
        }
    }

}
