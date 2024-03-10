<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Document' }}</title>
</head>
<body>
    <a>Testing</a>
    <header>
        <!-- Your header content goes here -->
    </header>

    <main>
        <!-- Your dynamic content goes here -->
        @yield('content')
    </main>

    <footer>
        <!-- Your footer content goes here -->
    </footer>
</body>
</html>
