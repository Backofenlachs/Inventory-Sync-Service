# Mini Inventory Sync Service (DB → REST)

## Projektziel

Dieses Projekt simuliert eine typische Integrationsaufgabe:

- Ein Warenwirtschaftssystem (ERP) speichert Produktdaten in einer Datenbank.
- Ein E-Shop verwaltet seine Produkte über eine REST-API.

Ziel ist es, einen Sync-Service zu entwickeln, der:

- Produktdaten aus einer ERP-Datenbank liest
- diese als JSON an eine Shop-API sendet
- neue Produkte anlegt oder bestehende aktualisiert
- Änderungen protokolliert
- Fehlerbehandlung implementiert

**Zeitbudget:** 2 Tage

---

## Motivation & Hintergrund

Viele Unternehmen nutzen:

- Ein externes Warenwirtschaftssystem
- Einen separaten Online-Shop

Typische Herausforderungen:

- Lagerbestand synchronisieren
- Preisänderungen übernehmen
- Neue Produkte anlegen
- Datenkonsistenz sicherstellen
- API-Fehler korrekt behandeln

Dieses Projekt simuliert diese Integration in vereinfachter Form.

Es dient gleichzeitig als:

- Demonstration meines technischen Verständnisses für Systemintegration
- Nachweis meiner Fähigkeit zur schnellen Einarbeitung in neue Projektkontexte
- Beispiel für saubere Backend-Architektur in reinem PHP
- Realistische 2-Tage-Implementierung

---

## Architekturüberblick

### Komponenten

- ERP-Datenbank (MySQL)
- Sync-Service (PHP Backend-Logik)
- Simulierte Shop-REST-API (PHP Endpoint)
- Logging-Komponente

### Systemfluss
ERP DB --> Sync Service --> REST API --> Shop-System

---

## Geplante Ordnerstruktur

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

### Tabelle: `inventory_system` (simuliertes ERP)

- `id` (INT, Primary Key)
- `sku` (VARCHAR)
- `name` (VARCHAR)
- `stock` (INT)
- `price` (DECIMAL)
- `last_updated` (TIMESTAMP)

---

## REST-API (simulierter Shop)

### Endpoint

```
POST /api/products
```

### JSON Payload

```json
{
  "sku": "PLANT-001",
  "name": "Lavandula angustifolia",
  "stock": 25,
  "price": 8.90
}
```

### Response Codes

 - **201 Created** – Neues Produkt angelegt
 - **200 OK** – Bestehendes Produkt aktualisiert
 - **400 Bad Request** – Validierungsfehler
 - **500 Internal Server Error** – Serverfehler

---

## Sync-Logic (High-Level)

Für jedes ERP-Produkt:

 1. JSON erzeugen
 2. API-Request via cURL senden
 3. Response prüfen
 4. Logging durchführen
 5. Statistik erfassen (created, updated, failed)

---

## Entwicklungsplan

### Tag 1 - Fundament & Sync

**Ziel:** Funktionierende DB --> REST Synchronisation
 - [ ] Projektstruktur anlegen
 - [ ] Datenbankschema definieren
 - [ ] Testdaten einfügen
 - [ ] PDO-Datenbankverbindung implementieren
 - [ ] Inventory Repository erstellen
 - [ ] API Endpoint (/api/products) erstellen
 - [ ] ApiClient (cURL rapper) implementieren
 - [ ] SyncService implementieren
 - [ ] Erste erfolgreiche Synchronisation testen

---

### Tag 2 - Robustheit & Professionalität

**Ziel:** Produktionsnahes Verhalten simulieren
 - [ ] Fehlerbehandlung verbessern
 - [ ] HTTP Statuscode Handeling sauber implementieren
 - [ ] cURL Requests
 - [ ] Trennung von Business-Logik und Infrastruktur
 - [ ] Logging & Fehlerbehandlung

---

## Dokumentation

Technische Diagramme befinden sich im docs/ -ordner

---

**Autor:** Palma Jacobs Perseus
**Zeitrahmen:** 2 Tage
**Status:** In Entwicklung
