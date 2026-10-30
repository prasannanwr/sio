<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function privacy()
    {
        return view('documents.privacy');
    }
    public function terms()
    {
        return view('documents.terms');
    }
    public function faqs()
    {
        return view('documents.faqs');
    }
    public function help()
    {
        return view('documents.help');
    }
}
