RewriteEngine On
RewriteRule ^rewrite-test$ test-rewrite.php [L]


# Public web server configuration for improved asset delivery
# Enables caching and compression for static assets served by Apache (XAMPP)

<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType text/javascript "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
    ExpiresByType application/x-javascript "access plus 1 week"
    ExpiresByType image/x-icon "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
    ExpiresByType application/font-woff "access plus 1 month"
    ExpiresByType application/font-woff2 "access plus 1 month"
    ExpiresByType application/vnd.ms-fontobject "access plus 1 month"
    ExpiresByType font/ttf "access plus 1 month"
    ExpiresByType font/otf "access plus 1 month"
</IfModule>

<IfModule mod_headers.c>
    # Encourage caching for static assets
    <FilesMatch "\.(css|js|png|jpg|jpeg|gif|webp|svg|woff|woff2|ttf|otf|eot)$">
        Header set Cache-Control "public, max-age=2592000, immutable"
    </FilesMatch>

    # Disable caching for dynamic PHP pages
    <FilesMatch "\.(php)$">
        Header set Cache-Control "no-cache, no-store, must-revalidate"
    </FilesMatch>
</IfModule>

<IfModule mod_deflate.c>
    # Compress common text-based assets
    AddOutputFilterByType DEFLATE text/plain text/html text/xml text/css application/xml application/xhtml+xml application/rss+xml application/javascript application/x-javascript application/json
</IfModule>

# Leverage ETags provided by Apache (do not disable here)
# Prevents directory listings
Options -Indexes

# Fallback to Laravel's index.php (should already be present)
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>