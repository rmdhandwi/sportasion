<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Dashboard extends Controller
{
    public function dashboardPage()
    {
        return Inertia::render('Admin/Dashboard', [
            'title' => 'Dashboard'
        ]);
    }
}
