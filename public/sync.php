<?php
// public/sync.php

/**
 * sync.php
 * --------
 * HTTP Entry Point zum manuellen Starten des Sync-Prozesses.
 *
 * Aufgaben:
 * - Initialisiert die benötigten Komponenten (Repository, ApiClient, SyncService)
 * - Führt den Sync aus
 * - Gibt eine JSON Summary zurück
 *
 * Hinweis:
 * Keine Business-Logik hier. Die steckt im SyncService.
 */

require_once __DIR__ . '/../src/Repository/InventoryRepository.php';
require_once __DIR__ . '/../src/Http/ApiClient.php';
require_once __DIR__ . '/../src/Service/SyncService.php';

header('Content-Type: application/json');

try {
    $repository = new InventoryRepository();

    // Base URL für die lokale Shop API (PHP Built-in Server)
    $apiClient = new ApiClient('http://127.0.0.1:8001');

    $syncService = new SyncService($repository, $apiClient);

    $summary = $syncService->run();

    // Optional: minimaler Log-Eintrag
    $logFile = __DIR__ . '/../logs/sync.log';
    $logLine = sprintf(
        "[%s] processed=%d created=%d updated=%d failed=%d\n",
        date('Y-m-d H:i:s'),
        $summary['processed'] ?? 0,
        $summary['created'] ?? 0,
        $summary['updated'] ?? 0,
        $summary['failed'] ?? 0
    );
    file_put_contents($logFile, $logLine, FILE_APPEND);

    http_response_code(200);
    echo json_encode($summary, JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'error' => 'Sync failed',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}