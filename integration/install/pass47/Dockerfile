# Disposable local acceptance environment; not a production Docker image.
FROM php:8.2-apache
RUN apt-get update && apt-get install -y --no-install-recommends \
    libcurl4-openssl-dev libicu-dev libzip-dev libpng-dev libjpeg62-turbo-dev \
    libfreetype6-dev libonig-dev libxml2-dev unzip \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j2 pdo_mysql mysqli curl mbstring bcmath intl gd zip exif \
 && a2enmod rewrite headers \
 && rm -rf /var/lib/apt/lists/*
RUN printf '%s\n' '<Directory /var/www/html>' 'AllowOverride All' 'Require all granted' '</Directory>' > /etc/apache2/conf-available/plusagency.conf \
 && a2enconf plusagency
COPY entrypoint.sh /usr/local/bin/pass47-start
RUN chmod 0755 /usr/local/bin/pass47-start
ENTRYPOINT ["/usr/local/bin/pass47-start"]
CMD ["apache2-foreground"]
