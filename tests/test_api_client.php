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

echo "Starte ApiClient Test" . PHP_EOL;

$client = new ApiClient("http://localhost:8000");

try {
    // ---------- Test 1: gültiges Produkt ----------
    $validProduct = [
        'external_id' => 'API-TEST-001',
        'name' => 'ApiClient Test Product',
        'sku' => 'SKU-001',
        'price' => 9.99,
        'stock' => 10,
    ];

    $result = $client->postProduct($validProduct);

    if ($result['error'] !== null) {
        throw new Exception("Fehler im HTTP Request: " . $result['error']);
    }

    if ($result['status'] === 201 || $result['status'] === 200) {
        success("Test 1 erfolgreich: gültiges Produkt gesendet (Status {$result['status']})");
    } else {
        throw new Exception("Unerwarteter Status Code: " . $result['status']);
    }

    // ---------- Test 2: ungültiges Produkt ----------
    $invalidProduct = [
        'name' => 'Missing external_id'
    ];

    $result2 = $client->postProduct($invalidProduct);

    if ($result2['error'] !== null) {
        throw new Exception("Fehler im HTTP Request (Test 2): " . $result2['error']);
    }

    if ($result2['status'] === 400) {
        success("Test 2 erfolgreich: ungültiges Produkt korrekt mit 400 beantwortet");
    } else {
        fail("Test 2 Warnung: Erwartet 400, erhalten {$result2['status']}");
    }

    success("ApiClient Test abgeschlossen");

} catch (Throwable $e) {
    fail("Test fehlgeschlagen: " . $e->getMessage());
    exit(1);
}