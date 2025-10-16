<?php

namespace App\Http\Controllers\Storefronts;

use App\Http\Controllers\Controller;
use App\Models\Storefront;
use Inertia\Inertia;

class StorefrontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Storefront $storefront)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Storefront $storefront)
    {
        //
    }
}
