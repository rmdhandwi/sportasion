<?php

namespace App\Http\Controllers\Costumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Costumer extends Controller
{
    public function costumerPage()
    {
        return Inertia::render('Costumer/Costumer', [
            'title' => 'Home'
        ]);
    }
}
