@echo off
ssh -i "C:\Users\hp\Downloads\RTLDEV (1).pem" ubuntu@51.21.252.67 "sudo chown -R www-data /var/www/html/revival_dev/public/build && sudo -u www-data php artisan -d /var/www/html/revival_dev view:clear"
