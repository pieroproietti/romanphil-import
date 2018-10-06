# romanphil
Sito di ecommerce realizzato in wordpress, in versione docker.

# Attenzione
Non viene salvato su git l'intero sito, ma solo la customizzazione.

Il database, viene creato nella cartella ./volumes/db, mentre il sorgente
directory ./srv viene impostato come /var/www/html dei container.

## docker
* ``` ./bin/dup  --build ``` docker-compose up -d --build
* ``` ./bin/ddown``` docker-compose down
* ``` ./bin/dexec``` docker exec -it [mysql|php|ngnix] bash

I container sono quattro:
* mysql
+ php
* ngnix
* phpmyadmin

Account
* user git thesi: pieroproietti

# Versioni wp da utilizzare per l'importazione
* [WordPress 4.7.9](https://woradpress.org/wordpress-4.7.9.zip)
* [woocommerce  3.2.1](https://github.com/woocommerce/woocommerce/archive/3.2.1.zip)


## Database
* dbname: wordress
* user: wordpress
* pass: wordpress

## Nella macchina con php
chown -Rf www-data:www-data /var/www/html/


## ngnix

```
location /wordpress/{
  # permalink
  autoindex on;
  try_files $uri $uri/ /wordpress/index.php?$args;
}
```

# Importazione
* git clone http://github.com/pieroproietti/import-romanphil in /var/www/html/WordPress
* scompattare wordpress in ./srv
* scompattare woocommerce-3.2.6 in ./srv/wordpress/wp-content/plugin
* attivare woocommerce dal browsers
* collegarsi alla macchina php e dalla directory /var/www/html
* php import/import.php

## Plugin and themes
Installare:
* theme: storefront
* plugin: WooCommerce collapsing categories
* widget: wc-categories

## Prodotti importati: 4144
