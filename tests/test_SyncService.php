<?php

require_once __DIR__ . '/../src/Repository/InventoryRepository.php';
require_once __DIR__ . '/../src/Http/ApiClient.php';
require_once __DIR__ . '/../src/Service/SyncService.php';

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

echo "Starte SyncService Test" . PHP_EOL;

try {
    $repository = new InventoryRepository();
    $apiClient = new ApiClient("http://localhost:8000");

    $syncService = new SyncService($repository, $apiClient);

    $summary = $syncService->run();

    if (!is_array($summary)) {
        throw new Exception("Summary ist kein Array");
    }

    if (!isset($summary['processed'])) {
        throw new Exception("Summary enthält kein 'processed'");
    }

    success("SyncService ausgeführt");

    echo "Summary:" . PHP_EOL;
    print_r($summary);

    if ($summary['processed'] > 0) {
        success("Mindestens ein Produkt verarbeitet");
    } else {
        fail("Keine Produkte verarbeitet");
    }

    success("SyncService Test abgeschlossen");

} catch (Throwable $e) {
    fail("Test fehlgeschlagen: " . $e->getMessage());
    exit(1);
}