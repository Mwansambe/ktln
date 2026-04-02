<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My App - @yield('title', 'Default Title')</title>
    <link rel="stylesheet" href="/css/app.css">
    @stack('styles')
     <link href="{{ asset('style.css') }}" rel="stylesheet">
</head>
<body>
    <!-- this is the main layout file that all other views will extend. It includes the header and footer, and defines a section for content that child views can fill in. -->
    @include('includes.header')

    <main class="container">
        @yield('content')
    </main>

    @include('includes.footer')

    @stack('scripts')
</body>
</html>