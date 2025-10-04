@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center mb-4">User Profile</h2>

        <div class="card shadow rounded">
            <div class="card-body">
                <h5 class="card-title">Welcome, {{ auth()->user()->name }}</h5>
                {{--                <p class="card-text"><strong>Username:</strong> {{ auth()->user()->username }}</p>--}}
                <p class="card-text"><strong>Email:</strong> {{ auth()->user()->email }}</p>
                <p class="card-text"><strong>Registered On:</strong> {{ auth()->user()->created_at }}</p>
                <div class="mt-4">
                    <a class="btn btn-danger" href="{{ route('admin.logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>”
            </div>
        </div>
    </div>
@endsection
