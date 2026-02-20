<?php
require_once __DIR__ . '/../src/Repository/InventoryRepository.php';

try {
    $repository = new InventoryRepository();

    echo "Starte Repository Test\n";

    // 1. Testprodukt definieren
    $testProduct = [
        'external_id' => 'TEST-REPO-001',
        'name' => 'Repository Test Produkt',
        'sku' => 'REPO-001',
        'price' => 19.99,
        'stock' => 50,
        'status' => 'active',
        'external_updated_at' => date('Y-m-d H:i:s')
    ];

    // 2. Sicherstellen, dass es nicht bereits existiert
    $existing = $repository->findByExternalId($testProduct['external_id']);
    if ($existing !== null) {
        echo "Testprodukt existiert bereits und wird entfernt\n";
        $pdo = (new Database())->getConnection();
        $stmt = $pdo->prepare("DELETE FROM products WHERE external_id = :external_id");
        $stmt->execute([':external_id' => $testProduct['external_id']]);
    }

    // 3. Insert testen
    $insertId = $repository->insertProduct($testProduct);
    echo "Insert erfolgreich, neue ID: " . $insertId . "\n";

    // 4. Find testen
    $found = $repository->findByExternalId($testProduct['external_id']);
    if ($found === null) {
        throw new Exception("Fehler: Produkt wurde nach Insert nicht gefunden");
    }
    echo "Find erfolgreich\n";

    // 5. Update testen
    $found['name'] = 'Repository Test Produkt Updated';
    $found['stock'] = 75;

    $updateResult = $repository->updateProduct($found);
    if (!$updateResult) {
        throw new Exception("Fehler beim Update");
    }

    $updated = $repository->findByExternalId($testProduct['external_id']);
    if ($updated['stock'] != 75) {
        throw new Exception("Update wurde nicht korrekt gespeichert");
    }

    echo "Update erfolgreich\n";

    // 6. Fetch All testen
    $allProducts = $repository->fetchAllProducts();
    if (!is_array($allProducts)) {
        throw new Exception("FetchAll hat kein Array geliefert");
    }

    echo "FetchAll erfolgreich, Anzahl Produkte: " . count($allProducts) . "\n";

    // 7. Cleanup
    $pdo = (new Database())->getConnection();
    $stmt = $pdo->prepare("DELETE FROM products WHERE external_id = :external_id");
    $stmt->execute([':external_id' => $testProduct['external_id']]);

    echo "Cleanup erfolgreich\n";
    echo "Repository Test erfolgreich abgeschlossen\n";

} catch (Throwable $e) {
    echo "Test fehlgeschlagen: " . $e->getMessage() . "\n";
}