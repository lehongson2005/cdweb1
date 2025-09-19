#!/bin/bash
# Cấp quyền cho mã nguồn mỗi khi container start
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

# Khởi động Apache
apache2-foreground
