<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\myNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class myController extends Controller
{
    public function notification(){
        $users = User::select('name', 'email')->first();

        $data = [];  
        $data = [
            'name' => $users->name,
            'comment' => 'Some One comment on your Post',
        ];   
         Notification::send($users, new myNotification($data)); 
        
        return view('sendNotification');
    }
}
