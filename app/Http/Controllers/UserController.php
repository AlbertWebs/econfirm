<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Return the admin dashboard view
        return view('user.dashboard');
    }
}