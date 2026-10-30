<?php

namespace App\Http\Controllers;

use App\Mail\DemoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DemoMailController extends Controller
{
    public function index(){
        $mailData = [
            'title' => 'this is mail data Title',
            'body' => 'this is mail data Description',
        ];
        Mail::to('infonazmulfci@gmail.com')->send(new DemoMail($mailData));
        dd('Email send successfull.');
    }
}
