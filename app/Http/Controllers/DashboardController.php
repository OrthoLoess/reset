<?php

namespace Reset\Http\Controllers;

use Illuminate\Http\Request;

use Reset\Http\Requests;
use Reset\Http\Controllers\Controller;
use Auth;

class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function home()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $contacts = $user->savedContacts;
            $count = $contacts->count();
            return view('home', [
                'count' => $count,
                'hasApi' => $user->keyId ? true : false,
            ]);
        } else {
            return view('landing');
        }

    }
}
