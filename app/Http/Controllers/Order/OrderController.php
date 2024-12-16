<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\products;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function addOrder(Request $req)
    {
        $produk = products::find($req->id_product);
        $order = Order::where('id_product', $req->id_product)->where('id_user', auth()->guard()->user()->id)->where('status', 2)->get();

        dd($produk->price * $req->quantity);


        if (!$order->isEmpty()) {

            $order[0]->quantity += $req->quantity;
            $order[0]->total_price = $produk->price * $order[0]->quantity;
            $insertOrder = $order[0]->save();

            $notif = [
                'notif_status' => 'success',
                'message' => 'Berhasil update keranjang!',
            ];
        } else {
            $insertOrder = Order::create([
                'id_user' => auth()->guard()->user()->id,
                'id_product' => $req->id_product,
                'quantity' => $req->quantity,
                'order_date' => Carbon::now('Asia/Jayapura'),
                'status' => 2,
                'total_price' => $produk->price * $req->quantity,
                'created_at' => Carbon::now('Asia/Jayapura')
            ]);

            $notif = [
                'notif_status' => 'success',
                'message' => 'Berhasil menambahkan keranjang!',
            ];
        }

        if ($insertOrder) {
            return redirect()->back()->with($notif);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Gagal menambahkan keranjang',
            ]);
        }
    }

    public function updateOrder(Request $req)
    {
        $order = Order::find($req->id_cart);
        $produk = products::find($order->id_product);
        
        $order->quantity = $req->quantity;
        $order->total_price = $produk->price * $order->quantity;
        $insertOrder = $order->save();

        if ($insertOrder) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Berhasil update keranjang',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Gagal update keranjang',
            ]);
        }
    }

    public function hapusOrder(Request $req)
    {
        $hapusOrder = Order::find($req->id_cart)->delete();

        if ($hapusOrder) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Berhasil menghapus produk dari keranjang!',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Gagal menghapus produk dari keranjang',
            ]);
        }
    }

    public function pesanOrder(Request $req)
    {
        $order = Order::find($req->id_cart);

        $order->quantity = $req->quantity;

        $order->status = 1;
        $order->total_price = $order->total_price * $order->quantity;
        $insertOrder = $order->save();

        if ($insertOrder) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Berhasil memesan produk!',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Gagal memesan produk!',
            ]);
        }
    }

    public function batalOrder(Request $req)
    {
        $order = Order::find($req->id_cart);

        $order->status = 2;
        $insertOrder = $order->save();

        if ($insertOrder) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Pesanan Produk Dibatalkan!',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Pesanan Produk Gagal Dibatalkan!',
            ]);
        }
    }

    public function orderPage()
    {
        $order = Order::with('product', 'user')->get();

        return Inertia::render('Admin/Orders', [
            'title' => 'Orders',
            'orders' => $order,
        ]);
    }

    public function acceptOrder($id)
    {
        // Temukan order berdasarkan id
        $order = Order::with('user')->find($id); // Menggunakan relasi untuk mengambil user

        if (!$order) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Orderan tidak ditemukan',
            ]); 
        } else {
            // Update status order
            $order->status = 0; // Misalnya, 0 berarti diterima
            $order->save();

            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Orderan dari ' . $order->user->name . ' berhasil diterima',
            ]);
        }
    }

    public function CancelOrder(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required'
        ],[
            'catatan.required' => 'Catatan tidak boleh kosong'
        ]);

        // Temukan order berdasarkan id
        $order = Order::with('user')->find($id); // Menggunakan relasi untuk mengambil user

        if (!$order) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Orderan tidak ditemukan',
            ]);
        } else {
            // Update status order
            $order->status = 3;
            $order->catatan = $request->catatan; // Misalnya, 0 berarti diterima
            $order->save();

            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Orderan dari ' . $order->user->name . ' dicancel',
            ]);
        }
    }
}
