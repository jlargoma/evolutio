<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Years;

class AppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
     
    function no_allowed() {return view('errors.403');}
    
    /**
     * @return mixed
     */
    protected static function getActiveYear()
    {
        $activeYear = Years::where('active', 1)->first();
        return $activeYear;
    }
}
