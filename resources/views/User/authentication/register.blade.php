@extends('welcome')

@section('panelName', 'Welcome...')

@section('adminAuth')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; background-color: #f8f9fa;">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow rounded-3">
                <div class="card-header bg-warning text-white text-center rounded-top">
                    <h4 class="mb-0">User Registration</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('user.register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    autocomplete="name" autofocus>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    autocomplete="email">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="phone"
                                   class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}"
                                    autocomplete="tel">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password"
                                   class="form-control"
                                    autocomplete="new-password">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">Register</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <small>Already have an account?
                            <a href="{{ route('user.login') }}" class="text-decoration-none">Login here</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
