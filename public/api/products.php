<?php
// public/api/products.php

/**
 * products.php
 * ------------
 * Simulierter Shop-API Endpoint.
 *
 * Contract (Minimal):
 * POST /api/products.php
 * Pflichtfelder: external_id, name, price, stock
 *
 * Responses:
 * - 201 Created: neues Produkt
 * - 200 OK: bestehendes Produkt aktualisiert
 * - 400 Bad Request: ungültiges JSON oder fehlende Pflichtfelder
 * - 500 Internal Server Error: interner Fehler (z.B. DB)
 */

require_once __DIR__ . '/../../src/Repository/InventoryRepository.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed'], JSON_PRETTY_PRINT);
        exit;
    }

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON'], JSON_PRETTY_PRINT);
        exit;
    }

    // Minimal required fields
    $required = ['external_id', 'name', 'price', 'stock'];

    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            http_response_code(400);
            echo json_encode(['error' => "Missing field: {$field}"], JSON_PRETTY_PRINT);
            exit;
        }
    }

    // Minimal type checks (lightweight, but useful)
    if (!is_numeric($data['price'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid field: price must be numeric'], JSON_PRETTY_PRINT);
        exit;
    }

    if (!is_numeric($data['stock'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid field: stock must be numeric'], JSON_PRETTY_PRINT);
        exit;
    }

    $repository = new InventoryRepository();

    // Normalize payload for repository methods
    $product = [
        'external_id' => (string)$data['external_id'],
        'name' => (string)$data['name'],
        'sku' => $data['sku'] ?? null,
        'price' => (float)$data['price'],
        'stock' => (int)$data['stock'],
        'status' => $data['status'] ?? 'active',
        'external_updated_at' => $data['external_updated_at'] ?? null
    ];

    $existing = $repository->findByExternalId($product['external_id']);

    if ($existing !== null) {
        $repository->updateProduct($product);
        http_response_code(200);
        echo json_encode(['result' => 'updated'], JSON_PRETTY_PRINT);
        exit;
    }

    $repository->insertProduct($product);
    http_response_code(201);
    echo json_encode(['result' => 'created'], JSON_PRETTY_PRINT);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}