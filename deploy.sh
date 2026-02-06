#!/bin/bash

# Laravel Production Setup Script
# Run this script after uploading your files to the server

echo "🚀 Setting up Laravel for production..."

# Set proper permissions
echo "📁 Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 644 .env

# Clear any existing cache
echo "🗑️ Clearing development caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Cache configuration for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generate application key if not set
echo "🔑 Checking application key..."
php artisan key:generate --force

echo "✅ Laravel setup complete!"
echo "Your application should now be ready for production."

# Check if build assets exist
if [ -d "public/build" ]; then
    echo "✅ Production assets found in public/build/"
else
    echo "❌ Warning: No production assets found. Run 'npm run build' locally first."
fi

echo ""
echo "🔧 Next steps:"
echo "1. Update your .env file with production settings"
echo "2. Set APP_ENV=production and APP_DEBUG=false"
echo "3. Configure your database connection"
echo "4. Point your web server to the /public folder"