<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$base_url = 'https://meloloapi-pearl.vercel.app';
$action = $_GET['action'] ?? '';

function makeRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

switch ($action) {
    case 'search':
        $query = $_GET['query'] ?? '';
        $offset = $_GET['offset'] ?? 0;
        $limit = $_GET['limit'] ?? 10;
        $url = "{$base_url}/api/search?query=" . urlencode($query) . "&offset={$offset}&limit={$limit}";
        $data = makeRequest($url);
        echo json_encode($data);
        break;
    
    case 'details':
        $series_id = $_GET['series_id'] ?? '';
        $url = "{$base_url}/api/video-details?series_id=" . urlencode($series_id);
        $data = makeRequest($url);
        echo json_encode($data);
        break;
    
    case 'model':
        $video_id = $_GET['video_id'] ?? '';
        $url = "{$base_url}/api/video-model?video_id=" . urlencode($video_id);
        $data = makeRequest($url);
        echo json_encode($data);
        break;
    
    case 'recommend':
        $user_id = $_GET['user_id'] ?? '1';
        $limit = $_GET['limit'] ?? 10;
        $url = "{$base_url}/api/recommend?user_id={$user_id}&limit={$limit}";
        $data = makeRequest($url);
        echo json_encode($data);
        break;
    
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>
