<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\categories;
use App\Models\Order;
use App\Models\products;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PHPUnit\Framework\Constraint\Count;

class Dashboard extends Controller
{
    public function dashboardPage()
    {
        $categories = categories::count();
        $products = products::count();
        $orders = Order::count();
        $transactions = Transaction::count();
        $customers = User::where('role', 3)->count(); // Ambil jumlah dari role 3

        return Inertia::render('Admin/Dashboard', [
            'title' => 'Dashboard',
            'data' => [
                'categories' => $categories,
                'products' => $products,
                'orders' => $orders,
                'transactions' => $transactions,
                'customers' => $customers,
            ],
        ]);
    }
}
