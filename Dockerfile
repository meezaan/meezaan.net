FROM quay.io/vesica/php72:latest

# Copy files
RUN cd ../ && rm -rf /var/www/html
COPY . /var/www/


