#!/bin/bash

# ============================================================
# Inventory Sync Service - Dual Dev Server Start
# ============================================================
# Startet zwei PHP Built-in Server (lokale Entwicklung):
#
#  - Port 8000: Sync Entry Point (public/sync.php)
#  - Port 8001: Shop API Endpoint (public/api/products.php)
#
# Hintergrund:
# Der PHP Built-in Server ist single-threaded. Würden Sync und API
# auf demselben Port laufen, blockiert sich sync.php selbst, sobald
# es per cURL die API aufruft (Timeout).
#
# Beenden: CTRL+C (stoppt beide Server)
# Logs:    /tmp/inventory-sync-sync.log und /tmp/inventory-sync-shop.log
# ============================================================

set -euo pipefail

SYNC_HOST="127.0.0.1"
SYNC_PORT="8000"
SHOP_HOST="127.0.0.1"
SHOP_PORT="8001"
DOC_ROOT="public"

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

SYNC_LOG="/tmp/inventory-sync-sync.log"
SHOP_LOG="/tmp/inventory-sync-shop.log"

cleanup() {
  echo ""
  echo "Stoppe Server..."
  if [[ -n "${SYNC_PID:-}" ]] && kill -0 "$SYNC_PID" 2>/dev/null; then
    kill "$SYNC_PID" || true
  fi
  if [[ -n "${SHOP_PID:-}" ]] && kill -0 "$SHOP_PID" 2>/dev/null; then
    kill "$SHOP_PID" || true
  fi
  echo "Beide Server gestoppt."
}

trap cleanup INT TERM

cd "$PROJECT_ROOT"

echo "Projekt: $PROJECT_ROOT"
echo ""
echo "Starte Sync Server  : http://${SYNC_HOST}:${SYNC_PORT}/sync.php"
php -S "${SYNC_HOST}:${SYNC_PORT}" -t "${DOC_ROOT}" >"$SYNC_LOG" 2>&1 &
SYNC_PID=$!

echo "Starte Shop API     : http://${SHOP_HOST}:${SHOP_PORT}/api/products.php"
php -S "${SHOP_HOST}:${SHOP_PORT}" -t "${DOC_ROOT}" >"$SHOP_LOG" 2>&1 &
SHOP_PID=$!

echo ""
echo "PIDs:"
echo "  Sync Server PID: ${SYNC_PID}"
echo "  Shop Server  PID: ${SHOP_PID}"
echo ""
echo "Logs:"
echo "  Sync: ${SYNC_LOG}"
echo "  Shop: ${SHOP_LOG}"
echo ""
echo "Beenden mit CTRL+C"
echo ""

# Warten, damit Script im Vordergrund bleibt (CTRL+C funktioniert)
wait