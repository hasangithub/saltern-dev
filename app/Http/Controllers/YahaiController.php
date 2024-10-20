<?php

namespace App\Http\Controllers;

use App\Models\Yahai;
use Illuminate\Http\Request;

class YahaiController extends Controller
{
    public function index()
    {
        $yahais = Yahai::all(); // Fetch all Yahai records
        return view('yahai.index', compact('yahais')); // Pass Yahai data to the view
    }
}
