#!/bin/bash
set -e

# Start services 
service mysql start
service apache2 start
# service php8.4-fpm start - check if needed - maybe handled by apache2

# initialize the database if variables exist
if [ -n "$DB_PASSWORD"]; then
    mysql -u "DB_USER" -p"DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
    mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < /docker-entrypoint-initdb.d/blogging_data.sql
fi

# keep container running 
tail -f /var/log/apache2/access.log