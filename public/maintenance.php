<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/kabus.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Kabus</title>
<style>
body {
font-family:"Arial",sans-serif;
margin:0;
padding:0;
background-color:#121212;
color:#e0e0e0;
display:flex;
justify-content:center;
align-items:center;
min-height:100vh
}

.maintenance-container {
max-width:600px;
margin:20px;
padding:40px;
background-color:#1e1e1e;
border-radius:12px;
box-shadow:0 6px 25px #0000004d;
border:1px solid #3c3c3c;
text-align:center;
animation:fadeIn .6s ease-out
}

@keyframes fadeIn {
from {
opacity:0;
transform:translateY(20px)
}

to {
opacity:1;
transform:translateY(0)
}
}

.maintenance-icon {
font-size:64px;
color:#bb86fc;
margin-bottom:20px
}

.maintenance-title {
color:#bb86fc;
font-size:32px;
margin-bottom:20px;
font-weight:700
}

.maintenance-message {
font-size:18px;
line-height:1.6;
margin-bottom:30px;
color:#e0e0e0
}

.maintenance-info {
background-color:#292929;
border-left:4px solid #bb86fc;
border-right:4px solid #bb86fc;
padding:20px;
text-align:left;
margin:20px 0;
border-radius:8px
}

.maintenance-info strong {
color:#bb86fc;
display:block;
text-align:center;
margin-bottom:10px
}

.maintenance-status {
display:inline-block;
background-color:#292929;
border:2px solid #bb86fc;
color:#bb86fc;
padding:12px 24px;
border-radius:25px;
font-size:16px;
font-weight:700;
text-transform:uppercase;
letter-spacing:1px;
margin-top:20px
}

.loading-animation {
width:50px;
height:50px;
margin:20px auto;
border:5px solid #bb86fc;
border-radius:50%;
border-top-color:transparent;
animation:spin 1s infinite linear
}

@keyframes spin {
0% {
transform:rotate(0deg)
}

100% {
transform:rotate(360deg)
}
}   
</style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">
            🔧
        </div>
        <h1 class="maintenance-title">Kabus Under Maintenance</h1>
        <p class="maintenance-message">
            We are currently performing scheduled maintenance. Our team is working to bring Kabus back online as soon as possible.
        </p>
        <div class="maintenance-info">
            <strong>What's happening?</strong>
            We are upgrading our systems and performing essential maintenance to ensure better performance and security. This process typically takes a few hours. Please be patient and stay calm. We are safe.
        </div>
        <div class="loading-animation"></div>
        <div class="maintenance-status">
            ⏰ Estimated Time: Undetermined
        </div>
    </div>
</body>
</html>
