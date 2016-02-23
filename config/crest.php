<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Crest Roots
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'public-root' => "https://public-crest.eveonline.com/",
    'auth-root' => "https://crest-tq.eveonline.com/",

    /*
    |--------------------------------------------------------------------------
    | EVE SSO vars
    |--------------------------------------------------------------------------
    | URIs can be changed to SISI for testing.
    |
    | App Key and App Secret are obtained from developers.eveonline.com
    */
    'sso-auth-uri' => "",
    'sso-token-uri' => "",
    'app-key' => env('SSO_APP_KEY'),
    'app-secret' => env('SSO_APP_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | CREST endpoint versions
    |--------------------------------------------------------------------------
    | CREST endpoints are versioned, these are the specific versions this app
    | was built and tested against. They are included with the requests to
    | avoid being affected by breaking changes in CREST.
    */

    'versions' => [
        'root' => 'application/vnd.ccp.eve.Api-v3+json',
        'decode' => 'application/vnd.ccp.eve.TokenDecode-v1+json',
        'character' => 'application/vnd.ccp.eve.Character-v3+json',
        'contacts' => 'application/vnd.ccp.eve.ContactCollection-v2+json',
    ],

];
