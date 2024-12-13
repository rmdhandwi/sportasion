<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\products;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TransactionController extends Controller
{
    //
    public function addTransaction(Request $req)
    {
        $order = Order::find($req->id_cart);

        $linkFile = NULL;

        $file = $req->file('bukti_bayar');
        $filename = Str::random(10);
        $extension = $file->getClientOriginalExtension();
        $filename = $filename . '-' . Carbon::now('Asia/Jayapura')->format('Y-m-d') . '.' . $extension;
        $path = 'uploads/bukti_tf/' . $filename;

        $req->validate([
            'bukti_tf'       => 'mimes:jpg,jpeg,png',
        ], [
            'bukti_tf.mimes'   => 'Gambar harus berformat jpg, jpeg, atau png',
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

        $insert = Transaction::create([
            'id_order' => $req->id_cart,
            'payment_method' => $req->metode_bayar,
            'amount' => $order->total_price,
            'bukti_tf' => $linkFile,
            'status' => 2,
            'payment_date' => Carbon::now('Asia/Jayapura'),
            'created_at' => Carbon::now('Asia/Jayapura'),
        ]);

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Berhasil mengkonfirmasi pembayaran',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Gagal mengkonfirmasi pembayaran',
            ]);
        }
    }

    public function transactionPage()
    {
        $transaction = Transaction::with('order.product', 'order.user')->get();

        return Inertia::render('Admin/Transaction', [
            'title' => 'Transaction',
            'transaction' => $transaction
        ]);
    }

    public function accaptTransaction(Request $request, $id)
    {

        $request->validate([
            'orderId' => 'required|integer|exists:orders,id',
            'quantity' => 'required|integer|min:1',
            'productId' => 'required|integer|exists:products,id',
        ]);

        $transaction = Transaction::findOrFail($id);

        if ($transaction->status !== 2) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Transaksi telah diterima',
            ]);
        }

        $order = Order::findOrFail($request->orderId);

        $product = products::findOrFail($request->productId);
        if ($product->stock < $request->quantity) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Stock tidak Mencukupi',
            ]);
        }

        $product->stock -= $request->quantity;
        $product->save();

        $transaction->status = 0;
        $transaction->save();

        return redirect()->back()->with([
            'notif_status' => 'success',
            'message' => 'Berhasil menerima transaksi',
        ]);
    }

    public function cancelTransaction(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required',
        ], [
            'catatan.required' => 'Catatan tidak boleh kosong'
        ]);

        $transaction = Transaction::findOrFail($id);

        if ($transaction->status === 1) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Transaksi tidak dapat dicancel',
            ]);
        }

        $transaction->status = 1;
        $transaction->catatan = $request->catatan;
        $transaction->save();

        return redirect()->back()->with([
            'notif_status' => 'success',
            'message' => 'Transaksi berhasil dicancel',
        ]);
    }
}
