<?php

// Seleziona tutti i post con "vaticano"
// Le modifiche sono arrivate a pagina 17

require '.auth.php';
ini_set("memory_limit", "32G");

global $pdo;
$pdo = new PDO($cnn, $user, $pass);
$tag2find="vaticano";

$sql="SELECT * FROM `wp_posts` where post_title like('%$tag2find%') and post_type='product' ";
$posts = $pdo->prepare($sql);
$posts->execute();

foreach ($posts as $post) {
    echo $post["post_title"] . "\n";
    $postId=$post['ID'];

    $sql="SELECT p.ID, t.term_id, t.name
          FROM wp_posts p
          INNER JOIN wp_term_relationships tr ON (p.ID=tr.object_id)
          INNER JOIN wp_term_taxonomy tt ON(tr.term_taxonomy_id=tt.term_taxonomy_id)
          INNER JOIN wp_terms t ON (t.term_id=tt.term_id)
          WHERE tt.taxonomy='product_tag' AND
                t.name like('%$tag2find%') AND
                p.ID=$postId";


    //echo $sql."\n\n";
    $tags = $pdo->prepare($sql);
    $tags->execute();
    $found=false;
    foreach ($tags as $tag) {
        $found=true;
    }

    echo "post_id: $postId!\n";
    if (!$found) {
        echo "categoria da inserire\n";
    } else {
        echo "OK\n";
    }
}

function
