<?php
function normalizeSpotifyUrl($url) {
    // Remove country code and query parameters
    $url = preg_replace('/\/intl-[a-z]{2}\//', '/', $url);
    $url = preg_replace('/\?.*$/', '', $url);
    return $url;
}

function getCoverArt($spotifyUrl) {
    // Normalize the URL
    $normalizedUrl = normalizeSpotifyUrl($spotifyUrl);

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $normalizedUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Execute cURL session and get the HTML content
    $html = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Regular expression pattern to match the cover art URL
    $pattern = '/<meta property="og:image" content="(https:\/\/i\.scdn\.co\/image\/[a-zA-Z0-9]+)"/';

    // Perform the regex match
    if (preg_match($pattern, $html, $matches)) {
        return $matches[1];
    }

    return false;
}

$coverArtUrl = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $spotifyUrl = $_POST['spotify_url'] ?? '';
    
    if (!empty($spotifyUrl)) {
        $coverArtUrl = getCoverArt($spotifyUrl);
        
        if (!$coverArtUrl) {
            $error = "Unable to fetch cover art. Please check the URL and try again.";
        }
    } else {
        $error = "Please enter a Spotify URL.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify Cover Art Fetcher</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            overflow: hidden;
            transition: background-color 0.3s;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
            z-index: 1;
        }

        h1 {
            color: #1DB954;
            margin-bottom: 20px;
            animation: fadeIn 2s ease-in;
        }

        form {
            margin-bottom: 20px;
            animation: fadeInUp 1s ease-in-out;
        }

        input[type="text"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #1DB954;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #14833b;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            animation: fadeIn 1s ease-in;
        }

        img {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
            border-radius: 10px;
            animation: fadeIn 1s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Floating Shapes Background */
        .floating-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .floating-bg div {
            position: absolute;
            background-color: rgba(29, 185, 84, 0.3);
            border-radius: 50%;
            animation: float 10s infinite ease-in-out;
            z-index: 0;
        }

        .floating-bg div:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 20%;
        }

        .floating-bg div:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 70%;
            left: 60%;
        }

        .floating-bg div:nth-child(3) {
            width: 100px;
            height: 100px;
            top: 30%;
            left: 80%;
        }

        .floating-bg div:nth-child(4) {
            width: 150px;
            height: 150px;
            top: 40%;
            left: 30%;
        }

        .floating-bg div:nth-child(5) {
            width: 90px;
            height: 90px;
            top: 80%;
            left: 10%;
        }

        @keyframes float {
            0% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-20px) translateX(20px); }
            100% { transform: translateY(0) translateX(0); }
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background-color: #121212;
        }

        body.dark-mode .container {
            background-color: #1e1e1e;
            color: #ddd;
        }

        body.dark-mode h1 {
            color: #1DB954;
        }

        body.dark-mode input[type="text"] {
            border: 1px solid #333;
            background-color: #333;
            color: #ddd;
        }

        body.dark-mode input[type="submit"] {
            background-color: #1DB954;
            color: #fff;
        }

        /* Dark Mode Toggle */
        .mode-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            cursor: pointer;
            z-index: 2;
            animation: fadeIn 2s ease-in-out;
        }

        .mode-toggle span {
            display: inline-block;
            font-size: 20px;
            padding: 10px 15px;
            border-radius: 50px;
            background-color: #1DB954;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
        }

        body.dark-mode .mode-toggle span {
            background-color: #333;
            color: #f0f0f0;
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="floating-bg">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div class="mode-toggle" onclick="toggleMode()">
        <span>🌙</span>
    </div>

    <div class="container">
        <h1>Spotify Cover Art Fetcher</h1>
        <form method="post">
            <input type="text" name="spotify_url" placeholder="Enter Spotify URL" required>
            <input type="submit" value="Fetch Cover Art">
        </form>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (!empty($coverArtUrl)): ?>
            <h2>Cover Art:</h2>
            <img src="<?php echo htmlspecialchars($coverArtUrl); ?>" alt="Spotify Cover Art">
        <?php endif; ?>
    </div>

    <script>
        function toggleMode() {
            document.body.classList.toggle('dark-mode');
            let toggleText = document.querySelector('.mode-toggle span');
            toggleText.textContent = document.body.classList.contains('dark-mode') ? '☀️' : '🌙';
        }
    </script>
</body>
</html>
