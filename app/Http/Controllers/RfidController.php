<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RfidController extends Controller
{
    public function index()
    {
        return view('rfid.index'); // We'll create this view next
    }
}
