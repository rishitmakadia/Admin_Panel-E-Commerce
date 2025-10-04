<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; background-color: #f8f9fa;">
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow rounded-3">
            <div class="card-header {{ !empty($buttonClass) ? 'bg-'.$buttonClass.' text-white rounded-top' : '' }} text-center">
                <h4 class="mb-0">{{ $title ?? '' }}</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ $action }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" autocomplete="email" autofocus>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password"
                               class="form-control" autocomplete="current-password">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-{{ !empty($buttonClass) ? $buttonClass:'primary' }}">Login</button>
                    </div>
                </form>

                @if(!empty($registerRoute))
                    <div class="text-center mt-3">
                        <small>Don't have an account? <a href="{{ $registerRoute }}" class="text-decoration-none">Register here</a></small>
                    </div>
                @endif

                @if(!empty($forgotRoute))
                    <div class="text-center mt-3">
                        <a href="{{ $forgotRoute }}" class="text-decoration-none">Forgot Password?</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
