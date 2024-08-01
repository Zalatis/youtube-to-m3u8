<?php
// Include the proxy configuration file from the parent directory
include '../proxy_config.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id)) {
    echo "Error: Please provide a valid channel ID as the parameter 'id'.";
    exit;
}

$url = "https://www.youtube.com/channel/$id/live";

function fetch_url($url, $use_proxy, $proxy = '', $proxy_userpwd = '') {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if ($use_proxy) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_userpwd);
    }

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    return $response;
}

$response = false;

if (isset($use_proxy) && $use_proxy) {
    // Try with proxy first if $use_proxy is set
    $response = fetch_url($url, true, $proxy, $proxy_userpwd);

    // If it fails and fallback is set to true, try without proxy
    if ($response === false && isset($fallback) && $fallback) {
        $response = fetch_url($url, false);
    }
} else {
    // If $use_proxy is false, try without proxy
    $response = fetch_url($url, false);
}

if ($response === false) {
    echo "Error: Unable to fetch the URL.";
    exit;
}

$html = $response;

preg_match_all('/"hlsManifestUrl":"([^"]+\.m3u8)"/', $html, $matches);

$stream_url = isset($matches[1][0]) ? $matches[1][0] : '';

if (!empty($stream_url)) {
    header("Location: $stream_url", true, 302);
} else {
    echo "Error: HLS manifest URL not found.";
}
?>
