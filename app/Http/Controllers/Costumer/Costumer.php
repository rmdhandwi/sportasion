<?php

namespace App\Http\Controllers\Costumer;

use App\Http\Controllers\Controller;
use App\Models\categories;
use App\Models\Order;
use App\Models\products;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Costumer extends Controller
{
    public function costumerPage()
    {
        $dataCart = Order::where('id_user', auth()->guard()->user()->id)->where('status','!=',0)->with('product')->get();
        $dataTransaksi = Order::where('id_user', auth()->guard()->user()->id)->where('status', 0)->with('product')->with('transaksi')->get();
        $dataKategori = categories::select('id','name')->get();
        $dataProduk = products::with('categories')->get();

        return Inertia::render('Costumer/Costumer', [
            'dataKategori' => $dataKategori,
            'dataProduk' => $dataProduk,
            'dataTransaksi' => $dataTransaksi,
            'dataCart' => $dataCart,
        ]);
    }

    public function kategoriPage(Request $req)
    {
        $dataCart = Order::where('id_user', auth()->guard()->user()->id)->with('product')->get();
        
        $dataKategori = categories::select('id', 'name')->get();
        $dataProduk = products::where('id_category',$req->id)->with('categories')->get();

        return Inertia::render('Costumer/Costumer', [
            'dataKategori' => $dataKategori,
            'dataProduk' => $dataProduk,
            'dataCart' => $dataCart,
        ]);

    }
}
