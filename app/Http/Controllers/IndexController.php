<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function dashboard() {
        return Inertia::render('Dashboard');
    }

    public function chat($id) {
        return Inertia::render('Dashboard');
    }
}
