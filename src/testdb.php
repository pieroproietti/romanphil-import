<?php
$cnn = "mysql:host=mysql;dbname=wordpress";
$user = 'wordpress';
$pass = 'wordpress';
$table_prefix  = 'wp_';

try {
    $pdo = new PDO($cnn, $user, $pass);
    foreach($dbh->query('SELECT * from ') as $row) {
        print_r($row);
    }
    $dbh = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>
