<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    /** Halaman Syarat & Ketentuan. */
    public function terms()
    {
        return view('frontend.pages.terms');
    }

    /** Halaman Kebijakan Privasi. */
    public function privacy()
    {
        return view('frontend.pages.privacy');
    }
}
