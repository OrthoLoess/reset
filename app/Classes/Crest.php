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

    public function __construct($usePublicCrest = false, EveSSO $sso)
    {
        $this->crestRoot = $usePublicCrest ? config('crest.public-root') : config('crest.auth-root');
        $this->currentURI = $this->crestRoot;
        $this->sso = $sso;
        $this->guzzle = new Client();
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

    public function get($version = null, $parameters = null)
    {
        // Perform a GET request at the $currentURI
        $uri = $this->currentURI;
        if ($parameters) {
            $uri = $uri.'?'.http_build_query($parameters);
        }
        return $this->getUri($uri, $version);
    }

    public function getUri($uri, $version = null)
    {
        $options = [
            'Headers' => [
                'User-Agent' => 'Reset app by Ortho Loess, hosted at '.config('app.url'),
                'Authorization' => 'Bearer '.$this->sso->getAccessToken(),
            ],
        ];
        if ($version) {
            $options['Headers']['Accept'] = $version;
        }

        $this->guzzle->get($uri, $options);
        return true;
    }

    public function post($payloadArray)
    {
        // Perform a POST request at the current URI, sending $payloadArray as json.
    }

    public function readCharacterContacts()
    {
        $contacts = [];

        $version = config('crest.versions.contacts');
        $this->returnToRoot()->walk('decode')->walk('Character')->walk('Contacts')->get($version);

        return $contacts;
    }


}