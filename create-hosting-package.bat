@echo off
echo ============================================
echo   LARAVEL TO STATIC HTML HOSTING PACKAGE
echo ============================================
echo.

REM Check if we're in a Laravel project
if not exist "artisan" (
    echo Error: This doesn't appear to be a Laravel project root directory.
    echo Please run this script from your Laravel project root.
    pause
    exit /b 1
)

echo ✓ Laravel project detected
echo.

REM Create hosting package directory
echo 📦 Creating hosting package...
if exist "hosting-package" rmdir /s /q "hosting-package"
mkdir hosting-package
mkdir hosting-package\public_html
mkdir hosting-package\php-backend

echo.
echo 📁 Copying files for hosting...

REM Copy Laravel backend files (excluding public directory)
echo   ➤ Copying Laravel backend files...
robocopy . hosting-package\php-backend /E /XD public hosting-package node_modules .git vendor

REM Copy public assets
echo   ➤ Copying public assets...
robocopy public hosting-package\public_html /E

REM Copy static HTML version
echo   ➤ Copying static HTML homepage...
copy index-static.html hosting-package\public_html\index.html

REM Create simple PHP entry point for hosting
echo   ➤ Creating PHP entry point...
(
echo ^<?php
echo // Simple PHP entry point for shared hosting
echo // Point your domain's document root to this directory
echo.
echo // Check if Laravel backend exists
echo $laravel_path = __DIR__ . '/../php-backend';
echo.
echo if ^(file_exists^($laravel_path . '/vendor/autoload.php'^)^) {
echo     // Laravel is available - redirect to Laravel
echo     require $laravel_path . '/vendor/autoload.php';
echo     $app = require_once $laravel_path . '/bootstrap/app.php';
echo     $app-^>handleRequest^(Illuminate\Http\Request::capture^(^)^);
echo } else {
echo     // Fallback to static HTML
echo     if ^(file_exists^('index.html'^)^) {
echo         include 'index.html';
echo     } else {
echo         echo 'Welcome to Samuel Clinic - Please contact administrator';
echo     }
echo }
echo ?^>
) > hosting-package\public_html\index.php

REM Create .htaccess for clean URLs
echo   ➤ Creating .htaccess...
(
echo RewriteEngine On
echo.
echo # Handle Laravel routes
echo RewriteCond %%{REQUEST_FILENAME} !-f
echo RewriteCond %%{REQUEST_FILENAME} !-d
echo RewriteRule ^^(.*)$ index.php [QSA,L]
echo.
echo # Security headers
echo Header always set X-Content-Type-Options nosniff
echo Header always set X-Frame-Options DENY
echo Header always set X-XSS-Protection "1; mode=block"
echo.
echo # Cache static assets
echo ^<FilesMatch "\.(css|js|png|jpg|jpeg|gif|ico|svg)$"^>
echo     ExpiresActive On
echo     ExpiresDefault "access plus 1 month"
echo ^</FilesMatch^>
) > hosting-package\public_html\.htaccess

REM Create environment configuration for hosting
echo   ➤ Creating hosting environment config...
(
echo # Environment Configuration for Hosting
echo # Copy this content to .env in your php-backend directory
echo.
echo APP_NAME="Clinic System Demo CLINIC"
echo APP_ENV=production
echo APP_KEY=base64:a4l3aTAxagUzTdJOsIeUGunkLiE6YbINxmtAdjlNtF4=
echo APP_DEBUG=false
echo APP_URL=https://yourdomain.com
echo.
echo # Database - Update with your hosting database details
echo DB_CONNECTION=mysql
echo DB_HOST=localhost
echo DB_PORT=3306
echo DB_DATABASE=your_database_name
echo DB_USERNAME=your_database_username
echo DB_PASSWORD=your_database_password
echo.
echo # Mail Configuration
echo MAIL_MAILER=log
echo MAIL_HOST=127.0.0.1
echo MAIL_PORT=2525
echo.
echo # Session and Cache
echo SESSION_DRIVER=file
echo CACHE_STORE=file
echo.
echo # Security
echo SESSION_ENCRYPT=false
echo BCRYPT_ROUNDS=12
) > hosting-package\php-backend\env-template.txt

REM Create upload instructions
echo   ➤ Creating upload instructions...
(
echo ================================================
echo      HOSTING UPLOAD INSTRUCTIONS
echo ================================================
echo.
echo 📂 FOLDER STRUCTURE AFTER UPLOAD:
echo.
echo your-hosting-account/
echo ├── php-backend/              ^(Upload this folder to hosting root^)
echo │   ├── app/
echo │   ├── config/
echo │   ├── database/
echo │   ├── .env                  ^(Create from env-template.txt^)
echo │   └── ... ^(all Laravel files^)
echo └── public_html/              ^(Upload contents to web directory^)
echo     ├── index.html            ^(Static homepage^)
echo     ├── index.php             ^(PHP entry point^)
echo     ├── .htaccess
echo     ├── css/
echo     ├── js/
echo     ├── images/
echo     └── ... ^(all public assets^)
echo.
echo 🚀 UPLOAD STEPS:
echo.
echo 1. Upload 'php-backend' folder to your hosting root directory
echo    ^(Usually /home/username/ or similar^)
echo.
echo 2. Upload CONTENTS of 'public_html' to your web directory
echo    ^(Usually public_html, www, or htdocs^)
echo.
echo 3. Copy env-template.txt to .env in php-backend directory
echo    Update database settings in .env file
echo.
echo 4. If using shared hosting without Laravel support:
echo    - Rename index.html to index.php in web directory
echo    - Or keep index.html as main page ^(static version^)
echo.
echo 5. Set file permissions ^(if on Linux hosting^):
echo    chmod 755 directories
echo    chmod 644 files
echo    chmod 777 php-backend/storage/ ^(recursive^)
echo.
echo 💡 HOSTING OPTIONS:
echo.
echo A^) STATIC HTML ONLY ^(Works on any hosting^):
echo    - Only upload public_html contents
echo    - Rename index.html to index.php if needed
echo    - No database required
echo.
echo B^) FULL LARAVEL ^(Requires PHP 8.1+ hosting^):
echo    - Upload both folders as instructed
echo    - Configure database in .env
echo    - Run: composer install --no-dev
echo    - Run: php artisan migrate
echo.
echo C^) HYBRID MODE ^(Recommended^):
echo    - Upload both folders
echo    - Static homepage works immediately
echo    - Laravel backend available when configured
echo.
echo 📞 SUPPORT:
echo If hosting provider says "Node.js not supported":
echo - Use static HTML version ^(option A^)
echo - This package contains no Node.js dependencies
echo - Everything is converted to standard PHP/HTML
) > hosting-package\UPLOAD_INSTRUCTIONS.txt

echo.
echo 🎉 HOSTING PACKAGE CREATED SUCCESSFULLY!
echo.
echo 📁 Package location: hosting-package\
echo.
echo 📋 What's included:
echo    ✓ Static HTML homepage ^(works anywhere^)
echo    ✓ Laravel PHP backend ^(for full functionality^)
echo    ✓ Hosting-optimized .htaccess
echo    ✓ Upload instructions
echo    ✓ Environment template
echo.
echo 📖 Next: Read UPLOAD_INSTRUCTIONS.txt for deployment steps
echo.
pause