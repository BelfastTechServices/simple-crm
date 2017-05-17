simple-crm
============

# Server set up

sudo nano /etc/hostname; sudo nano /etc/hosts

sudo nano /etc/network/interfaces

sudo apt-get update; sudo apt-get dist-upgrade; sudo apt-get install apache2 php5 mysql-server phpmyadmin postfix git; sudo apt-get autoremove; sudo apt-get autoclean

sudo a2enmod ssl rewrite; sudo a2ensite default-ssl; sudo service apache2 restart

sudo nano /etc/apache2/sites-available/000-default.conf

```
<VirtualHost *:80>
    RewriteEngine on
    RewriteCond %{SERVER_PORT} !^443$
    RewriteRule ^/(.*) https://%{HTTP_HOST}/$1 [NC,R=301,L]
</VirtualHost>
```

# Setup - single domain/ssl site

nothing more to do

# Setup - multiple domains/ssl sites

sudo nano /etc/apache2/sites-available/simple-crm-ssl.conf

```
<IfModule mod_ssl.c>
    <VirtualHost *:443>
        ServerAdmin chris@belfasttechservices.co.uk
        DocumentRoot /var/www/simple-crm
        ServerName simple-crm.belfasttechservices.co.uk
        ServerAlias www.simple-crm.belfasttechservices.co.uk
        
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
        SSLEngine on
        
        SSLCertificateFile /etc/ssl/certs/ssl-cert-snakeoil.pem
        SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key
        #   SSLCertificateChainFile /etc/apache2/ssl.crt/server-ca.crt
        
        # modern configuration, tweak to your needs
        SSLProtocol             all -SSLv2 -SSLv3 -TLSv1
        SSLCipherSuite          ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:DHE-DSS-AES128-GCM-SHA256:kEDH+AESGCM:ECDHE-RSA-AES128-SHA256:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA:ECDHE-ECDSA-AES128-SHA:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA:ECDHE-ECDSA-AES256-SHA:DHE-RSA-AES128-SHA256:DHE-RSA-AES128-SHA:DHE-DSS-AES128-SHA256:DHE-RSA-AES256-SHA256:DHE-DSS-AES256-SHA:DHE-RSA-AES256-SHA:!aNULL:!eNULL:!EXPORT:!DES:!RC4:!3DES:!MD5:!PSK
        SSLHonorCipherOrder     on
        SSLCompression          off
        
        <FilesMatch "\.(cgi|shtml|phtml|php)$">
                SSLOptions +StdEnvVars
        </FilesMatch>
        <Directory /usr/lib/cgi-bin>
                SSLOptions +StdEnvVars
        </Directory>
        
        BrowserMatch "MSIE [2-6]" \
                nokeepalive ssl-unclean-shutdown \
                downgrade-1.0 force-response-1.0
        # MSIE 7 and newer should be able to use keepalive
        BrowserMatch "MSIE [17-9]" ssl-unclean-shutdown
    </VirtualHost>
</IfModule>

# vim: syntax=apache ts=4 sw=4 sts=4 sr noet

```

sudo a2ensite simple-crm-ssl; sudo service apache2 restart

cd; sudo add-apt-repository ppa:certbot/certbot; sudo apt-get update; sudo apt-get install python-certbot-apache

sudo certbot --apache -d simple-crm.belfasttechservices.co.uk -d www.simple-crm.belfasttechservices.co.uk --agree-tos --renew-by-default --no-redirect

#install

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

sudo rm -rf simple-crm /var/www/simple-crm

### https url

git clone https://github.com/chris18890/simple-crm.git

### git url

eval $(ssh-agent -s); ssh-add .ssh/github_rsa

git clone git@github.com:chris18890/simple-crm.git

cd simple-crm/crm; cp config.sample.inc.php config.inc.php; nano config.inc.php; cd

**if single site, remember to edit $config_base_path back to /crm/ **

sudo cp -r simple-crm/crm/ /var/www/simple-crm; sudo chown www-data -R /var/www
