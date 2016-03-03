<?php

namespace Reset\Classes;


use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Reset\User;

class EveSSO
{
    protected $refreshToken;
    //protected $accessToken;

    public function __construct()
    {
        //
    }

    public function redirectToSSO($scopes)
    {
        $scopeText = implode(' ', $scopes);
        $redirect = config('crest.sso-auth-uri')
            .'?response_type=code'
            .'&redirect_uri='.$this->getCallbackUrl()
            .'&client_id='.config('crest.app-id')
            .'&scope='.$scopeText
            .'&state='.$this->generateState();
        //dd($redirect);
        return redirect($redirect);
    }

    public function handleCallback(Request $request)
    {
        if ( !($request->has('state') && $request->has('code')) ) {
            throw(new \Exception('Callback does not provide expected parameters.'));
            // TODO: Change to SSOCallbackException
        }
        $this->checkState($request->input('state')); // Throws exception if no state match in cache
        // get an access token
        $accessToken = $this->getAccessToken($request->input('code'));
        $charInfo = $this->getCharInfo($accessToken);
        // Find user or create new user.
        //dd($charInfo);
        $user = User::firstOrNew([
            'id' => $charInfo['CharacterID'],
            'name' => $charInfo['CharacterName'],
        ]);
        $user->refreshToken = $this->refreshToken;
        $user->save();

        \Cache::put('accessToken:'.$user->id, $accessToken , 15);

        \Auth::login($user);

        return true;
    }

    /**
     * @param String|null $code
     * @param User|null $user
     * @return mixed
     */
    public function getAccessToken($code = null, $user = null)
    {

        if ($code){
            // swap code for access token plus refresh token
            $guzzle = new Client();
            $options = [
                'headers' => [
                    'User-Agent' => 'Reset app by Ortho Loess, hosted at '.config('app.url'),
                    'Authorization' => $this->makeBasicAuthHeader(),
                ],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                ],
            ];
            $response = $guzzle->post(config('crest.sso-token-uri'), $options);
            $jsonResponse = json_decode($response->getBody(), true);
            //$this->accessToken = $jsonResponse['access_token'];
            $this->refreshToken = $jsonResponse['refresh_token'];
            return $jsonResponse['access_token'];
        } else {
            // use refresh token to get new access token.
            if (!$user) {
                $user = \Auth::user();
            }
            $token = \Cache::remember('accessToken:'.$user->id, 15, function () use ($user) {
                $guzzle = new Client();
                $options = [
                    'headers' => [
                        'User-Agent' => 'Reset app by Ortho Loess, hosted at '.config('app.url'),
                        'Authorization' => $this->makeBasicAuthHeader(),
                    ],
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $user->refreshToken,
                    ],
                ];
                $response = $guzzle->post(config('crest.sso-token-uri'), $options);
                $jsonResponse = json_decode($response->getBody(), true);
                return $jsonResponse['access_token'];
            });
            return $token;
        }
        // cache access token
    }

    protected function getCharInfo($accessToken)
    {
        $guzzle = new Client();
        $options = [
            'headers' => [
                'User-Agent' => 'Reset app by Ortho Loess, hosted at '.config('app.url'),
                'Authorization' => 'Bearer '.$accessToken,
            ],
        ];
        $response =  $guzzle->get(config('crest.sso-verify-uri'), $options);
        if ($response->getStatusCode() == 200){
            $charInfo = json_decode($response->getBody(), true);
            // $this->getAffiliation($charInfo); // Not currently needed!
            return $charInfo;
        }
        throw(new \Exception('non-200 response from SSO verify endpoint'));
    }

    protected function getAffiliation(&$charInfo)
    {
        $pheal = \App::make('Pheal\Pheal');

        $response = $pheal->eveScope->CharacterAffiliation(["ids" => $charInfo['CharacterID']]);
        if ($response->characters[0]->characterID == $charInfo['CharacterID']) {
            $charInfo['corporationID'] = (int) $response->characters[0]->corporationID;
            $charInfo['corporationName'] = $response->characters[0]->corporationName;
            $charInfo['allianceID'] = (int) $response->characters[0]->allianceID;
            $charInfo['allianceName'] = $response->characters[0]->allianceName;
        }
    }

    protected function generateState()
    {
        // TODO: generate random string and store in cache (60 mins)
        $state = 'exampleState';
        return $state;
    }

    protected function checkState($state)
    {
        // TODO: Check provided state is present in cache and remove it.
        if (true)
            return true;
        throw(new \Exception('Error checking state after SSO callback.')); // TODO: Change to SSOStateException
    }

    protected function getCallbackUrl()
    {
        $base = config('app.url');
        if (substr($base, -1) == '/') {
            $url = $base.'callback';
        } else {
            $url = $base.'/callback';
        }
        return $url;
    }

    public function makeBasicAuthHeader()
    {
        return 'Basic '.base64_encode(config('crest.app-id').':'.config('crest.app-secret'));
    }

}