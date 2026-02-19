# Mini Inventory Sync Service (DB → REST)

## Projektziel

Dieses Projekt simuliert eine typische Integrationsaufgabe:

Ein Warenwirtschaftssystem (ERP) speichert Produktdaten in einer Datenbank.  
Ein E-Shop verwaltet seine Produkte über eine REST-API.

Ziel ist es, einen Sync-Service zu entwickeln, der:

- Produktdaten aus einer ERP-Datenbank liest
- diese als JSON an eine Shop-API sendet
- neue Produkte anlegt oder bestehende aktualisiert
- Änderungen protokolliert
- Fehlerbehandlung implementiert

Zeitbudget: **2 Tage**

---

## Motivation & Hintergrund

Viele Unternehmen nutzen:

- Ein externes Warenwirtschaftssystem
- Einen separaten Online-Shop

Die zentrale Herausforderung:
- Lagerbestand synchronisieren
- Preisänderungen übernehmen
- Neue Produkte anlegen
- Datenkonsistenz sicherstellen
- API-Fehler korrekt behandeln

Dieses Projekt simuliert diese Integration in vereinfachter Form.

Gleichzeitig dient es als:

- Demonstration meines technischen Verständnisses für Systemintegration
- Nachweis meiner Fähigkeit zur schnellen Einarbeitung in neue Projektkontexte
- Beispiel für saubere Backend-Architektur in reinem PHP
- Realistische 2-Tage-Implementierung

---

## Architekturüberblick

### Komponenten

1. ERP-Datenbank (MySQL)
2. Sync-Service (PHP Backend-Logik)
3. Simulierte Shop-REST-API (PHP Endpoint)
4. Logging-Komponente

---

## Systemdiagramm (konzeptionell)

ERP DB → Sync Service → REST API → Shop-System

---

## Geplante Ordnerstruktur


graph TD
    root["inventory-sync/"]
    root --> config["config/"]
    config --> db["database.php"]

    root --> src["src/"]
    src --> repo["Repository/"]
    repo --> inv["InventoryRepository.php"]
    src --> service["Service/"]
    service --> sync["SyncService.php"]
    src --> http["Http/"]
    http --> apiClient["ApiClient.php"]

    root --> public["public/"]
    public --> syncphp["sync.php"]
    public --> api["api/"]
    api --> products["products.php"]

    root --> logs["logs/"]
    logs --> logfile["sync.log"]

    root --> schema["schema.sql"]
    root --> readme["README.md"]

inventory-sync/

│

├── config/

│ └── database.php

│

├── src/

│ ├── Repository/

│ │ └── InventoryRepository.php

│ ├── Service/

│ │ └── SyncService.php

│ └── Http/

│ └── ApiClient.php

│
├── public/

│ ├── sync.php

│ └── api/

│ └── products.php

│

├── logs/

│ └── sync.log

│
├── schema.sql

└── README.md

---

## Datenbankschema

### inventory_system (simuliertes ERP)

- id (INT, PK)
- sku (VARCHAR)
- name (VARCHAR)
- stock (INT)
- price (DECIMAL)
- last_updated (TIMESTAMP)

---

## REST-API (simuliert Shop)

### Endpoint:
POST /api/products

### JSON Payload:
{
  "sku": "PLANT-001",
  "name": "Lavandula angustifolia",
  "stock": 25,
  "price": 8.90
}

### Response:
- 201 Created (neues Produkt)
- 200 OK (Produkt aktualisiert)
- 400 Bad Request
- 500 Internal Server Error

---

## Kernlogik des Sync-Services

Für jedes ERP-Produkt:

1. JSON erstellen
2. API-Request via cURL senden
3. Response prüfen
4. Logging durchführen
5. Statistik erfassen (created, updated, failed)

---

# Entwicklungsplan

## Tag 1 – Fundament & Sync

Ziel: Funktionierende DB → REST Synchronisation

- [ ] Projektstruktur anlegen
- [ ] Datenbankschema definieren
- [ ] Testdaten einfügen
- [ ] PDO-Datenbankverbindung implementieren
- [ ] InventoryRepository erstellen
- [ ] API Endpoint (/api/products) erstellen
- [ ] ApiClient (cURL Wrapper) implementieren
- [ ] SyncService implementieren
- [ ] Erste erfolgreiche Synchronisation testen

Ergebnis Tag 1:
→ Produkte werden erfolgreich per REST synchronisiert.

---

## Tag 2 – Robustheit & Professionalität

Ziel: Produktionsnahes Verhalten simulieren

- [ ] Fehlerbehandlung verbessern
- [ ] HTTP Statuscode Handling sauber implementieren
- [ ] Logging-Datei einbauen
- [ ] JSON-Statistik im Sync-Output
- [ ] Code-Refactoring
- [ ] README finalisieren
- [ ] Optional: einfache Auth-Simulation (API-Key)

Ergebnis Tag 2:
→ Sauber strukturiertes Integrations-Miniprojekt.

---

## Lernfokus

- PDO & Prepared Statements
- JSON Encoding / Decoding
- REST API Grundlagen
- HTTP Statuscodes
- cURL Requests
- Trennung von Business-Logik und Infrastruktur
- Logging & Fehlerbehandlung

---

Autor: Perseus Palma Jacobs  
Zeitrahmen: 2 Tage  
Status: In Entwicklung
