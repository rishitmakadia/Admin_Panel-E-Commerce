<!DOCTYPE html>
<html>
<head>
    <title>Let's get you back in Platform</title>
</head>
<body>
<h1>Nice to see you again, {{ $data['name'] }}!</h1>
<p>Enter this otp in the portal</p>
<p>OTP: {{ $data['otp'] }}</p>
<p>Enjoy your experience with us!</p>
<a href="{{route('user.forgot')}}"><button>Forgot Password</button></a>
</body>
</html>

