FROM ubuntu:24.04

RUN apt-get update && apt-get install -y \
	apache2 \
	mysql-server \
	php \
	php-mysql \
	libapache2-mod-php \
	&& apt-get clean

COPY . /var/www/html/

# copy database dump file into the container
COPY blogging_data.sql /docker-entrypoint-initdb.d/

# Use environment variables for database crendetials
ENV DB_PASS=DB_

# import the database
RUN service mysql start && \
	mysql -u user_two -p"claRioJan2k17!" -e "CREATE DATABASE myapp;" && \
	mysql -u user_two -p"claRioJan2k17!" myapp < /docker-entrypoint-initdb.d/blogging_data.sql	

EXPOSE 80

CMD service mysql start && service apache2 start && tail -f /var/log/apache2/access.log

# CMD ["/entrypoint.sh"]