<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/png" href="{{ asset('images/kabus.png') }}">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>
<body id="top" class="dark-mode">
    @include('components.navbar')
    <div class="content-wrapper">
        @auth
            @include('components.left-bar')
        @endauth
        <main class="main-content">
            @include('components.alerts')
            @yield('content')
        </main>
        @auth
            @include('components.right-bar')
        @endauth
    </div>
    
    @include('components.footer')
    
    <a href="#top" class="scroll-button scroll-top" title="Scroll to top">▲</a>
    <a href="#bottom" class="scroll-button scroll-bottom" title="Scroll to bottom">▼</a>
    <div id="bottom"></div>
</body>
</html>
