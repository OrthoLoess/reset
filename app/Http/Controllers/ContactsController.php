<?php

namespace Reset\Http\Controllers;

use Illuminate\Http\Request;

use Pheal\Pheal;
use Reset\Classes\Crest;
use Reset\Contact;
use Reset\Http\Requests;
use Reset\Http\Controllers\Controller;
use Auth;
use Cache;

class ContactsController extends Controller
{

    protected $crest;
    /**
     * @var \Reset\User
     */
    protected $user;
    protected $pheal;

    public function __construct(Crest $crest)
    {
        $this->crest = $crest;
        $this->user = Auth::user();
        $this->pheal = new Pheal($this->user->keyId, $this->user->vCode, 'char');
    }

    public function getXmlContacts()
    {
        $this->pullStandings();
    }

    public function writeFromXML(Request $request)
    {
        $justBlues = (bool) $request->input('justBlues');
        $contacts = $this->pullStandings();
        $contacts = array_merge($contacts['corp'], $contacts['alliance']);
        $contacts = array_filter($contacts, function ($contact) use ($justBlues) {
            if (((int) $contact['standing']) > 0 || (((int) $contact['standing']) < 0 && !$justBlues)){
                return true;
            }
            return false;
        });
        foreach ($contacts as $contact) {
            $crestContact = $this->makeCrestContact($contact);
            $uri = $this->crest->postContact($crestContact);
            Contact::firstOrCreate([
                'json' => json_encode($crestContact),
                'user_id' => $this->user->id,
                'href' => $uri,
            ]);
            //dd($newContact);
        }
        //dd($this->user->savedContacts);
        return redirect('/')->with('alert-success', 'Contacts written to client');
    }

    public function setApiKey(Request $request)
    {
        if ($this->checkKey($request->input('keyId'), $request->input('vCode'))){
            $this->user->keyId = $request->input('keyId');
            $this->user->vCode = $request->input('vCode');
            $this->user->save();
            return redirect('/')->with('alert-success', 'API key set successfully');
        } else {
            return redirect('/')->withInput()->with('alert-error', 'Key not of correct type, please check access mask');
        }
    }

    public function removeContacts()
    {
        $contacts = $this->user->savedContacts;
        foreach ($contacts as $contact) {
            $this->crest->delete($contact->href);
            $contact->delete();
        }
        return redirect('/')->with('alert-info', 'Contact overrides removed. Contacts should be back how they were.');
    }

    protected function makeCrestContact($xmlContact)
    {
        //dd($xmlContact);
        $contactType = $this->getContactType($xmlContact['contactTypeID']);
        return [
            'standing' => 0,
            'contactType' => $contactType,
            'contact' => [
                'id_str' => $xmlContact['contactID'],
                'href' => config('crest.auth-root').strtolower($contactType).'s/'.$xmlContact['contactID'].'/',
                'name' => $xmlContact['contactName'],
                'id' => (int) $xmlContact['contactID'],
            ],
            'watched' => false,
        ];
    }

    protected function getContactType($typeId)
    {
        if ($typeId == 2)
            return 'Corporation';
        if ($typeId == 16159)
            return 'Alliance';
        return 'Character';
    }

    protected function pullStandings()
    {
        $response = $this->pheal->ContactList(['characterID' => $this->user->id]);
        $contacts = [
            'corp' => $response->corporateContactList->toArray(),
            'alliance' => $response->allianceContactList->toArray(),
        ];
        return $contacts;
    }

    protected function checkKey($keyId, $vCode)
    {
        // TODO: check access mask and character.
        return true;
    }


    /* **************************************************************************
     * Everything below is on hold until crest contacts has support for labels.
     * **************************************************************************
     */

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
