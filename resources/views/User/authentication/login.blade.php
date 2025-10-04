@extends('welcome')

@section('panelName', 'Welcome...')

@section('adminAuth')
    @component('components.login-card', [
        'title' => 'User Login',
        'action' => route('user.login'),
        'buttonClass' => 'warning',
        'registerRoute' => route('user.register'),
        'forgotRoute' => route('user.forgot')
    ])
    @endcomponent
@endsection
