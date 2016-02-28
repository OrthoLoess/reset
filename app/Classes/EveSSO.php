<?php

namespace Reset\Classes;


class EveSSO
{
    protected $authUri;
    protected $tokenUri;
    protected $appKey;
    protected $appSecret;
    protected $refreshToken;
    protected $accessToken;

    public function __construct()
    {
        // TODO read configs and set vars
    }

    public function redirectToSSO($scopes)
    {
        // TODO take list of scopes and redirect with request - array or text list?
    }

    public function handleCallback()
    {
        // Check state matches
        // get an access token
        // store refresh token
    }

    public function getAccessToken($code = null)
    {
        // cache using remember for 15 mins
    }

    public function getCharInfo()
    {
        //
    }

    protected function generateState()
    {
        //
    }

    protected function checkState()
    {
        //
    }

    protected function storeRefreshToken()
    {
        //
    }

    protected function renewAccessToken()
    {
        //
    }
}