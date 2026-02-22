#!/bin/bash

# ============================================
# Inventory Sync Service - Dev Server
# ============================================
# Startet den lokalen PHP Development Server
# mit public/ als Document Root.
#
# Nutzung:
# ./bin/start_server.sh
#
# Danach erreichbar unter:
# http://localhost:8000
# ============================================

HOST="localhost"
PORT="8000"
DOC_ROOT="public"

echo "Starte PHP Development Server..."
echo "Host: $HOST"
echo "Port: $PORT"
echo "Document Root: $DOC_ROOT"
echo ""
echo "Beenden mit CTRL+C"
echo ""

php -S ${HOST}:${PORT} -t ${DOC_ROOT}