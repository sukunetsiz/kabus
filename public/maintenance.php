<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabus Market</title>
    <style>
        /* Existing styles */
        body {
            font-family: "Arial", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .maintenance-container {
            max-width: 600px;
            margin: 20px;
            padding: 40px;
            background-color: #1e1e1e;
            border-radius: 12px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid #3c3c3c;
            text-align: center;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .maintenance-icon {
            font-size: 64px;
            color: #bb86fc;
            margin-bottom: 20px;
        }

        .maintenance-title {
            color: #bb86fc;
            font-size: 32px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .maintenance-message {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #e0e0e0;
        }

        .maintenance-info {
            background-color: #2c2c2c;
            border-left: 4px solid #bb86fc;
            padding: 20px;
            text-align: left;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .maintenance-info strong {
            color: #bb86fc;
            display: block;
            margin-bottom: 10px;
        }

        .maintenance-status {
            display: inline-block;
            background-color: #2c2c2c;
            border: 2px solid #bb86fc;
            color: #bb86fc;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
        }

        /* Loading animation styles */
        .loading-animation {
            width: 50px;
            height: 50px;
            margin: 20px auto;
            border: 5px solid #bb86fc;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s infinite linear;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .maintenance-container {
                padding: 30px 20px;
                margin: 15px;
            }

            .maintenance-title {
                font-size: 24px;
            }

            .maintenance-message {
                font-size: 16px;
            }

            .loading-animation {
                width: 30px;
                height: 30px;
                border-width: 3px;
                margin: 15px auto;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">
            🔧
        </div>
        <h1 class="maintenance-title">Kabus Market Bakımda</h1>
        <p class="maintenance-message">
            Sitemizde planlanmış bir bakım çalışması. Ekibimiz, Kabus Market'i mümkün olan en kısa sürede çevrimiçi olacak hale getirmek için çalışıyor.
        </p>
        <div class="maintenance-info">
            <strong>Ne oluyor?</strong>
            Sistemlerimizi yükseltiyor ve temel bakım yaparak daha iyi performans ve güvenlik sağlıyoruz. Bu işlem genellikle birkaç saat sürüyor.
        </div>
        <div class="loading-animation"></div>
        <div class="maintenance-status">
            ⏰ Tahmini Süre: Belirsiz
        </div>
    </div>
</body>
</html>
