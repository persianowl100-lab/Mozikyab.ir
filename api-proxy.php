<?php
// api-proxy.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // در صورت نیاز دامنه خاصی را محدود کنید
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// ===== کلید API خود را در اینجا وارد کنید =====
define('API_KEY', 'hkr6GZGIDCR28R3pr6T9HwBltb2RHq44pP9uJ9PPU07HDKGeQpTiJpj1MVB8rjyF');
// ============================================

define('BASE_URL', 'https://shazam-api.com/api');

$requestUri = $_SERVER['REQUEST_URI'];

// مسیر تشخیص (recognize)
if (strpos($requestUri, '/api-proxy.php/recognize') !== false) {
    $input = json_decode(file_get_contents('php://input'), true);
    $url = $input['url'] ?? '';
    
    if (!$url) {
        http_response_code(400);
        echo json_encode(['error' => 'URL is required']);
        exit;
    }
    
    $ch = curl_init(BASE_URL . '/recognize');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['url' => $url]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . API_KEY
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    http_response_code($httpCode);
    echo $response;
    exit;
}

// مسیر دریافت نتیجه (results)
if (preg_match('#/api-proxy.php/results/([a-f0-9-]+)#', $requestUri, $matches)) {
    $uuid = $matches[1];
    
    $ch = curl_init(BASE_URL . '/results/' . $uuid);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . API_KEY
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    http_response_code($httpCode);
    echo $response;
    exit;
}

// اگر مسیر نامعتبر بود
http_response_code(404);
echo json_encode(['error' => 'Not found']);
