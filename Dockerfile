# Use official PHP image with CLI
FROM php:8.2-cli

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Set working directory
WORKDIR /var/www/html

# Copy project files into container
COPY . .

# Expose port 10000 for Render
EXPOSE 10000

# Start PHP's built-in server
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
