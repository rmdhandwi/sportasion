<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\categories as ModelCategories;
use App\Models\products as ModelsProducts;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class Products extends Controller
{
    public function productsPage()
    {
        $categories = ModelCategories::all();
        $products = ModelsProducts::with('categories')->get();
        return Inertia::render('Admin/Product', [
            'title' => 'Products',
            'categories' => $categories,
            'products' => $products
        ]);
    }

    public function addProduct(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id_category' => 'required|numeric',
            'name'        => 'required|',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric',
        ], [
            '*.required'     => 'Kolom harus diisi!',
            '*.numeric'      => 'Kolom harus berupa angka!',

        ]);

        $linkFile = null;

        // Jika ada file gambar yang diunggah, proses penyimpanan file terlebih dahulu
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Str::random(10);
            $extension = $file->getClientOriginalExtension();
            $filename = $filename . '-' . Carbon::now('Asia/Jakarta')->format('Y-m-d') . '.' . $extension;
            $path = 'uploads/product/' . $filename;

            $request->validate([
                'image'       => 'mimes:jpg,jpeg,png',
            ], [
                'image.mimes'   => 'Gambar harus berformat jpg, jpeg, atau png',
            ]);

            // Simpan file ke storage publik
            $insertFile = Storage::disk('public')->put($path, file_get_contents($file));

            // Jika penyimpanan file berhasil, simpan link file
            if ($insertFile) {
                $linkFile = Storage::url($path);
            } else {
                // Jika penyimpanan file gagal, kembalikan dengan notifikasi error
                return redirect()->back()->with([
                    'notif_status' => 'error',
                    'message'      => 'Gagal mengunggah gambar.',
                ]);
            }
        }

        // Buat kategori baru
        $insert = ModelsProducts::create([
            'id_category'      => $request->id_category,
            'name'             => ucwords($request->name),
            'description'      => $request->deskripsi,
            'price'            => $request->price,
            'stock'            => $request->stock,
            'image'            => $linkFile,
            'created_at'       => Carbon::now('Asia/Jayapura'),
        ]);

        // Periksa apakah penyimpanan berhasil
        if ($insert) {

            return redirect()->back()->with([
                'notif_status' => 'success',
                'message'      => 'Berhasil menambahkan gambar dan product.',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Gagal menambahkan menu.',
            ]);
        }
    }

    public function editProduct(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'id_category' => 'required|numeric',
            'name'        => 'required',
            'price'       => 'required|numeric',
            'stock'       => 'required|numeric',
            'image'       => 'nullable|mimes:jpg,jpeg,png', // Gambar bersifat opsional
        ], [
            '*.required' => 'Kolom harus diisi!',
            '*.numeric'  => 'Kolom harus berupa angka!',
            'image.mimes' => 'Gambar harus berformat jpg, jpeg, atau png',
        ]);

        // Ambil produk berdasarkan ID
        $product = ModelsProducts::find($id);

        // Jika produk tidak ditemukan, kembalikan dengan notifikasi error
        if (!$product) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Produk tidak ditemukan.',
            ]);
        }

        // Variabel untuk menyimpan link gambar
        $linkFile = $product->image; // Simpan link gambar lama

        // Jika ada file gambar yang diunggah, proses penyimpanan file terlebih dahulu
        if ($request->hasFile('image')) {
            // Hapus file lama jika ada
            if (
                $linkFile &&
                Storage::disk('public')->exists(str_replace('/storage/', '', $linkFile))
            ) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $linkFile));
            }

            $file = $request->file('image');
            $filename = Str::random(10);
            $extension = $file->getClientOriginalExtension();
            $filename = $filename . '-' . Carbon::now('Asia/Jakarta')->format('Y-m-d') . '.' . $extension;
            $path = 'uploads/product/' . $filename;

            // Simpan file ke storage publik
            $insertFile = Storage::disk('public')->put($path, file_get_contents($file));

            // Jika penyimpanan file berhasil, simpan link file
            if ($insertFile) {
                $linkFile = Storage::url($path); // Update link file dengan path baru
            } else {
                // Jika penyimpanan file gagal, kembalikan dengan notifikasi error
                return redirect()->back()->with([
                    'notif_status' => 'error',
                    'message'      => 'Gagal mengunggah gambar.',
                ]);
            }
        }

        // Update data produk
        $product->update([
            'id_category' => $request->id_category,
            'name'        => ucwords($request->name),
            'description' => $request->description, // Pastikan ini sesuai dengan input
            'price'       => $request->price,
            'stock'       => $request->stock,
            'image'       => $linkFile,
            'updated_at'  => Carbon::now('Asia/Jakarta'),
        ]);

        // Kembalikan dengan notifikasi sukses
        return redirect()->back()->with([
            'notif_status' => 'success',
            'message'      => 'Berhasil memperbarui produk.',
        ]);
    }

    public function deleteProduct($id)
    {
        // Ambil produk berdasarkan ID
        $product = ModelsProducts::find($id);

        // Jika produk tidak ditemukan, kembalikan dengan notifikasi error
        if (!$product) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message'      => 'Produk tidak ditemukan.',
            ]);
        }

        $linkFile = $product->image;

        // Hapus file gambar dari storage jika ada
        if (
            $linkFile &&
            Storage::disk('public')->exists(str_replace('/storage/', '', $linkFile))
        ) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $linkFile));
        }

        // Hapus produk dari database
        $product->delete();

        // Kembalikan dengan notifikasi sukses
        return redirect()->back()->with([
            'notif_status' => 'success',
            'message'      => 'Produk berhasil dihapus.',
        ]);
    }
}
