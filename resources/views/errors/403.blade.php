<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden</title>
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-message">Access Forbidden</div>
        <div class="error-description">
            Sorry, you don't have permission to access this page. If you think this is an error, please contact the site administrator.
        </div>
        <a href="{{ url('/') }}" class="home-button">Return to Kabus Market</a>
    </div>
</body>
</html>