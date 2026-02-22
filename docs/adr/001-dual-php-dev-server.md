# ADR 001: Einsatz von zwei PHP Built-in Servern für die lokale Entwicklung

## Status
Angenommen

## Kontext

Für die lokale Entwicklung wird der PHP Built-in Server (`php -S`) verwendet.

Im Rahmen der Implementierung des SyncService trat folgendes Problem auf:

- `public/sync.php` startet den Synchronisationsprozess.
- Innerhalb dieses Prozesses sendet der ApiClient per cURL einen HTTP-Request an `/api/products.php`.
- Beide Endpunkte werden vom gleichen PHP Built-in Server bereitgestellt.

Beobachtung:

Der HTTP-Request an die Shop-API lief in ein Timeout:

    curl_exec failed: Operation timed out

Ursache:

Der PHP Built-in Server ist single-threaded und verarbeitet immer nur eine Anfrage gleichzeitig.
Während `sync.php` ausgeführt wird, kann der Server keinen weiteren Request (z. B. `/api/products.php`) entgegennehmen.

Dadurch blockiert sich der Prozess selbst.


## Entscheidung

Für die lokale Entwicklung werden zwei separate PHP Built-in Server gestartet:

- Port 8000 → Sync Entry Point (`public/sync.php`)
- Port 8001 → Shop API (`public/api/products.php`)

Der ApiClient ruft die Shop API über folgende URL auf:

    http://127.0.0.1:8001/api/products.php


## Konsequenzen

Positive Auswirkungen:

- Keine gegenseitige Blockierung von Requests
- Realistischere Simulation einer verteilten Architektur
- Klare Trennung zwischen Sync-Service und Shop-API

Negative Auswirkungen:

- Zwei lokale Serverprozesse erforderlich
- Etwas erhöhter Setup-Aufwand


## Betrachtete Alternativen

1. Nutzung eines Routing-Skripts im Built-in Server  
   → löst das Problem nicht, da weiterhin single-threaded

2. Einsatz von Apache oder Nginx lokal  
   → technisch sauber, aber für ein 2-Tage-Demo-Projekt überdimensioniert

3. Verwendung von Docker-Containern  
   → strukturell sinnvoll, aber zusätzlicher Setup-Overhead


## Begründung

Der parallele Start von zwei Built-in Servern ist die einfachste und pragmatischste Lösung, um:

- die technische Einschränkung zu umgehen,
- die Architektur klar zu trennen,
- den Setup-Aufwand gering zu halten.

Diese Entscheidung gilt ausschließlich für die lokale Entwicklungsumgebung.
In einer produktiven Umgebung würde ein Multi-Process-Webserver eingesetzt werden.