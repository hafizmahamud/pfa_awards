<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
    @if (env('APP_ENV') == 'development')
        <script type="text/javascript" src="https://www.bugherd.com/sidebarv2.js?apikey=gllkkobxpkkwgj3jp9gvuw" async="true">
        </script>
    @endif

    <!-- Scripts -->
    @routes
    @vite('resources/js/app.js')
    @inertiaHead
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9M0JKWLBRQ"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-9M0JKWLBRQ');
    </script>
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
