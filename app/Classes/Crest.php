<?php

namespace Reset\Classes;


use GuzzleHttp\Client;

class Crest
{
    // check/get access token

    // Store Contacts

    // Set Contact List

    // Read Contacts

    // Set Contact

    protected $currentURI;
    protected $crestRoot;
    protected $guzzle;

    public function __construct($usePublicCrest = false)
    {
        $this->crestRoot = $usePublicCrest ? config('crest.public-root') : config('crest.auth-root');
        $this->currentURI = $this->crestRoot;
        $this->guzzle = new Client([
            'headers'  => [
                'User-Agent' => '', // TODO Set CREST headers

            ],
        ]);
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
        $uri = $this->currentURI;
        if ($parameters) {
            $uri = $uri.'?'.http_build_query($parameters);
        }
        return $this->getUri($uri);
    }

    public function getUri($uri)
    {

        return true;
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