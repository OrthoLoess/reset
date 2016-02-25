<?php

namespace Reset\Classes;


class Crest
{
    // check/get access token

    // Store Contacts

    // Set Contact List

    // Read Contacts

    // Set Contact

    protected $currentURI;
    protected $crestRoot;

    public function __construct($usePublicCrest = false)
    {
        $this->crestRoot = $usePublicCrest ? config('crest.public-root') : config('crest.auth-root');
        $this->currentURI = $this->crestRoot;
    }

    public function returnToRoot()
    {
        //

        return $this;
    }

    public function walk($endpointName)
    {
        //

        return $this;
    }

    public function get($parameters = null)
    {
        // Perform a GET request at the $currentURI
    }

    public function post($payloadArray)
    {
        // Perform a POST request at the current URI, sending $payloadArray as json.
    }

    public function readCharacterContacts()
    {
        $contacts = [];

        $this->returnToRoot()->walk('decode')->walk('Character')->walk('Contacts')->get();

        return $contacts;
    }


}