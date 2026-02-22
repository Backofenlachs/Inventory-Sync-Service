#!/bin/bash
# ============================================================
# Inventory Sync Service - Database Setup Script (Ubuntu)
# ============================================================
# Dieses Skript erstellt die MySQL-Datenbank, einen Benutzer
# und die 'products'-Tabelle für das Projekt.
# Es kann auf jedem Ubuntu-Rechner einmalig ausgeführt werden.
#
# Vorraussetzungen:
#  - MySQL / MariaDB installiert und laufend
#  - Zugriff als root-Benutzer oder sudo
#  - script aus root verzeichniss ausführen
# ============================================================

DB_NAME="inventory_system"
DB_USER="inventory_user"
DB_PASS="securepassword"   # Hier bei Bedarf anpassen

# Schema SQL-Datei 
SCHEMA_FILE="schema.sql"

echo "==============================="
echo "Inventory Sync Service - Setup"
echo "==============================="

# Datenbank erstellen
echo "Erstelle Datenbank '$DB_NAME'..."
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# User erstllen und Berechtigungen vergeben
echo "Erstelle Benutzer '$DB_USER' und vergebe Rechte..."
# User erstellen & Berechtigungen vergeben
echo " Erstelle Benutzer '$DB_USER' und vergib Rechte..."
mysql -u root -p -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;"

# Tabelle erstellen
if [ -f "$SCHEMA_FILE" ]; then
    echo "Erstelle Tabelle 'products'..."
    mysql -u $DB_USER -p$DB_PASS $DB_NAME < "$SCHEMA_FILE"
else
    echo " Schema-Datei $SCHEMA_FILE nicht gefunden!"
    exit 1
fi

# Testprodukt einfügen (optional)
echo "Füge Testprodukt ein..."
mysql -u $DB_USER -p$DB_PASS $DB_NAME -e "
INSERT INTO products (external_id, name, sku, price, stock)
VALUES ('TEST-001','Test Produkt','TP-001',9.99,100);"

echo "Setup abgeschlossen!"
echo "Datenbank: $DB_NAME"
echo "User: $DB_USER"
echo "Testprodukt: TEST-001"

echo "Hinweis: Passwort in database.php auf $DB_PASS setzen!"