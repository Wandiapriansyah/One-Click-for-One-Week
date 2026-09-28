<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One Click for One Week</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <div id="app">
        <div class="main-wrapper">
            @include('shared.header')
            <div class="main-content">
                @yield('content')
            </div>
            @include('shared.footer')
        </div>
    </div>
</body>
</html>