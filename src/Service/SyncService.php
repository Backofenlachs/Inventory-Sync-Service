<?php
// src/Service/SyncService.php

/**
 * SyncService
 * -----------
 * Orchestriert den Sync-Prozess:
 * - liest Produkte aus der ERP-Datenbank (InventoryRepository)
 * - sendet jedes Produkt an die Shop-API (ApiClient)
 * - wertet Statuscodes aus und erstellt eine Summary
 *
 * Hinweis:
 * Dieser Service enthält die Ablauf-/Use-Case-Logik, aber keine HTTP-Details
 * (die stecken im ApiClient) und keine SQL-Details (die stecken im Repository).
 */

require_once __DIR__ . '/../Repository/InventoryRepository.php';
require_once __DIR__ . '/../Http/ApiClient.php';

class SyncService
{
    private InventoryRepository $repository;
    private ApiClient $apiClient;

    public function __construct(InventoryRepository $repository, ApiClient $apiClient)
    {
        $this->repository = $repository;
        $this->apiClient = $apiClient;
    }

    /**
     * Führt den vollständigen Sync-Lauf aus.
     *
     * @return array Summary mit Zählwerten und optionalen Fehlermeldungen
     */
    public function run(): array
    {
        $products = $this->repository->fetchAllProducts();

        $summary = [
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => [] // optional: sammelt Fehlermeldungen für Debugging
        ];

        foreach ($products as $product) {
            $summary['processed']++;

            // Produktdaten so weitergeben, wie sie die API erwartet.
            // Minimal: external_id, name, price, stock (sku optional)
            $payload = [
                'external_id' => $product['external_id'],
                'name' => $product['name'],
                'sku' => $product['sku'] ?? null,
                'price' => (float)$product['price'],
                'stock' => (int)$product['stock'],
                'status' => $product['status'] ?? 'active',
                'external_updated_at' => $product['external_updated_at'] ?? null
            ];

            $result = $this->apiClient->postProduct($payload);

            // Transport-/Encoding-Fehler im ApiClient
            if ($result['error'] !== null || $result['status'] === 0) {
                $summary['failed']++;
                $summary['errors'][] = [
                    'external_id' => $payload['external_id'],
                    'status' => $result['status'],
                    'error' => $result['error']
                ];
                continue;
            }

            // Statuscode-Auswertung wie im Diagramm (200 / 201)
            if ($result['status'] === 201) {
                $summary['created']++;
            } elseif ($result['status'] === 200) {
                $summary['updated']++;
            } else {
                $summary['failed']++;
                $summary['errors'][] = [
                    'external_id' => $payload['external_id'],
                    'status' => $result['status'],
                    'error' => 'Unexpected status code',
                    'body' => $result['body']
                ];
            }
        }

        return $summary;
    }
}