<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <title>{{ $appName ?? config('app.name', 'Go Clear') }} - @yield('title')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('photo/pickup.png') }}">
 <link rel="stylesheet" href="{{ asset('css/sidebar-brand.css') }}">
  <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
  
</head>
<body class="bg-gray-100">
  <main class="max-w-md mx-auto mt-20">
    @yield('content')
  </main>
</body>
</html>
