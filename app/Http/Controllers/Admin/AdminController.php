<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends BackendController
{
    public function index()
    {
        // abort_if(Gate::denies('index-admin'), 403);

        return Inertia::render('Admin/Index');
    }
}
