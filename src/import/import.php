<?php
/*
MAIN per importazione

import->getCategories->getPages->getProducts

Testato con: wordpress 4.9.4 e woocommerce 3.2.6

ATTENZIONE: con woocommerce 3.3.3 NON carica le categorie

*/
ini_set("memory_limit", "32G");
set_include_path('/var/www/html/wordpress');

require('wp-blog-header.php');
require('simple_html_dom.php');
require '.auth.php';
require 'postSlug.php';
require 'categories.php';
require 'products.php';
require 'images.php';


$pdo = new PDO($cnn, $user, $pass);
$site='http://www.romanphil.com/';
//$html = file_get_html($site . 'Lista_Prodotti.asp?idCategoria=23&style=1&page=1#');
$html = file_get_html($site);

getCategories($html);
