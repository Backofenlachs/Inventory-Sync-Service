<?php
// test_database.php
require_once __DIR__ . '/../config/database.php';

try {
    // 1️⃣ Verbindung aufbauen
    $db = (new Database())->getConnection();
    echo "✅ Verbindung zur Datenbank erfolgreich!\n";

    // 2️⃣ Prüfen ob Tabelle 'products' existiert
    $stmt = $db->query("SHOW TABLES LIKE 'products'");
    if ($stmt->rowCount() === 0) {
        echo "⚠ Tabelle 'products' existiert noch nicht!\n";
    } else {
        echo "✅ Tabelle 'products' existiert.\n";
    }

    // 3️⃣ Testprodukt einfügen
    $insert = $db->prepare("
        INSERT INTO products (external_id, name, sku, price, stock)
        VALUES (:external_id, :name, :sku, :price, :stock)
    ");

    $insert->execute([
        ':external_id' => 'TEST-001',
        ':name' => 'Test Produkt',
        ':sku' => 'TP-001',
        ':price' => 9.99,
        ':stock' => 100
    ]);

    echo "✅ Testprodukt eingefügt.\n";

    // 4️⃣ Testprodukt abrufen
    $stmt = $db->prepare("SELECT * FROM products WHERE external_id = :external_id");
    $stmt->execute([':external_id' => 'TEST-001']);
    $product = $stmt->fetch();

    if ($product) {
        echo "✅ Testprodukt gefunden:\n";
        print_r($product);
    } else {
        echo "❌ Testprodukt konnte nicht gefunden werden.\n";
    }

    // 5️⃣ Optional: Testprodukt wieder löschen
    $db->prepare("DELETE FROM products WHERE external_id = :external_id")
       ->execute([':external_id' => 'TEST-001']);
    echo "✅ Testprodukt wieder gelöscht.\n";

} catch (Exception $e) {
    echo "❌ Fehler: " . $e->getMessage() . "\n";
}