<?php

require_once __DIR__ . '/../src/Http/ApiClient.php';

define('COLOR_GREEN', "\033[32m");
define('COLOR_RED', "\033[31m");
define('COLOR_RESET', "\033[0m");

function success(string $message): void
{
    echo COLOR_GREEN . $message . COLOR_RESET . PHP_EOL;
}

function fail(string $message): void
{
    echo COLOR_RED . $message . COLOR_RESET . PHP_EOL;
}

echo "Starte Shop API (products.php) Test" . PHP_EOL;

$client = new ApiClient('http://127.0.0.1:8001');

try {
    // Test 1: valid payload (expect 201 on first insert, 200 if already exists)
    $valid = [
        'external_id' => 'API-PRODUCTS-TEST-001',
        'name' => 'Products API Test',
        'sku' => 'SKU-TEST-001',
        'price' => 1.23,
        'stock' => 5
    ];

    $r1 = $client->postProduct($valid);

    if ($r1['error'] !== null) {
        throw new Exception("HTTP Fehler: " . $r1['error']);
    }

    if ($r1['status'] === 201 || $r1['status'] === 200) {
        success("Test 1 erfolgreich: valid payload (Status {$r1['status']})");
    } else {
        throw new Exception("Test 1 unerwarteter Status: {$r1['status']}. Body: {$r1['body']}");
    }

    // Test 2: invalid payload (missing external_id) -> expect 400
    $invalid = [
        'name' => 'Missing external_id',
        'price' => 1.23,
        'stock' => 5
    ];

    $r2 = $client->postProduct($invalid);

    if ($r2['error'] !== null) {
        throw new Exception("HTTP Fehler (Test 2): " . $r2['error']);
    }

    if ($r2['status'] === 400) {
        success("Test 2 erfolgreich: invalid payload liefert 400");
    } else {
        throw new Exception("Test 2 unerwarteter Status: {$r2['status']}. Body: {$r2['body']}");
    }

    // Test 3: update case (same product again) -> expect 200
    $valid['stock'] = 6;
    $r3 = $client->postProduct($valid);

    if ($r3['error'] !== null) {
        throw new Exception("HTTP Fehler (Test 3): " . $r3['error']);
    }

    if ($r3['status'] === 200) {
        success("Test 3 erfolgreich: update case liefert 200");
    } else {
        throw new Exception("Test 3 unerwarteter Status: {$r3['status']}. Body: {$r3['body']}");
    }

    success("Products API Test abgeschlossen");

} catch (Throwable $e) {
    fail("Test fehlgeschlagen: " . $e->getMessage());
    exit(1);
}