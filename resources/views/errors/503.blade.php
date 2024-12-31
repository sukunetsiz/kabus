<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Service Unavailable</title>
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>
<body>
    <div class="error-container">
        <div class="error-code">503</div>
        <div class="error-message">Service Unavailable</div>
        <div class="error-description">
            Our site is currently under maintenance or experiencing high load. We'll be back shortly. Thank you for your patience.
        </div>
        <a href="{{ url('/') }}" class="home-button">Return to Kabus Market</a>
    </div>
</body>
</html>