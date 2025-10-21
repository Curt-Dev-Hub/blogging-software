#!/bin/bash
set -e

# Wait for MySQL to be ready (needed if your app starts faster than MySQL)
echo "Waiting for database to become available..."
while ! mysqladmin ping -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" --silent; do
    sleep 1
done

# Initialize database if variables exist
if [ -n "$DB_PASSWORD" ]; then
    echo "Checking database $DB_NAME..."

    # Create database if none exists
    if ! mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" -e "USE $DB_NAME" 2>/dev/null; then
        echo "Creating database $DB_NAME..."
        mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" -e "CREATE DATABASE $DB_NAME;"
    fi

    # Import SQL file if exists
    if [ -f "/docker-entrypoint-initdb.d/blogging_data.sql" ]; then
        echo "Importing initial data..."
        mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < /docker-entrypoint-initdb.d/blogging_data.sql
    fi
fi

    # mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
    
    # if [ -f "/docker-entrypoint-initdb.d/blogging_data.sql" ]; then
      #   mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < /docker-entrypoint-initdb.d/blogging_data.sql
    # fi
# fi

# Execute the main command (Apache in this case)
exec "$@"