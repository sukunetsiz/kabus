<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-message">Oops! Page Not Found</div>
        <div class="error-description">
            The page you're looking for might have been removed, renamed, or is temporarily unavailable. Don't worry, even the best explorers get lost sometimes.
        </div>
        <a href="{{ url('/') }}" class="home-button">Return to Kabus Market</a>
    </div>
</body>
</html>