<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="bg-light">

<header class="p-3 mb-3 border-bottom shadow-sm">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none h4">
{{--                Admin Panel--}}
                @yield('panelName', 'Admin Panel')
            </a>

            <div class="ms-auto">
                {{-- Note: The 'admin.register' route needs to be defined in your web.php file --}}
                <a href="{{ route('admin.login') }}" class="btn btn-outline-primary me-2">Login</a>
                <a href="{{ route('admin.register') }}" class="btn btn-primary">Register</a>
                <a href="{{ route('user.login') }}" class="btn btn-outline-warning">Client-Side</a>
            </div>
        </div>
    </div>
</header>

<div class="content text-center">
    <div class="container">
        @yield('adminAuth')
    </div>
</div>
@stack('scripts')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
</body>
</html>
