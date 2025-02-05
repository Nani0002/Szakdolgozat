<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Laravel App')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <header>
        <h1>@yield('header1')</h1>

        @include('layouts.navbar', ['menuItems' => $menuItems ?? []])
    </header>

    <main>
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer>
        footer
        <script src="{{ asset('js/app.js') }}"></script>
    </footer>
</body>

</html>
