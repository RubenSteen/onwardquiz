<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('superadmin');
    }

    public function dashboard()
    {
        // abort_if(Gate::denies('index-admin'), 403);

        return Inertia::render('Admin/Dashboard');
    }
}
