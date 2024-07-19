<?php
// Include the proxy configuration file from the parent directory
include '../proxy_config.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id)) {
    echo "Error: Please provide a valid user name as the parameter 'id'.";
    exit;
}

$url = "https://www.youtube.com/user/$id/live";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

if ($use_proxy) {
    // Set up SOCKS5 proxy with authentication using variables from the config file
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_userpwd);
}

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL error: " . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

$html = $response;

preg_match_all('/"hlsManifestUrl":"([^"]+\.m3u8)"/', $html, $matches);

$stream_url = $matches[1][0];

if (!empty($stream_url)) {
    header("Location: $stream_url", true, 302);
} else {
    echo "Error: HLS manifest URL not found.";
}
?>