<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        // Slug diye page khujbe, na pele 404 error dibe
        $page = DynamicPage::where('page_slug', $slug)->firstOrFail();

        return view('frontend.dynamic_page', compact('page'));
    }
}
