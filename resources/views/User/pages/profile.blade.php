@extends('layouts.main')

@section('title', 'Profile')

@section('content')
    <style>
        body {
            background: linear-gradient(to right, #ece9e6, #ffffff);
        }
        .profile-card {
            max-width: 700px;
            margin: auto;
            border: none;
            border-radius: 15px;
        }
        .profile-img {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #dee2e6;
        }
    </style>

    <div class="container py-5">
        <h2 class="text-center mb-4">User Profile</h2>

        <div class="card shadow profile-card">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="me-md-4 mb-4 mb-md-0 text-center text-md-start">
                    <h5 class="card-title">Welcome, {{ auth()->user()->name }}</h5>
                    <p class="card-text"><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p class="card-text">
                        <strong>Registered On:</strong>
                        {{ auth()->user()->created_at->format('F j, Y') }}
                    </p>

                    <a href="#" class="btn btn-outline-primary mt-2">Edit Profile</a>
                    <a class="btn btn-danger mt-2 ms-md-2" href="#"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>

                    <form id="logout-form" action="{{ route('user.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>

                <div class="text-center">
                    @php
                        $photo = auth()->user()->profile_photo
                            ? asset('storage/' . auth()->user()->profile_photo)
                            : asset('storage/profile_photos/default.jpg');
;
                    @endphp
                    <img src="{{ $photo }}" alt="Profile Photo" class="profile-img shadow-sm">
                </div>
            </div>
        </div>
    </div>
@endsection
