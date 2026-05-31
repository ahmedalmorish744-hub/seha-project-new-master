FROM php:8.2-apache

# Enable SQLite3
RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev && \
    docker-php-ext-install sqlite3 pdo_sqlite && \
    rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy website files
COPY . /var/www/html/

# Create data directory for SQLite database and startup script
RUN mkdir -p /var/www/html/data && chmod 777 /var/www/html/data

# Set permissions
RUN chown -R www-data:www-data /var/www/html/data

# Create startup script that configures PORT at runtime
RUN echo '#!/bin/sh\nPORT_NUM=${PORT:-80}\necho "Starting Apache on port $PORT_NUM"\nsed -i "s/Listen 80/Listen $PORT_NUM/" /etc/apache2/ports.conf\nsed -i "s/:80/:$PORT_NUM/" /etc/apache2/sites-enabled/000-default.conf\napache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
