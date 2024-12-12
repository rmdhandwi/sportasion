<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\products;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function addOrder(Request $req)
    {
        $produk = products::find($req->id_product);
        $userCartOrder = OrderDetails::where('id_product',$req->id_product)->get();
        
        if(!$userCartOrder->isEmpty())
        {
            $order = Order::find($userCartOrder[0]->id_order);
            
            $userCartOrder[0]->quantity += $req->quantity;
            $orderDetails = $userCartOrder[0]->save();

            $order->total_price = $produk->price * $userCartOrder[0]->quantity;
            $insertOrder = $order->save();
        }
        else
        {
            $insertOrder = Order::create([
            'id_user' => auth()->guard()->user()->id,
            'order_date' => Carbon::now('Asia/Jayapura'),
            'status' => 2,
            'total_price' => $produk->price * $req->quantity,
            'created_at' => Carbon::now('Asia/Jayapura')
            ]);
    
            $orderDetails = OrderDetails::create([
                'id_order' => $insertOrder->id,
                'id_product' => $req->id_product,
                'quantity' => $req->quantity,
                'price' => $produk->price,
            ]);
        }

        if($insertOrder && $orderDetails)
        {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'message' => 'Berhasil menambahkan keranjang!',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'message' => 'Gagal menambahkan keranjang',
            ]);
        }
    }

    public function updateOrder(Request $req)
    {
        $userCartOrder = OrderDetails::where('id_order',$req->id_cart)->get();
        
        
        $order = Order::find($req->id_cart);
        
        $userCartOrder[0]->quantity = $req->quantity;
        $orderDetails = $userCartOrder[0]->save();

        $order->total_price = $userCartOrder[0]->price * $userCartOrder[0]->quantity;
        $insertOrder = $order->save();

        if($insertOrder && $orderDetails)
        {
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
        $hapusOrderDetails = OrderDetails::where('id_order', $req->id_cart)->delete();
        $hapusOrder = Order::find($req->id_cart)->delete();

        if ($hapusOrderDetails && $hapusOrder) {
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
        $userCartOrder = OrderDetails::where('id_order', $req->id_cart)->get();
        

        $order = Order::find($req->id_cart);

        $userCartOrder[0]->quantity = $req->quantity;
        $orderDetails = $userCartOrder[0]->save();

        $order->status = 1;
        $order->total_price = $userCartOrder[0]->price * $userCartOrder[0]->quantity;
        $insertOrder = $order->save();

        if ($insertOrder && $orderDetails) {
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
}
