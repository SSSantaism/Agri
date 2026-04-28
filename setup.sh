#!/bin/bash
# Freshly - Quick Setup Script
# Run: sudo bash setup.sh

echo "🌿 Freshly - Setup Script"
echo "========================="

# 1. Start XAMPP services
echo "Starting XAMPP MySQL & Apache..."
/opt/lampp/lampp startmysql
/opt/lampp/lampp startapache

# Wait for MySQL to be ready
echo "Waiting for MySQL..."
sleep 3

# 2. Fix symlink - remove old dir if it's not a symlink
if [ -d /opt/lampp/htdocs/Agri ] && [ ! -L /opt/lampp/htdocs/Agri ]; then
    echo "Removing old Agri directory..."
    rm -rf /opt/lampp/htdocs/Agri
fi

if [ -L /opt/lampp/htdocs/Agri ]; then
    rm /opt/lampp/htdocs/Agri
fi

echo "Creating symlink..."
ln -sf /home/santaism/Documents/Agri /opt/lampp/htdocs/Agri

# Verify symlink
echo "Symlink: $(ls -la /opt/lampp/htdocs/Agri)"

# 3. Import database with proper charset
echo "Creating database and tables..."
/opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 < database/schema.sql

echo "Seeding initial data..."
/opt/lampp/bin/mysql -u root --default-character-set=utf8mb4 < database/seed.sql

# 4. Set upload directory permissions
chmod -R 755 uploads/

echo ""
echo "✅ Setup Complete!"
echo ""
echo "🌐 Open: http://localhost/Agri/"
echo ""
echo "📋 Default Credentials:"
echo "  Buyer:  buyer@freshly.com / password123"
echo "  Seller: seller@freshly.com / password123"
echo "  Admin:  admin@freshly.com / password123"
echo ""
