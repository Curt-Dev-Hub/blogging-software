FROM ubuntu:24.04

RUN apt-get update && apt-get install -y \
    apache2 \
    mysql-server \
    php \
    php-mysql \
    libapache2-mod-php \
    && apt-get clean

COPY . /var/www/html/

# Copy database dump (without credentials)
COPY blogging_data.sql /docker-entrypoint-initdb.d/

# Use environment variables for credentials
ENV DB_USER=dev_user \
    DB_NAME=myapp

# Runtime script will handle DB init
COPY docker-entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]