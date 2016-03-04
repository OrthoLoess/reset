<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Http\Request;
use Reset\Classes\EveSSO;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
    Route::get('feedback', function () {
        return 'You\'ve been clicked, punk.';
    });
    Route::get('test', function (\Reset\Classes\EveSSO $sso) {
        $charInfo = [
            'characterID' => 95590328,
        ];
        //$sso->getAffiliation($charInfo);
        //dd($charInfo);
        return $sso->redirectToSSO(config('crest.scopes'));
    });

    Route::get('test64', function (\Reset\Classes\EveSSO $sso) {
        $charInfo = [
            'characterID' => 95590328,
        ];
        //$sso->getAffiliation($charInfo);
        //dd($charInfo);
        return $sso->makeBasicAuthHeader();
    });

    Route::get('/', 'DashboardController@home');
    Route::get('login', function(EveSSO $sso) {
        return $sso->redirectToSSO(config('crest.scopes'));
    });
    Route::get('callback', function (\Reset\Classes\EveSSO $sso, Request $request) {
        $sso->handleCallback($request);
        return redirect('/');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('readcontacts', 'ContactsController@saveCrestContacts');
        Route::get('restorebackup', 'ContactsController@writeFromBackup');
        Route::get('getxml', 'ContactsController@writeFromXML');
        Route::get('removeContacts', 'ContactsController@removeContacts');
    });
});
