FROM  php:8.1-fpm

ENV TIMEZONE Europe/Moscow

RUN apt-get update && apt-get install -y \
      apt-utils \
      nodejs npm \
      libpq-dev \
      libpng-dev \
	  libonig-dev \
	  libxml2-dev \
      libzip-dev \
      zip unzip \
	  curl \
	  default-mysql-client \
      git && \
      docker-php-ext-install pdo_mysql && \
	  docker-php-ext-install mysqli && \
	  docker-php-ext-install mbstring && \
	  docker-php-ext-install soap && \
      docker-php-ext-install bcmath && \
      docker-php-ext-install gd && \
      docker-php-ext-install zip && \
      apt-get clean && \
      rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
	  
RUN apt-get update; \
    apt-get install -y libmagickwand-dev; \
    pecl install imagick; \
    docker-php-ext-enable imagick; \
    true

COPY ./_docker/app/conf.d/php.ini /usr/local/etc/php/conf.d/php.ini

RUN curl -sL https://deb.nodesource.com/setup_19.x | bash - 
RUN apt-get install -y nodejs

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install TimeZone
RUN cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone
	
RUN printf '[PHP]\ndate.timezone = "Europe/Moscow"\n' > /usr/local/etc/php/conf.d/tzone.ini

# Set working directory
WORKDIR /var/www
RUN cd /var/www

RUN chown www-data:www-data /var/www/

ENTRYPOINT ["sh", "/var/www/entrypoint.sh"]
CMD ["php-fpm"]
