<?php

namespace Reset\Http\Controllers;

use Illuminate\Http\Request;

use Pheal\Pheal;
use Reset\Classes\Crest;
use Reset\Http\Requests;
use Reset\Http\Controllers\Controller;
use Auth;
use Cache;

class ContactsController extends Controller
{

    protected $crest;
    protected $user;

    public function __construct(Crest $crest)
    {
        $this->crest = $crest;
        $this->user = Auth::user();
    }

    public function saveCrestContacts()
    {
        $this->user->contacts = $this->getCurrentContacts();
        $this->user->save();
        //dd($this->user->contacts);
        return redirect('/');
    }

    public function writeFromBackup()
    {
        $this->deleteAll();
        //dd('break');
        $contacts = json_decode($this->user->contacts, true);
        //dd($contacts);
        foreach ($contacts as $contact) {
            $this->crest->postContact($contact);
        }
        Cache::put('contacts:'.$this->user->id, $this->user->contacts, 5);
    }

    public function writeFromXML(Pheal $pheal)
    {
        //
    }

    protected function getCurrentContacts()
    {
        return Cache::remember('contacts:'.$this->user->id, 5, function() {
            $contacts = $this->crest->readCharacterContacts();
            $compressedContacts = array_map(function ($contact) {
                unset($contact['character']);
                unset($contact['blocked']);
                return $contact;
            }, $contacts);
            return json_encode($compressedContacts);
        });
    }

    protected function deleteAll()
    {
        //Cache::put('contacts:'.$this->user->id, json_encode([]), 5);
        $contacts = json_decode($this->getCurrentContacts(), true);
        //dd($contacts);

        //if ($contacts) {
            foreach ($contacts as $contact) {
                $this->delete($contact);
            }
        //}
        Cache::put('contacts:'.$this->user->id, json_encode([]), 5);
    }

    protected function delete($contact)
    {
        $href = $contact['href'];
        unset($contact['href']);
        $this->crest->delete($href);
    }
}
