@extends('welcome')

@section('panelName', 'Reset Password')

@section('adminAuth')
    <div class="container d-flex justify-content-center align-items-center"
         style="min-height: 100vh; background-color: #f8f9fa;">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow rounded-3">
                <div class="card-header bg-warning text-white text-center rounded-top">
                    <h4 class="mb-0">Reset Password</h4>
                </div>
                <div class="card-body p-4">
                    <form id="reset-password-form">
                        @csrf
                        <div id="emailSection">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" autocomplete="email" autofocus>
                                <small id="email_error" class="text-danger"></small>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-secondary" id="getOtp">Get Code</button>
                            </div>
                        </div>
                        <div id="otpSection" style="display: none;">
                            <div class="mb-3">
                                <label for="otp" class="form-label">Enter OTP</label>
                                <div class="input-group">
                                    <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter OTP">
                                    <button type="button" class="btn btn-outline-primary" id="verifyOtp">Verify</button>
                                </div>
                                <small id="otp_error" class="text-danger"></small>
                            </div>
                        </div>
                        <div id="pwdSection" style="display: none;">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                                <small id="password_error" class="text-danger"></small>
                            </div>
                            <div class="d-grid">
                                <button type="button" class="btn btn-warning" id="savePwd">Reset Password</button>
                            </div>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <small>Back to Login?
                            <a href="{{ route('user.login') }}" class="text-decoration-none">Login</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        $(document).ready(function () {
            function clearErrors() {
                $('#email_error').text('');
                $('#otp_error').text('');
                $('#password_error').text('');
            }

            $('#getOtp').on('click', function (e) {
                e.preventDefault();
                clearErrors();
                const email = $('#email').val();
                const button = $(this);
                button.prop('disabled', true).text('Sending...');

                $.ajax({
                    url: "{{ route('user.forgot') }}",
                    method: 'POST',
                    data: {
                        email: email,
                        action: 'send_otp',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        $('#emailSection').find('button').hide();
                        $('#email').prop('disabled', true);
                        $('#otpSection').show();
                    },
                    error: function (xhr) {
                        $('#email_error').text('Enter Correct E-mail');
                        button.prop('disabled', false).text('Get Code');
                    }
                });
            });

            $('#verifyOtp').on('click', function (e) {
                e.preventDefault();
                clearErrors();
                const otp = $('#otp').val();
                const button = $(this);
                button.prop('disabled', true).text('Verifying...');

                $.ajax({
                    url: "{{ route('user.forgot') }}",
                    method: 'POST',
                    data: {
                        otp: otp,
                        action: 'verify_otp',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        // $('#otpSection').hide();
                        $('#otpSection').find('button').text('Verified');
                        $('#pwdSection').show();
                    },
                    error: function (response) {
                        $('#otp_error').text('Invalid OTP');
                        button.prop('disabled', false).text('Verify');
                    }
                });
            });

            $('#savePwd').on('click', function (e) {
                e.preventDefault();
                clearErrors();
                const button = $(this);
                button.prop('disabled', true).text('Saving...');
                $.ajax({
                    url: "{{ route('user.forgot') }}",
                    method: 'POST',
                    data: {
                        password: $('#password').val(),
                        action: 'reset_password',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        window.location.href = response.redirect_url;
                    },
                    error: function (xhr) {
                        let errorMsg = 'Password must be 6 character';
                        if (xhr.status === 422 && xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            }
                        }
                        $('#password_error').text(errorMsg);
                        button.prop('disabled', false).text('Reset Password');
                    }
                });
            });
        });
    </script>
@endpush
