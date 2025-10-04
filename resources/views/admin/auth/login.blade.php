@extends('welcome')
@section('adminAuth')

    <x-login-card
        title="Admin Login"
        :action="route('admin.login')"
        button-class=""
        :register-route="route('admin.register')"
    />

@endsection

