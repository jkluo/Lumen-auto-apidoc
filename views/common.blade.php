<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <link href='/apidoc/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
    <link href='/apidoc/css/style.css' rel='stylesheet' type='text/css'>
    <script src="/apidoc/js/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="/apidoc/js/bootstrap.min.js" type="text/javascript"></script>
    @yield('head')
</head>
<body>
@yield('content')
@yield('footer')
</body>
</html>