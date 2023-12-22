FROM php:alpine
COPY . /app
WORKDIR /app
CMD php public/index.php