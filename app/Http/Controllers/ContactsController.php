<?php

namespace Reset\Http\Controllers;

use Illuminate\Http\Request;

use Reset\Classes\Crest;
use Reset\Http\Requests;
use Reset\Http\Controllers\Controller;

class ContactsController extends Controller
{
    public function readCrestContacts(Crest $crest)
    {
        return dd($crest->readCharacterContacts());
    }
}
