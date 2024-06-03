<?php
$id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id)) {
    echo "Error: Please provide a valid handle name as the parameter 'id'.";
    exit;
}

$url = "https://www.youtube.com/${id}/live";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$html = $response;

preg_match_all('/"hlsManifestUrl":"([^"]+\.m3u8)"/', $html, $matches);

$stream_url = $matches[1][0];

if (!empty($stream_url)) {
    // echo "$stream_url";
    header("Location: $stream_url", true, 302);
} else {
    echo "Error: HLS manifest URL not found.";
}
?>