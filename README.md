simple-crm web app
==============

# install

## set up MySQL database

```
sudo mysql -u root
CREATE DATABASE `simple-crm` CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
CREATE USER 'simple-crm'@'localhost' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON `simple-crm` . * TO 'simple-crm'@'localhost';
FLUSH PRIVILEGES;
quit
```

## git instructions

```
sudo rm -rf simple-crm /var/www/simple-crm
eval $(ssh-agent -s) && ssh-add ~/.ssh/github_ed25519
git clone git@github.com:BelfastTechServices/simple-crm.git
```

## Site setup

```
nano simple-crm/simple-crm-ssl.conf
sudo cp simple-crm/simple-crm-ssl.conf /etc/apache2/sites-available/simple-crm-ssl.conf && sudo a2ensite simple-crm-ssl
sudo apache2ctl configtest && sudo systemctl reload apache2
sudo certbot --apache -d simple-crm.belfasttechservices.co.uk -d www.simple-crm.belfasttechservices.co.uk --agree-tos --no-redirect
```

## Site deployment

```
cp simple-crm/crm/config.sample.inc.php simple-crm/crm/config.inc.php && nano simple-crm/crm/config.inc.php
sudo cp -r simple-crm/crm/ /var/www/simple-crm && sudo chown chris:www-data -R /var/www/simple-crm
sudo find /var/www/simple-crm -type d -exec chmod 2750 {} \;
sudo find /var/www/simple-crm -type f -exec chmod 640 {} \;
sudo find /var/www/simple-crm/files -type d -exec chmod 2770 {} \;
# app has no known runtime write dir; if it writes cache/uploads/logs under the tree, grant 2770 on that dir only:
# sudo chown -R www-data:www-data /var/www/simple-crm/<writable-dir>
# sudo find /var/www/simple-crm/<writable-dir> -type d -exec chmod 2770 {} \;
```
