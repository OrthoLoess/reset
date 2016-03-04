@extends('layouts.master')

@section('content')
    <div class="jumbotron text-center">
        <h1>Reset</h1>
        <h3>for NPSI</h3>
        <hr>
        <p>Add an API key with access to standingsList to have all your blue standings overridden and set neutral.<br>
            Note: You may have to adjust overview settings to make it work correctly.</p>
    </div>
    <div class="row">

        <div class="panel panel-default col-sm-6 text-center">
            <div class="panel-body">
                <p>Pull all alliance and corp contacts from the API and set a neutral contact for each via CREST.</p>
                {!! Form::open(['url' => 'getxml', 'method' => 'get', 'class' => 'form']) !!}
                <div class="radio-inline">
                    <label><input type="radio" name="justBlues" value="1" checked>Override blue contacts only</label>
                </div>
                <div class="radio-inline">
                    <label><input type="radio" name="justBlues" value="0">Override red and blue contacts</label>
                </div>
                <button type="submit" class="btn btn-default"{{ $hasApi ? '' : ' disabled' }}>Add Contacts</button>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="panel panel-default col-sm-6 text-center">
            <div class="panel-body">
                <p>You currently have {{ $count }} contact overrides in place.</p>
                <a href="removeContacts" class="btn btn-default"{{ $count ? '' : ' disabled'}}>Remove Contacts</a>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-12 text-center">
            {!! Form::model(Auth::user(), ['url' => 'users/postKey', 'class' => 'form-inline']) !!}
            <div class="form-group">
                {{ Form::label('keyId', 'KeyId') }}
                {{ Form::text('keyId', null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('vCode', 'vCode') }}
                {{ Form::text('vCode', null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::submit('Submit', ['class' => 'form-control']) }}
            </div>
            {!! Form::close() !!}
            <p>API key must have contactList and be of type character. <a href="https://community.eveonline.com/support/api-key/CreatePredefined?accessMask=16" target="_blank">Magic link!</a><br>
            It must give access for the character you logged in with: {{ Auth::user()->name }}</p>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-12">
            <h1 id="about">About</h1>
            <p>
                This tool was written for the EVE Crest Competition in March 2016. It is open source, licenced under MIT, source is available on <a href="https://github.com/OrthoLoess/reset">GitHub</a>
            </p>
        </div>
    </div>
@endsection
