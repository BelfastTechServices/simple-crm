simple-crm web app
============

# Setup - multiple domains/ssl sites

```
sudo nano /etc/apache2/sites-available/simple-crm-ssl.conf; sudo a2ensite simple-crm-ssl; sudo service apache2 restart

cd; sudo add-apt-repository ppa:certbot/certbot; sudo apt-get update; sudo apt-get install python-certbot-apache

sudo certbot --apache -d simple-crm.belfasttechservices.co.uk -d www.simple-crm.belfasttechservices.co.uk --agree-tos --renew-by-default --no-redirect
```

# install

## set up MySQL database

```
mysql -u root -p

CREATE DATABASE `simple-crm` CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'simple-crm'@'localhost' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON `simple-crm` . * TO 'simple-crm'@'localhost';

FLUSH PRIVILEGES;

quit
```

## git instructions

```
sudo rm -rf simple-crm /var/www/simple-crm
```

### https url

```
git clone https://github.com/chris18890/simple-crm.git
```

### git url

```
eval $(ssh-agent -s); ssh-add .ssh/github_rsa

git clone git@github.com:chris18890/simple-crm.git

cd simple-crm/crm; cp config.sample.inc.php config.inc.php; nano config.inc.php; cd
```

**if single site, remember to edit $config_base_path back to /crm/**

```
sudo cp -r simple-crm/crm/ /var/www/simple-crm; sudo chown www-data -R /var/www
```
