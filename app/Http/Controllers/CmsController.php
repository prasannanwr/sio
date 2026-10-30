<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CmsPage;

class CmsController extends Controller
{
    //
    public function show($slug)
    {
        $page = CmsPage::where('slug', $slug)->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content
            ]
        ]);
    }
}
