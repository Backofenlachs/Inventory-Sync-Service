# Mini Inventory Sync Service (DB → REST)

## Projektziel

Dieses Projekt simuliert eine typische Integrationsaufgabe zwischen:

- einem Warenwirtschaftssystem (ERP)
- einem Online-Shop mit REST-API

Ziel ist die Entwicklung eines Sync-Services, der:

- Produktdaten aus einer ERP-Datenbank liest
- diese als JSON an eine Shop-API sendet
- neue Produkte anlegt oder bestehende aktualisiert
- Ergebnisse protokolliert
- Fehlerfälle sauber behandelt

**Umsetzungszeit:** 3 Tage  
**Status:** Funktionsfähiger Prototyp  
**Version:** 1.0.0

---

## Motivation & Kontext

Viele Unternehmen nutzen:

- Ein externes ERP-System
- Einen separaten Online-Shop

Typische Herausforderungen:

- Lagerbestand synchronisieren
- Preisänderungen übernehmen
- Neue Produkte automatisch anlegen
- API-Fehler korrekt behandeln
- Datenkonsistenz sicherstellen

Dieses Projekt simuliert eine solche Integrationsaufgabe in kompakter Form.

Es dient als Demonstration für:

- strukturiertes Backend-Design
- Trennung von Logik und Infrastruktur
- API-Contract-orientierte Entwicklung
- Nachvollziehbare Dokumentation von Architekturentscheidungen (ADR)

---

## Architekturüberblick

### Komponenten

- ERP-Datenbank (MySQL)
- SyncService (Business-Logik)
- ApiClient (HTTP-Kommunikation)
- Simulierte Shop-API (`public/api/products.php`)
- Logging in Datei (`logs/sync.log`)

### Systemfluss
```text
ERP DB --> Sync Service --> ApiClient --> Shop API --> Shop DB
```

Ein Sequenzdiagramm befindet sich unter:
```text
docs/erp-shop-sync-sequence.puml
```

---

## Architekturprinzipien

- Trennung von Business-Logik (SyncService) und Infrastruktur (Repository, ApiClient)
- Repository Pattern für Datenbankzugriff
- Klar definierter API Contract (Request/Response)
- Minimale, nachvollziehbare Implementierung ohne Framework-Abhängigkeiten
- Dokumentation technischer Entscheidungen mittels ADR

---

## Projektstruktur

```text
inventory-sync/
├── config/
│   └── database.php              # Datenbankverbindung & Konfiguration
│
├── src/
│   ├── Repository/
│   │   └── InventoryRepository.php  # Zugriff auf die Inventory-Datenbank (CRUD)
│   │
│   ├── Service/
│   │   └── SyncService.php          # Business-Logik für den API-Sync
│   │
│   └── Http/
│       └── ApiClient.php            # Kommunikation mit externer API
│
├── public/
│   ├── sync.php                 # Startpunkt für manuellen Sync
│   │
│   └── api/
│       └── products.php         # REST-Endpoint für Produktdaten
│
├── logs/
│   └── sync.log                 # Log-Datei für Synchronisationsprozesse
│
├── schema.sql                   # Datenbankschema
└── README.md                    # Projektdokumentation
```

---

## Datenbankschema

### Tabelle: `products` (ERP-Quelle)

- id (BIGINT, Primary Key)
- external_id (VARCHAR, Unique)
- name (VARCHAR)
- sku (VARCHAR, optional)
- price (DECIMAL)
- stock (INT)
- status (VARCHAR, default: active)
- external_updated_at (DATETIME)
- created_at (DATETIME)
- updated_at (DATETIME)

`external_id` dient als eindeutiger Identifikator zwischen ERP und Shop.

---

## REST-API (Simulierter Shop)

### Endpoint
```text
POST /api/products.php
```

### Pflichtfelder

- external_id (string)
- name (string)
- price (number)
- stock (int)

### Optional

- sku
- status
- external_updated_at

### Beispiel-Request

```json
{
  "external_id": "PLANT-001",
  "name": "Lavandula angustifolia",
  "sku": "SKU-PLANT-001",
  "price": 8.90,
  "stock": 25
}
```

### Response Codes
- **201 Created** - Produkt neu angelegt
- **200 OK** - Produkt existiert bereits und wurde aktualisiert
- **400 Bad Request** - JSON ungültig oder Pflichtfelder fehlen
- **500 Internal Server Error** - Interner Fehler

---

## Sync-Logic (High-Level)

Für jedes ERP-Produkt:
1. JSON-Payload erzeugen
2. POST-Request via ApiClient senden
3. Statuscode auswerten
4. Statistik aktualisieren (created / updated / failed)
5. Ergebnis in Log-Datei schreiben

Beispiel Summary:
```json
{
  "processed": 2,
  "created": 1,
  "updated": 1,
  "failed": 0
}
```

---

## Setup (Ubuntu)

### Voraussetungen

- MySQLServer oder MariaDB
- PHP 8+
- php-mysql
- php-curl

Installation:
```bash
sudo apt update 
sudo apt install -y mysql-server php php-mysql php-curl
```

### Datenbank einrichten
```bash
chmod +x setup/setup_database.sh
./setup/setup_database.sh
```

---

## Lokale Entwicklung

Aufgrund der Single-Thread-Eigenschaft des PHP Build-in Servers werden zwei Server gestartet:
- Port 8000 --> Sync Entry Point
- Port 8001 --> Shop API

Start:
```bash
chmod +x bin/start_servers.sh
./bin/start_servers.sh
```

Sync ausführen:
```text
http://127.0.0.1:8000/sync.php
```

---

## Dokumentation

Architekturentscheidungen befinden sich unter:
```text
docs/adr/
```

- ADR 001: Dualer PHP Built-in Server für lokale Entwicklung

---

## Umsetung (3 Tage)
**Tag 1**
- Datenbankshema definieren
- Repository implementiert
- ApiClient implementiert
- Shop API erstellt

**Tag 2**
- SyncService implementiert
- Statuscode-Habdling ergänzt
- Logging integriert
- End-to-End-Tests durchgeführt

**Tag 3**
- Integrationstests ergänzt
- Dual-Server-Setup implementiert
- Architekturentscheidung (ADR dokumentiert)
- README überarbeited und konsolidiert



