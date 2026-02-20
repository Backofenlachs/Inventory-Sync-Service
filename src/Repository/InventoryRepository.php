<?php
// InventoryRepository.php

/**
 * InveentoryRepository
 * --------------------
 * Dieses Repository kapselt den Zugriff auf die 'products'-Tabelle
 * in der ERP-Datenbank. Es stellt dgrundlegende CRUD-Methoden bereit:
 * 
 * - Alle Produkte abrufen
 * - Produkt nach external_id suchen
 * - Neues Produkt einfügen
 * - Bestehendes Prdukt aktualisieren
 * 
 * Zeck:
 * Wird vom SyncService genutzt, um ERP-Daten zu lesen und für den
 * Abgleich mit der Shop-API vorzubereiten.
 * 
 * Hinweis:
 * Die Verbindung zur Datenbank erfolgt über die zentrale Datenbase-Klasse.
 */


require_once __DIR__ . '/../../config/database.php';

class InventoryRepository
{
    private PDO $pdo;

    /**
     * Konstruktor
     * Baut die Datenbankverbindung über die zentrale Database-Klasse auf.
     */
    public function __construct()
    {
        $this->pdo = (new Database())->getConnection();
    }

    /**
     * Holt alle Produkte aus der Datenbank.
     *
     * @return array Liste aller Produkte als assoziatives Array
     */
    public function fetchAllProducts(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM products");
        return $stmt->fetchAll();
    }

    /**
     * Sucht ein Produkt anhand der external_id.
     *
     * @param string $externalId Eindeutige ID aus dem externen System
     * @return array|null Produktdaten oder null, wenn nicht gefunden
     */
    public function findByExternalId(string $externalId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM products WHERE external_id = :external_id"
        );

        $stmt->execute([':external_id' => $externalId]);
        $product = $stmt->fetch();

        return $product ?: null;
    }

    /**
     * Fügt ein neues Produkt in die Datenbank ein.
     *
     * @param array $product Produktdaten (external_id, name, price, stock, etc.)
     * @return int ID des neu erstellten Datensatzes
     */
    public function insertProduct(array $product): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO products 
            (external_id, name, sku, price, stock, status, external_updated_at)
            VALUES 
            (:external_id, :name, :sku, :price, :stock, :status, :external_updated_at)
        ");

        $stmt->execute([
            ':external_id' => $product['external_id'],
            ':name' => $product['name'],
            ':sku' => $product['sku'] ?? null,
            ':price' => $product['price'],
            ':stock' => $product['stock'],
            ':status' => $product['status'] ?? 'active',
            ':external_updated_at' => $product['external_updated_at'] ?? null
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Aktualisiert ein bestehendes Produkt anhand der external_id.
     *
     * @param array $product Aktualisierte Produktdaten
     * @return bool True bei Erfolg, sonst False
     */
    public function updateProduct(array $product): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE products SET
                name = :name,
                sku = :sku,
                price = :price,
                stock = :stock,
                status = :status,
                external_updated_at = :external_updated_at,
                updated_at = CURRENT_TIMESTAMP
            WHERE external_id = :external_id
        ");

        return $stmt->execute([
            ':external_id' => $product['external_id'],
            ':name' => $product['name'],
            ':sku' => $product['sku'] ?? null,
            ':price' => $product['price'],
            ':stock' => $product['stock'],
            ':status' => $product['status'] ?? 'active',
            ':external_updated_at' => $product['external_updated_at'] ?? null
        ]);
    }
}