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

    protected $currentEndpoint;
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
        $this->currentEndpoint = 'root';
        $this->currentURI = $this->crestRoot;
        return $this;
    }

    public function walk($endpointName)
    {
        $response = $this->get();
        $this->currentURI = $response[$endpointName]['href'];
        $this->currentEndpoint = $endpointName;
        return $this;
    }

    public function get($parameters = null)
    {
        // Perform a GET request at the $currentURI
        $version = config('crest.versions.'.$this->currentEndpoint);
        $uri = $this->currentURI;
        if ($parameters) {
            $uri = $uri.'?'.http_build_query($parameters);
        }
        $arrayResponse = $this->getUri($uri, $version);

        // If resource is paginated, pull all pages and combine.
        while (array_key_exists('items', $arrayResponse) && array_key_exists('next', $arrayResponse)) {
            $nextPage = $this->getUri($arrayResponse['next']['href'], $version);
            $nextPage['items'] = array_merge($arrayResponse['items'], $nextPage['items']);
            $arrayResponse = $nextPage;
        }
        return $arrayResponse;
    }

    public function getUri($uri, $version = null)
    {
        $options = [
            'headers' => [
                'User-Agent' => 'Reset app by Ortho Loess, hosted at '.config('app.url'),
                'Authorization' => 'Bearer '.$this->sso->getAccessToken(),
            ],
        ];
        if ($version) {
            $options['headers']['Accept'] = $version;
        }
        $response = $this->guzzle->get($uri, $options);
        return json_decode($response->getBody(), true);
    }

    public function post($payloadArray)
    {
        // Perform a POST request at the current URI, sending $payloadArray as json.
        $options = [
            'headers' => [
                'User-Agent' => 'Reset app by Ortho Loess, hosted at '.config('app.url'),
                'Authorization' => 'Bearer '.$this->sso->getAccessToken(),
            ],
            'json' => $payloadArray,
        ];
        return $this->guzzle->post($this->currentURI, $options);
    }

    public function delete($uri)
    {
        $options = [
            'headers' => [
                'User-Agent' => 'Reset app by Ortho Loess, hosted at '.config('app.url'),
                'Authorization' => 'Bearer '.$this->sso->getAccessToken(),
            ],
        ];
        $this->guzzle->delete($uri, $options);
    }

    public function readCharacterContacts()
    {
        return $this->returnToRoot()->walk('decode')->walk('character')->walk('contacts')->get()['items'];
    }

    public function postContact($contact)
    {
        //unset($contact['href']);

        //dd($contact);
        $response = $this->returnToRoot()->walk('decode')->walk('character')->walk('contacts')->post($contact);
        return $response->getHeader('Location')[0];
    }
}