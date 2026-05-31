FROM php:8.2-apache

RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev && \
    docker-php-ext-install pdo_sqlite && \
    rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY . /var/www/html/

RUN mkdir -p /var/www/html/data && chmod 777 /var/www/html/data
RUN chown -R www-data:www-data /var/www/html/data

RUN echo '#!/bin/sh\n\
PORT_NUM=${PORT:-80}\n\
echo "Starting Apache on port $PORT_NUM"\n\
sed -i "s/Listen 80/Listen $PORT_NUM/" /etc/apache2/ports.conf\n\
sed -i "s/:80/:$PORT_NUM/" /etc/apache2/sites-enabled/000-default.conf\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
