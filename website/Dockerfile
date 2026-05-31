FROM php:8.2-apache

# Enable SQLite3
RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev && \
    docker-php-ext-install sqlite3 pdo_sqlite && \
    rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy website files
COPY . /var/www/html/

# Create data directory for SQLite database
RUN mkdir -p /var/www/html/data && chmod 777 /var/www/html/data

# Configure Apache to use PORT env variable
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT}/' /etc/apache2/sites-enabled/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html/data
