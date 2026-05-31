FROM php:8.2-apache

# Fix MPM conflict - disable mpm_event, keep mpm_prefork for PHP
RUN a2dismod mpm_event || true

# Install SQLite dependencies and PHP PDO SQLite extension
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Create data directory for SQLite database with proper permissions
RUN mkdir -p /var/www/html/data \
    && chmod 777 /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data

# Create startup script to handle Railway's dynamic PORT
RUN echo '#!/bin/sh\n\
set -e\n\
PORT_NUM=${PORT:-80}\n\
echo "Starting Apache on port $PORT_NUM"\n\
# Ensure only mpm_prefork is loaded (fix MPM conflict)\n\
a2dismod mpm_event 2>/dev/null || true\n\
a2enmod mpm_prefork 2>/dev/null || true\n\
# Update Apache port configuration\n\
sed -i "s/Listen 80/Listen $PORT_NUM/" /etc/apache2/ports.conf\n\
sed -i "s/:80/:$PORT_NUM/" /etc/apache2/sites-enabled/000-default.conf\n\
# Start Apache in foreground\n\
exec apache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
