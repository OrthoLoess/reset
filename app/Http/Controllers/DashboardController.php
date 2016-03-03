<?php

namespace Reset\Http\Controllers;

use Illuminate\Http\Request;

use Reset\Http\Requests;
use Reset\Http\Controllers\Controller;
use Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        return 'Welcome to the dashboard '.$user->name;
    }

    public function home()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return 'Welcome to the dashboard '.$user->name;
        } else {
            return 'Landing page<br><br><a href="login">Login</a>';
        }

    }
}
