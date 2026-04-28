#!/bin/bash
# Freshly - Fix Script
# Run: sudo bash fix.sh

echo "🔧 Fixing Freshly setup..."

# 1. Fix symlink - remove old directory and create proper symlink
if [ -d /opt/lampp/htdocs/Agri ] && [ ! -L /opt/lampp/htdocs/Agri ]; then
    echo "Removing old Agri directory in htdocs..."
    rm -rf /opt/lampp/htdocs/Agri
fi

if [ -L /opt/lampp/htdocs/Agri ]; then
    rm /opt/lampp/htdocs/Agri
fi

echo "Creating symlink..."
ln -sf /home/santaism/Documents/Agri /opt/lampp/htdocs/Agri
ls -la /opt/lampp/htdocs/ | grep Agri

# 2. Fix database - drop and recreate with proper charset
echo "Fixing database with proper charset..."
/opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 < /home/santaism/Documents/Agri/database/schema.sql
/opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 freshly < /home/santaism/Documents/Agri/database/seed.sql

# 3. Set permissions
chmod -R 755 /home/santaism/Documents/Agri/uploads/

echo ""
echo "✅ Fix complete! Open: http://localhost/Agri/"
