<?php

namespace App\Http\Controllers\Costumer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\products;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Costumer extends Controller
{
    public function costumerPage()
    {
        $dataCart = Order::where('id_user', auth()->guard()->user()->id)->with('orderDetails.product')->get();

        $dataProduk = products::with('categories')->select('id','name','description','price','image','stock')->get();
        return Inertia::render('Costumer/Costumer', [
            'title' => 'Home',
            'dataProduk' => $dataProduk,
            'dataCart' => $dataCart,
        ]);
    }
}
