<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DynamicPageController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $pages = DynamicPage::where('status', 'active')
            ->select('id', 'page_title', 'page_slug')
            ->latest()
            ->get();

        return $this->success($pages, 'Dynamic pages fetched successfully', 200);
    }


    public function show(string $slug)
    {
        $page = DynamicPage::where('page_slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!$page) {
            return $this->error([], 'Page not found', 404);
        }

        return $this->success($page, 'Dynamic page fetched successfully', 200);
    }
}
