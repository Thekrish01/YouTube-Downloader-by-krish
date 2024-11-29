<?php
header("Content-Type: application/json");

// Function to fetch video information from the API
function fetchVideoInfo($url) {
    $apiUrl = "https://yt-cw.fabdl.com/youtube/get?url=" . urlencode($url) . "&mp3_task=2";
    $ch = curl_init();
    
    // Set CURL options
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        return json_decode($response, true);
    } else {
        return null;
    }
}

// Handle the GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['url']) || empty($_GET['url'])) {
        echo json_encode(["error" => "Video URL is required"]);
        http_response_code(400);
        exit;
    }

    $url = $_GET['url'];
    $videoInfo = fetchVideoInfo($url);

    if ($videoInfo) {
        // Map the fetched data to your desired structure
        $response = [
            "title" => $videoInfo['title'] ?? "Unknown Title",
            "duration" => $videoInfo['duration'] ?? 0,
            "thumbnail" => $videoInfo['thumbnail'] ?? "",
            "url" => $videoInfo['url'] ?? $url,
            "uploader" => $videoInfo['uploader'] ?? "Unknown Uploader"
        ];

        echo json_encode($response);
    } else {
        echo json_encode(["error" => "Failed to fetch video info"]);
        http_response_code(500);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
    http_response_code(405);
}
?>