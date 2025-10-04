<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'main')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
<div id="app" class="wrapper">
    @include('user.partials.navbar')

    <div class="content-wrapper">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @include('user.partials.cart')
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
<script>
    console.log('{{session('api_token')}}');
    function cardCount(){
        $.ajax({
            url: 'http://localhost/laravel/admin-panel/public/api/user/show/cart',
            type: 'POST',
            headers: {
                'Authorization': 'Bearer {{ session('api_token') }}'
            },
            data: {
                api_token: '{{session('api_token')}}'
            },
            success: function (response) {
                if (response && response.count !== undefined) {
                    $('#cartCount').text(response.count);
                } else {
                    $('#cartCount').text(0);
                }
            },
            error: function (xhr) {
                console.log(xhr);
            }
        });
    }
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
