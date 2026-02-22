# Inventory Sync Service (Demo-Projekt)

Dieses Projekt simuliert eine typische Integrationsaufgabe zwischen:

- einem Warenwirtschaftssystem (ERP)
- einem Online-Shop mit REST-API

Ziel ist es, Produktdaten aus einer Datenbank zu lesen und mit einer Shop-API zu synchronisieren.

---

## Hintergrund

Das Projekt wurde als praktische Demonstration entwickelt, um zu zeigen:

- strukturiertes Arbeiten mit PHP
- saubere Trennung von Logik und Infrastruktur
- grundlegendes Verständnis von API-Design
- Umgang mit Datenbanken (MySQL)
- Dokumentation von Architekturentscheidungen (ADR)

Es handelt sich bewusst um eine kompakte, nachvollziehbare Lösung und nicht um ein überkomplexes Framework-Projekt.

---

## Architektur

Das System besteht aus:

- `InventoryRepository` (Datenbankzugriff)
- `ApiClient` (HTTP-Kommunikation)
- `SyncService` (Ablauf-Logik)
- `public/sync.php` (Entry Point)
- `public/api/products.php` (simulierte Shop-API)

Der Sync-Prozess:

1. Produkte aus der ERP-Datenbank lesen
2. Per JSON an die Shop-API senden
3. Insert oder Update anhand `external_id`
4. Statuscodes auswerten
5. Zusammenfassung zurückgeben

---

## Technische Aspekte

- PHP 8
- MySQL
- PDO mit Prepared Statements
- cURL für HTTP-Kommunikation
- Logging in Datei
- einfache Integrationstests
- ADR-Dokumentation im `docs/adr` Ordner

---

## Entwicklungsziel

Dieses Projekt dient als Demonstration für:

- Integrationsaufgaben zwischen bestehenden Systemen
- Ansprechpartner-Rolle zwischen ERP-Anbieter und Unternehmen
- saubere und nachvollziehbare Backend-Logik

Es wurde innerhalb weniger Tage als kompaktes Beispiel umgesetzt.