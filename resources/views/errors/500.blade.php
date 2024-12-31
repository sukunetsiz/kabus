<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Internal Server Error</title>
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <div class="error-message">Internal Server Error</div>
        <div class="error-description">
            Oops! Something went wrong on our end. We're working to fix the problem. Please try again later or contact our support team if the issue persists.
        </div>
        <a href="{{ url('/') }}" class="home-button">Return to Kabus Market</a>
    </div>
</body>
</html>